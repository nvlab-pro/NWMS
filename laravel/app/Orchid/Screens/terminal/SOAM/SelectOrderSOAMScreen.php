<?php

namespace App\Orchid\Screens\terminal\SOAM;

use App\Models\rwOrder;
use App\Models\rwOrderOffer;
use App\Models\rwPlace;
use App\Models\WhcRest;
use App\Orchid\Services\SOAService;
use App\WhPlaces\WhPlaces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
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

        $currentUser = Auth::user();

        $arOffersList = [];
        $arPlacesList = [];

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

            //Поолучаем список товаров
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
                            'whcr_count' => $dbPlace->whcr_count,
                        ];
                    }

                }

                $arOffersList[$dbOrderOffer->oo_id] = [
                    'offerId' => $dbOrderOffer->getOffer->of_id,
                    'offerDocId' => $dbOrderOffer->oo_id,
                    'offerName' => $dbOrderOffer->getOffer->of_name,
                    'offerArt' => $dbOrderOffer->getOffer->of_article,
                    'offerImg' => $dbOrderOffer->getOffer->of_img,
                    'offerQty' => $dbOrderOffer->oo_qty,
                ];

            }
        }

        $this->orderId = $dbOrder->o_id;

        return [
            'soaId' => $soaId,
            'dbOrder' => $dbOrder,
            'arOffersList' => $arOffersList,
            'arPlacesList' => $arPlacesList,
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
            Layout::view('Screens.Terminal.SOAM.SelectOrderSOAM'),
        ];
    }

}
