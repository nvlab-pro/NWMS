<?php

namespace App\Orchid\Screens\terminal\SOA;

use App\Models\rwOrder;
use App\Models\rwOrderOffer;
use App\Models\rwPlace;
use App\Orchid\Services\SOAService;
use App\WhPlaces\WhPlaces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class ScanSOAScreen extends Screen
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

        $scanFinish = 0;
        $dbOrder = [];
        $OfferId = 0;
        $nextPlaceId = 0;
        $plWeight = 9999999999;
        $arPlace = [];

        $currentUser = Auth::user();
        isset($validatedData['action']) ? $action = $validatedData['action'] : $action = null;
        isset($validatedData['barcode']) ? $barcode = $validatedData['barcode'] : $barcode = null;

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

            // Осканировано место, ищем ближайший товар
            if ($action == 'findPlace') {

                $tmpPlaceId = new WhPlaces($barcode);
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
                                    $plWeight    = $tmpWeight;
                                    $arPlace    = [
                                        'placeId'      => $dbPlace->getPlace->pl_id,
                                        'pl_room'      => $dbPlace->getPlace->pl_room,
                                        'pl_floor'     => $dbPlace->getPlace->pl_floor,
                                        'pl_section'   => $dbPlace->getPlace->pl_section,
                                        'pl_row'       => $dbPlace->getPlace->pl_row,
                                        'pl_rack'      => $dbPlace->getPlace->pl_rack,
                                        'pl_shelf'     => $dbPlace->getPlace->pl_shelf,
                                    ];
                                    $OfferId = $dbOrderOffer->oo_id;

                                }
                            }
                        }
                    }
                }
            }

            // Если имеем заказ на сборке получаем список товаров
            if ($dbOrder->o_status_id == 50) {

                // Получаем первый несобранный товар
                $dbOrderOffers = rwOrderOffer::where('oo_order_id', $dbOrder->o_id)
                    ->where('oo_operation_user_id', 0)
                    ->first();

                if ($dbOrderOffers) {

                } else {

                    $scanFinish = 1;

                }

            }

        }

        $this->orderId = $dbOrder->o_id;

        return [
            'soaId'             => $soaId,
            'OfferId'           => $OfferId,
            'dbOrder'           => $dbOrder,
            'dbOrderOffers'     => $dbOrderOffers,
            'scanFinish'        => $scanFinish,
            'action'            => $action,
            'nextPlaceId'       => $nextPlaceId,
            'arPlace'           => $arPlace,
        ];
    }

    public function name(): ?string
    {
        return __('Сборка заказа').' '.$this->orderId;
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        return [
//            Layout::view('Screens.Terminal.SOA.ScanInput'),
            Layout::view('Screens.Terminal.SOA.actionNull'),
            Layout::view('Screens.Terminal.SOA.findPlace'),
            Layout::view('Screens.Terminal.SOA.scanOffer'),
        ];
    }

}
