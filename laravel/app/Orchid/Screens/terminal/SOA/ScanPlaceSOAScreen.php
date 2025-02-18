<?php

namespace App\Orchid\Screens\terminal\SOA;

use App\Models\rwOrder;
use App\Models\rwOrderOffer;
use App\Models\rwPlace;
use App\Models\rwWarehouse;
use App\Orchid\Services\SOAService;
use App\WhPlaces\WhPlaces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class ScanPlaceSOAScreen extends Screen
{
    private $orderId;

    public function screenBaseView(): string
    {
        return 'loyouts.base';

    }

    public function query($soaId, $orderId, Request $request): iterable
    {

        $validatedData = $request->validate([
            'action' => 'nullable|string',
            'barcode' => 'nullable|string',
        ]);

        $offerId = 0;
        $orderOfferId = 0;
        $nextPlaceId = 0;
        $parentWhId = 0;
        $plWeight = 9999999999;
        $arPlace = [];

        $currentUser = Auth::user();
        isset($validatedData['barcode']) ? $barcode = $validatedData['barcode'] : $barcode = null;

        // Получаем заказ для сборки
        if ($orderId == 0) {
            $dbOrder = SOAService::getFirstOrder($soaId, $currentUser->id);
        } else {
            $dbOrder = rwOrder::find($orderId);
        }

        // Если заказ есть
        if ($dbOrder) {

            // Получаем parentWhId
            $dbParentWh = rwWarehouse::find($dbOrder->o_wh_id);
            if (isset($dbParentWh) &&  $dbParentWh->wh_parent_id > 0) {

                $parentWhId = $dbParentWh->wh_parent_id;

            }

            // Получаем вес текущего места нахождения кладовщика
            $tmpPlaceId = new WhPlaces($barcode, $parentWhId);
            $currentWeight = $tmpPlaceId->getPlaceWeight();

            // Получаем список товаров
            $dbOrderOffers = rwOrderOffer::where('oo_order_id', $dbOrder->o_id)
                ->where('oo_operation_user_id', 0)
                ->get();

            foreach ($dbOrderOffers as $dbOrderOffer) {

                $dbPlaces = $dbOrderOffer->getPlaces;
                foreach ($dbPlaces as $dbPlace) {

                    if ($dbPlace->whcr_count > 0 && $dbPlace->whcr_count !== null) {

                        if (isset($dbPlace->getPlace->pl_id)) {

                            $tmpWeight = abs($dbPlace->getPlace->pl_place_weight - $currentWeight);
                            if ($tmpWeight < $plWeight) {

                                $nextPlaceId = $dbPlace->getPlace->pl_id;
                                $plWeight = $tmpWeight;
                                $arPlace = [
                                    'placeId' => $dbPlace->getPlace->pl_id,
                                    'pl_room' => $dbPlace->getPlace->pl_room,
                                    'pl_floor' => $dbPlace->getPlace->pl_floor,
                                    'pl_section' => $dbPlace->getPlace->pl_section,
                                    'pl_row' => $dbPlace->getPlace->pl_row,
                                    'pl_rack' => $dbPlace->getPlace->pl_rack,
                                    'pl_shelf' => $dbPlace->getPlace->pl_shelf,
                                ];
                                $orderOfferId = $dbOrderOffer->oo_id;
                                $offerId = $dbOrderOffer->oo_offer_id;

                            }
                        }
                    }
                }
            }
        }

        $this->orderId = $dbOrder->o_id;

        return [
            'soaId' => $soaId,
            'offerId' => $offerId,
            'orderOfferId' => $orderOfferId,
            'dbOrder' => $dbOrder,
            'dbOrderOffers' => $dbOrderOffers,
            'nextPlaceId' => $nextPlaceId,
            'arPlace' => $arPlace,
        ];
    }

    public function name(): ?string
    {
        return __('Сборка заказа') . ' ' . $this->orderId;
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        return [
            Layout::view('Screens.Terminal.SOA.ScanPlace'),
        ];
    }

}
