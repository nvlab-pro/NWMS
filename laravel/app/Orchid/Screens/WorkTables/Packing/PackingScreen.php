<?php

namespace App\Orchid\Screens\WorkTables\Packing;

use App\Models\rwBarcode;
use App\Models\rwOrder;
use App\Models\rwOrderOffer;
use App\Models\rwOrderPacking;
use App\Models\rwPlace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Screen;

class PackingScreen extends Screen
{
    public function screenBaseView(): string
    {
        return 'loyouts.base';

    }

    protected $orderId, $tableId, $queueId;

    public function query($queueId, $tableId, $orderId, Request $request): iterable
    {

        $currentUser = Auth::user();
        $this->orderId = $orderId;
        $arOffersList = [];
        $arPackedOffersList = [];

        isset($request->barcode) ? $barcode = $request->barcode : $barcode = null;
        isset($request->cash) ? $cash = $request->cash : $cash = null;

        $dbCurrentPlace = rwPlace::find($tableId);
        $this->tableId = $dbCurrentPlace->pl_shelf;
        $this->queueId = $queueId;

        // Получаем данные по заказу товарам
        $dbOrder = rwOrder::where('o_id', $orderId)
            ->with('getWarehouse')
            ->first();

        // Сохраняем отсканированный товар
        if ($barcode) {

            $selectOffer = null;
            $dbBarcodes = rwBarcode::where('br_barcode', $barcode)
                ->where('br_shop_id', $dbOrder->o_shop_id)
                ->get();

            foreach ($dbBarcodes as $currentBarcode) {

                $dbCurrentOffer = rwOrderOffer::where('oo_order_id', $orderId)
                    ->where('oo_offer_id', $currentBarcode->br_offer_id)
                    ->first();

                if ($dbCurrentOffer) {
                    $selectOffer = $dbCurrentOffer;
                    break;
                }
            }

            // если товар с таким баркодом в заказе есть
            if ($selectOffer) {

                $currentPackingOffer = rwOrderPacking::where('op_id', $selectOffer->oo_id)->first();

                if ($currentPackingOffer) {

                    if ($currentPackingOffer->op_cash != $cash) {

                        rwOrderPacking::where('op_id', $selectOffer->oo_id)->update([
                            'op_qty' => $currentPackingOffer->op_qty + 1,
                            'op_cash' => $cash,
                        ]);

                        Alert::success(__('Товар добавлен в заказ!'));

                    }

                } else {

                    rwOrderPacking::create([
                        'op_id' => $selectOffer->oo_id,
                        'op_order_id' => $orderId,
                        'op_offer_id' => $selectOffer->oo_offer_id,
                        'op_user_id' => $currentUser->id,
                        'op_barcode' => $barcode,
                        'op_data' => date('Y-m-d H:i:s', time()),
                        'op_cash' => $cash,
                        'op_qty' => 1,
                    ]);

                    Alert::success(__('Товар добавлен в заказ!'));

                }


            }

        }

        $dbOffersList = rwOrderOffer::where('oo_order_id', $orderId)
            ->with('getOffer')
            ->with('getPackingOffer')
            ->get();

        // Формируем список товаров
        foreach ($dbOffersList as $dbOffer) {

            $packedQty = 0;
            $currentCash = 0;
            if (isset($dbOffer->getPackingOffer->op_qty) && $dbOffer->getPackingOffer->op_qty > 0) {
                $packedQty = $dbOffer->getPackingOffer->op_qty;
                $currentCash = $dbOffer->getPackingOffer->op_cash;
            }

            if ($packedQty == $dbOffer->oo_qty) {
                $arPackedOffersList[$dbOffer->oo_id] = [
                    'oo_id' => $dbOffer->oo_id,
                    'oo_offer_id' => $dbOffer->oo_offer_id,
                    'of_name' => $dbOffer->getOffer->of_name,
                    'of_article' => $dbOffer->getOffer->of_article,
                    'of_img' => $dbOffer->getOffer->of_img,
                    'of_datamarix' => $dbOffer->getOffer->of_datamarix,
                    'oo_qty' => $dbOffer->oo_qty,
                    'op_cash' => $currentCash,
                    'packed_qty' => $packedQty,
                ];
            } else {
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
                ];
            }
        }

        uasort($arOffersList, function ($a, $b)
        {
            return $b['op_cash'] <=> $a['op_cash'];
        });

        return [
            'dbOrder' => $dbOrder,
            'arOffersList' => $arOffersList,
            'arPackedOffersList' => $arPackedOffersList,
            'dbCurrentPlace' => $dbCurrentPlace,
            'queueId' => $queueId,
            'tableId' => $tableId,
        ];
    }


    public function name(): ?string
    {
        return __('Упаковка заказа') . ' № ' . $this->orderId;
    }

    public function description(): ?string
    {
        return __('Стол упаковки') . ' № ' . $this->tableId . ', ' . __('очередь упаковки') . ' № ' . $this->queueId;
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
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
            Layout::view('Screens.WorkTables.Packing.ScanInput'),
            Layout::view('Screens.WorkTables.Packing.PackingOffersList'),
        ];
    }
}
