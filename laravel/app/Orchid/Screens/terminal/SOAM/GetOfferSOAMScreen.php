<?php

namespace App\Orchid\Screens\terminal\SOAM;

use App\Models\rwBarcode;
use App\Models\rwOffer;
use App\Models\rwOrder;
use App\Models\rwOrderAssembly;
use App\Models\rwOrderOffer;
use App\Models\rwPlace;
use App\Models\WhcRest;
use App\Orchid\Services\SOAService;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class GetOfferSOAMScreen extends Screen
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
            'offerId' => 'nullable|string',
            'orderOfferId' => 'nullable|string',
            'placeId' => 'nullable|string',
        ]);

        $currentUser = Auth::user();
        $currentPlace = [];
        $maxCount = 0;
        isset($validatedData['barcode']) ? $barcode = $validatedData['barcode'] : $barcode = null;
        isset($validatedData['offerId']) ? $offerId = $validatedData['offerId'] : $offerId = null;
        isset($validatedData['orderOfferId']) ? $orderOfferId = $validatedData['orderOfferId'] : $orderOfferId = null;
        isset($validatedData['placeId']) ? $placeId = $validatedData['placeId'] : $placeId = null;

        // Получаем заказ для сборки
        if ($orderId == 0) {
            $dbOrder = SOAService::getFirstOrder($soaId, $currentUser->id);
        } else {
            $dbOrder = rwOrder::find($orderId);
        }

        // Если заказ есть
        if ($dbOrder) {

            if ($barcode != '') {

                $dbBarcode = rwBarcode::where('br_barcode', $barcode)
                    ->where('br_offer_id', $offerId)
                    ->first();

                if ($dbBarcode) {

                    $dbOffer = rwOffer::where('of_id', $offerId)->first();

                }

                // Получаем очередной товар на этом месте
                $dbOrderOffer = rwOrderOffer::where('oo_id', $orderOfferId)
                    ->where('oo_order_id', $dbOrder->o_id)
                    ->with('getOffer')
                    ->first();

                // Считаем оставшийся остаток
                $offerRest = $dbOrderOffer->oo_qty;
                $sendQty = rwOrderAssembly::where('oa_order_id', $dbOrder->o_id)
                    ->where('oa_offer_id', $dbOrderOffer->getOffer->of_id)
                    ->where('oa_qty', '>', 0)
                    ->sum('oa_qty');

                $offerRest -= $sendQty;


                // Получаем текущее место хранения
                $currentPlace = rwPlace::where('pl_id', $placeId)
                    ->first();

                // Получаем максимальное количество товара на полке
                if ($currentPlace) {

                    $dbRest = WhcRest::where('whcr_offer_id', $dbOrderOffer->getOffer->of_id)
                        ->where('whcr_wh_id', $dbOrder->o_wh_id)
                        ->where('whcr_place_id', $currentPlace->pl_id)
                        ->where('whcr_count', '>', 0)
                        ->first();

                    if ($dbRest) {
                        $maxCount = $dbRest->whcr_count;
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
            'dbOffer' => $dbOffer,
            'dbOrderOffer' => $dbOrderOffer,
            'currentPlace' => $currentPlace,
            'maxCount' => $maxCount,
            'offerRest' => $offerRest,
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
            Layout::view('Screens.Terminal.SOAM.getOfferSOAM'),
        ];
    }
}
