<?php

namespace App\Orchid\Screens\terminal\SOAM;

use App\Console\scheduleOrders;
use App\Models\rwBarcode;
use App\Models\rwOffer;
use App\Models\rwOrder;
use App\Models\rwOrderAssembly;
use App\Models\rwOrderOffer;
use App\Models\rwPlace;
use App\Models\rwWarehouse;
use App\Models\WhcRest;
use App\Orchid\Services\OrderService;
use App\Orchid\Services\SOAService;
use App\Services\CustomTranslator;
use App\WhPlaces\WhPlaces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

// Определяем текущее положение человека на складе
class SelectOrderSOAMScreen extends Screen
{
    private $orderId;

    public function screenBaseView(): string
    {
        return 'loyouts.base';

    }

    public function query($soaId, $orderId, Request $request): iterable
    {
        $validatedData = $request->validate([
            'barcode' => 'nullable|string',
            'action'  => 'nullable|string',
            'count'   => 'nullable|string',
            'cash'   => 'nullable|string',
            'currentPlace'   => 'nullable|string',
            'orderOfferId'   => 'nullable|string',
            'offerId'   => 'nullable|string',
        ]);

        $currentUser = Auth::user();

        $currentWeight = $placeId = 0;
        $arOffersList = $arPlacesList = $currentPlace = $currentOffer = $arBarcodes = [];

        isset($validatedData['barcode']) ? $barcode = $validatedData['barcode'] : $barcode = '';
        isset($validatedData['action']) ? $action = $validatedData['action'] : $action = '';
        isset($validatedData['count']) ? $count = $validatedData['count'] : $count = 0;
        isset($validatedData['cash']) ? $cash = $validatedData['cash'] : $cash = 0;
        isset($validatedData['currentPlace']) ? $currentPlace = $validatedData['currentPlace'] : $currentPlace = 0;
        isset($validatedData['orderOfferId']) ? $orderOfferId = $validatedData['orderOfferId'] : $orderOfferId = 0;
        isset($validatedData['offerId']) ? $offerId = $validatedData['offerId'] : $offerId = 0;

        // Получаем заказ для сборки
        if ($orderId == 0) {
            $dbOrder = SOAService::getFirstOrder($soaId, $currentUser->id);
        } else {
            $dbOrder = rwOrder::find($orderId);
        }

        // Если заказ есть
        if ($dbOrder) {

            // Переводим заказ в "собирается"
            if ($dbOrder->o_status_id == 40) {

                $dbOrder->o_status_id = 50;
                $dbOrder->o_operation_user_id = $currentUser->id;
                $dbOrder->save();

                $dbOrderOffers = rwOrderOffer::where('oo_order_id', $dbOrder->o_id)
                    ->update([
                        'oo_operation_user_id' => 0,
                    ]);

            }

            // Сохраняем подобранный товар
            if ($action == 'saveOffer' && $count > 0) {

                $dbOrderAssembly = rwOrderAssembly::where('oa_order_id', $dbOrder->o_id)
                    ->where('oa_offer_id', $offerId)
                    ->where('oa_place_id', $currentPlace)
                    ->where('oa_qty', '>', 0)
                    ->first();

                if ($dbOrderAssembly) {

                    if ($dbOrderAssembly->oa_cash != $cash) {

                        dump($dbOrderAssembly->oa_id);

                        $dbOrderAssembly = rwOrderAssembly::find($dbOrderAssembly->oa_id);
                        $dbOrderAssembly->oa_qty += $count;
                        $dbOrderAssembly->save();

                        Alert::success(CustomTranslator::get('Товар подобран, выберите следующий!'));

                        // Пересчитываем остатки
                        $serviceOrder = new OrderService($dbOrder->o_id);
                        $serviceOrder->resaveOrderRests();

                    }

                } else {

                    rwOrderAssembly::create([
                        'oa_order_id' => $dbOrder->o_id,
                        'oa_user_id'  => $currentUser->id,
                        'oa_offer_id' => $orderOfferId,
                        'oa_place_id' => $currentPlace,
                        'oa_data'     => date('Y-m-d H:i:s'),
                        'oa_qty'      => $count,
                        'oa_cash'     => $cash,
                    ]);

                    Alert::success(CustomTranslator::get('Товар подобран, выберите следующий!'));

                    // Пересчитываем остатки
                    $serviceOrder = new OrderService($dbOrder->o_id);
                    $serviceOrder->resaveOrderRests();

                }


                $action = '';

            }

            //  Отсканирован баркод места хранения
            if ($barcode != '') {

                // Получаем parentWhId
                $dbParentWh = rwWarehouse::find($dbOrder->o_wh_id);
                if (isset($dbParentWh) &&  $dbParentWh->wh_parent_id > 0) {

                    $parentWhId = $dbParentWh->wh_parent_id;
                    $selectedPlace = new WhPlaces($barcode, $parentWhId);
//                    $currentWeight = $selectedPlace->getPlaceWeight();
                    $placeId = $selectedPlace->getPlaceId();
                    $action='badBarcode';

                }

            }

            //Получаем список товаров
            $dbOrderOffers = rwOrderOffer::where('oo_order_id', $dbOrder->o_id)
                ->with('getOffer')
                ->get();

            foreach ($dbOrderOffers as $dbOrderOffer) {

                $dbPlaces = WhcRest::where('whcr_offer_id', $dbOrderOffer->getOffer->of_id)
                    ->where('whcr_wh_id', $dbOrder->o_wh_id)
                    ->where('whcr_count', '>', 0)
                    ->with('getPlace')
                    ->orderBy('whcr_count', 'DESC')
                    ->get();

                $tmpShowOffer = 0;

                foreach ($dbPlaces as $dbPlace) {

                    if ($dbPlace->whcr_place_id > 0) {

                        $arPlacesList[$dbOrderOffer->getOffer->of_id][] = [
                            'pl_id' => $dbPlace->getPlace->pl_id,
                            'pl_room' => $dbPlace->getPlace->pl_room,
                            'pl_floor' => $dbPlace->getPlace->pl_floor,
                            'pl_section' => $dbPlace->getPlace->pl_section,
                            'pl_row' => $dbPlace->getPlace->pl_row,
                            'pl_rack' => $dbPlace->getPlace->pl_rack,
                            'pl_shelf' => $dbPlace->getPlace->pl_shelf,
                            'pl_place_weight' => $dbPlace->getPlace->pl_place_weight,
                            'whcr_count' => $dbPlace->whcr_count,
                        ];

                        if ($dbPlace->getPlace->pl_id == $placeId) {
                            $tmpShowOffer = 1;
                            $action='findOffers';
                            $currentPlace = [
                                'pl_id' => $dbPlace->getPlace->pl_id,
                                'pl_room' => $dbPlace->getPlace->pl_room,
                                'pl_floor' => $dbPlace->getPlace->pl_floor,
                                'pl_section' => $dbPlace->getPlace->pl_section,
                                'pl_row' => $dbPlace->getPlace->pl_row,
                                'pl_rack' => $dbPlace->getPlace->pl_rack,
                                'pl_shelf' => $dbPlace->getPlace->pl_shelf,
                                'pl_place_weight' => $dbPlace->getPlace->pl_place_weight,
                                'whcr_count' => $dbPlace->whcr_count,
                            ];
                        }

                    }

                }

                $currentQty = $dbOrderOffer->oo_qty;
                $sendQty = rwOrderAssembly::where('oa_order_id', $dbOrder->o_id)
                    ->where('oa_offer_id', $dbOrderOffer->getOffer->of_id)
                    ->where('oa_qty', '>', 0)
                    ->sum('oa_qty');

                $currentQty -= $sendQty;

                if ($currentQty > 0) {

                    $arOffersList[$dbOrderOffer->oo_id] = [
                        'offerId' => $dbOrderOffer->getOffer->of_id,
                        'offerDocId' => $dbOrderOffer->oo_id,
                        'offerName' => $dbOrderOffer->getOffer->of_name,
                        'offerArt' => $dbOrderOffer->getOffer->of_article,
                        'offerImg' => $dbOrderOffer->getOffer->of_img,
                        'offerQty' => $currentQty,
                        'offerShow' => $tmpShowOffer,
                    ];

                }

                // Нашел товар, сохраняю его
                if ($tmpShowOffer == 1 && isset($arOffersList[$dbOrderOffer->oo_id])) {

                    $currentOffer = $arOffersList[$dbOrderOffer->oo_id];
                    $arBarcodes = rwBarcode::where('br_offer_id', $dbOrderOffer->oo_offer_id)
                        ->pluck('br_barcode')
                        ->toArray();

//                    break;
                }

            }

            $this->orderId = $dbOrder->o_id;
        }

        return [
            'soaId'             => $soaId,
            'dbOrder'           => $dbOrder,
            'arOffersList'      => $arOffersList,
            'arPlacesList'      => $arPlacesList,
            'action'            => $action,
            'selectedPlaceId'   => $placeId,
            'currentOffer'      => $currentOffer,
            'currentPlace'      => $currentPlace,
            'arBarcodes'        => $arBarcodes,
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Сборка заказа') . ' ' . $this->orderId;
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        return [
            Layout::view('Screens.Terminal.SOAM.SelectOrderSOAM'),
        ];
    }

}
