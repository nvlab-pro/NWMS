<?php

namespace App\Orchid\Screens\terminal\SOA;

use App\Models\rwBarcode;
use App\Models\rwOffer;
use App\Models\rwOrder;
use App\Models\rwOrderOffer;
use App\Orchid\Services\SOAService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class GetOfferSOAScreen extends Screen
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
                    ->first();

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
            Layout::view('Screens.Terminal.SOA.getOffer'),
        ];
    }
}
