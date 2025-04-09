<?php

namespace App\Orchid\Screens\WorkTables\Packing;

use App\Models\rwBarcode;
use App\Models\rwOrder;
use App\Models\rwOrderAssembly;
use App\Models\rwOrderOffer;
use App\Models\rwOrderPacking;
use App\Models\rwPlace;
use App\Models\rwSettingsProcPacking;
use App\Models\WhcRest;
use App\Services\CustomTranslator;
use App\WhCore\WhCore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class PrintAssemblingList extends Screen
{
    public function screenBaseView(): string
    {
        return 'loyouts.base';

    }

    protected $orderId, $tableId, $queueId, $queueStartPlaceType;

    public function query($queueId, $tableId, $orderId, $action, Request $request): iterable
    {

        $currentUser = Auth::user();
        $this->orderId = $orderId;

        $dbCurrentPlace = rwPlace::find($tableId);
        $this->tableId = $dbCurrentPlace->pl_shelf;
        $this->queueId = $queueId;

        // **************************************
        // Получаем настройки очереди
        $currentQueue = rwSettingsProcPacking::where('spp_id', $queueId)
            ->first();

        if ($currentQueue) {

            // Получаем данные очереди
            $queueWhId = $currentQueue->spp_wh_id;
            $queueStartPlaceType = $currentQueue->spp_start_place_type; // 102 - подбор с полки, 104 -  Полка сортировки, 105 - Стол упаковки,
            $queueRackFrom = $currentQueue->spp_place_rack_from;
            $queueRackTo = $currentQueue->spp_place_rack_to;
            $queuePackType = $currentQueue->spp_packing_type;           // 0 - Скан артикула (под пересчет), 1 - Скан каждого товара, 2 - Со сканом честного знака

            $this->queueStartPlaceType = $queueStartPlaceType;

        }

        // **************************************
        // Получаем данные по заказу товарам
        $dbOrder = rwOrder::where('o_id', $orderId)
            ->with('getWarehouse')
            ->first();

        // **************************************
        // Формируем список товаров для вывода

        $dbOffersList = rwOrderOffer::where('oo_order_id', $orderId)
            ->with('getOffer')
            ->with('getPackingOffer')
            ->get();

        foreach ($dbOffersList as $dbOffer) {

            $packedQty = 0;
            $currentCash = 0;
            if (isset($dbOffer->getPackingOffer->op_qty) && $dbOffer->getPackingOffer->op_qty > 0) {
                $packedQty = $dbOffer->getPackingOffer->op_qty;
                $currentCash = $dbOffer->getPackingOffer->op_cash;
            }

            // Еще не собранный товары

            // Если товар лежит на полке, собираем все его места хранения
            $arPlacesList = [];

            $dbPlaces = WhcRest::where('whcr_offer_id', $dbOffer->oo_offer_id)
                ->where('whcr_wh_id', $dbOrder->o_wh_id)
                ->with('getPlace')
                ->orderBy('whcr_count', 'DESC')
                ->get();

            foreach ($dbPlaces as $dbPlace) {

                if (isset($dbPlace->getPlace->pl_id)) {

                    $placeCount = $dbPlace->whcr_count;

                    $pickedCount = 0;
                    $dbOA = rwOrderAssembly::where('oa_order_id', $orderId)
                        ->where('oa_offer_id', $dbOffer->oo_offer_id)
                        ->where('oa_place_id', $dbPlace->getPlace->pl_id)
                        ->first();

                    if (isset($dbOA->oa_id)) $pickedCount = $dbOA->oa_qty;

                    $placeCount += $pickedCount;

                    if ($placeCount >= 0 && !($placeCount == 0 && $pickedCount == 0)) {

                        $arPlacesList[] = [
                            'pl_id' => $dbPlace->getPlace->pl_id,
                            'pl_room' => $dbPlace->getPlace->pl_room,
                            'pl_floor' => $dbPlace->getPlace->pl_floor,
                            'pl_section' => $dbPlace->getPlace->pl_section,
                            'pl_row' => $dbPlace->getPlace->pl_row,
                            'pl_rack' => $dbPlace->getPlace->pl_rack,
                            'pl_shelf' => $dbPlace->getPlace->pl_shelf,
                            'pl_place_weight' => $dbPlace->getPlace->pl_place_weight,
                            'whcr_count' => $placeCount,
                            'picked_count' => $pickedCount,
                        ];

                    }

                }

            }

            $arOffersList[$dbOffer->oo_id] = [
                'oo_id' => $dbOffer->oo_id,
                'oo_offer_id' => $dbOffer->oo_offer_id,
                'of_name' => $dbOffer->getOffer->of_name,
                'of_article' => $dbOffer->getOffer->of_article,
                'of_img' => $dbOffer->getOffer->of_img,
                'of_datamarix' => $dbOffer->getOffer->of_datamarix,
                'oo_qty' => $dbOffer->oo_qty,
                'op_cash' => $currentCash,
                'packed_qty' => $packedQty,
                'offerPlaces' => $arPlacesList,
            ];

        }

        uasort($arOffersList, function ($a, $b) {
            return $b['op_cash'] <=> $a['op_cash'];
        });

        return [
            'queueId' => $queueId,
            'tableId' => $tableId,
            'orderId' => $this->orderId,
            'arOffersList' => $arOffersList,
            'action' => $action,
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Упаковка заказа') . ' № ' . $this->orderId . ' ⇒ ' . CustomTranslator::get('Печать упаковочного листа');
    }

    public function description(): ?string
    {
        return CustomTranslator::get('Стол упаковки') . ' № ' . $this->tableId . ', ' . CustomTranslator::get('очередь упаковки') . ' № ' . $this->queueId;
    }

    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::view('Screens.WorkTables.Packing.PrintAssemblingList'),
        ];
    }

    public function saveDocument(Request $request) {

        $queueId = $request->queueId;
        $tableId = $request->tableId;
        $orderId = $request->orderId;
        $arOffersLost = $request->offerCount;
        $currentUser = Auth::user();
        $whId = 0;

        $dbOrder = rwOrder::where('o_id', $orderId)->first();

        if (isset($dbOrder->o_wh_id) && $dbOrder->o_wh_id > 0) $whId = $dbOrder->o_wh_id;

        $currentCore = new WhCore($whId);

        rwOrderAssembly::where('oa_order_id', $orderId)->update([
            'oa_cash'  => 0,
        ]);

        foreach ($arOffersLost as $offerId => $currentOffers) {
            foreach ($currentOffers as $placeId => $count) {
                if ($count > 0) {

                    $dbOA = rwOrderAssembly::where('oa_order_id', $orderId)
                        ->where('oa_offer_id', $offerId)
                        ->where('oa_place_id', $placeId)
                        ->first();

                    if (!$dbOA) {
                        $dbOA = new rwOrderAssembly();
                        $dbOA->oa_order_id = $orderId;
                        $dbOA->oa_offer_id = $offerId;
                        $dbOA->oa_user_id = $currentUser->id;
                        $dbOA->oa_place_id = $placeId;
                    }

                    $dbOA->oa_qty = $count;
                    $dbOA->oa_data = date('Y-m-d H:i:s');
                    $dbOA->oa_cash = time();
                    $dbOA->save();


                }
            }

        }

        rwOrderAssembly::where('oa_order_id', $orderId)
            ->where('oa_cash', 0)
            ->delete();

        $currentCore->reservAssembledOrder($orderId);

        Alert::success(CustomTranslator::get('Данные сохранены'));

        return redirect()->route('platform.tables.packing.assembling.print', [$queueId, $tableId, $orderId, 1]);
    }
}
