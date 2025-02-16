<?php

namespace App\Orchid\Screens\terminal\SOA;

use App\Models\rwBarcode;
use App\Models\rwOffer;
use App\Models\rwOrder;
use App\Models\rwOrderOffer;
use App\Models\rwPlaces;
use App\Orchid\Services\SOAService;
use App\WhCore\WhCore;
use App\WhPlaces\WhPlaces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

// Мы знаем место, берем товар
class ScanOfferSOAScreen extends Screen
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

        $whId = 0;
        $currentUser = Auth::user();
        isset($validatedData['barcode']) ? $barcode = $validatedData['barcode'] : $barcode = null;
        isset($validatedData['offerId']) ? $offerId = $validatedData['offerId'] : $offerId = null;
        isset($validatedData['orderOfferId']) ? $orderOfferId = $validatedData['orderOfferId'] : $orderOfferId = null;
        isset($validatedData['placeId']) ? $placeId = $validatedData['placeId'] : $placeId = null;
        $arBarcodes = [];

        // Получаем заказ для сборки
        if ($orderId == 0) {
            $dbOrder = SOAService::getFirstOrder($soaId, $currentUser->id);
        } else {
            $dbOrder = rwOrder::find($orderId);
        }

        // Если заказ есть
        if ($dbOrder) {

            $whId = $dbOrder->o_wh_id;

            // Определяем место
            $dbPlace = new WhPlaces($barcode);

            $currentPlace = $dbPlace->getPlaceId();

            // Ищем товары на этом месте
            if ($currentPlace > 0) {

//                $whCore = new WhCore($whId);

                $dbOffer = rwOffer::where('of_id', $offerId)->first();

                $arBarcodes = rwBarcode::where('br_offer_id', $offerId)->get();

                    $arBarcodes = rwBarcode::where('br_offer_id', $offerId)->pluck('br_barcode')->toArray();

            }

            // Получаем очередной товар на этом месте
            $dbOrderOffer = rwOrderOffer::where('oo_id', $orderOfferId)
                ->where('oo_order_id', $dbOrder->o_id)
                ->first();

        }

        $this->orderId = $dbOrder->o_id;

        return [
            'soaId' => $soaId,
            'offerId' => $offerId,
            'orderOfferId' => $orderOfferId,
            'dbOrder' => $dbOrder,
            'dbOffer' => $dbOffer,
            'dbOrderOffer' => $dbOrderOffer,
            'arBarcodes' => $arBarcodes,
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
            Layout::view('Screens.Terminal.SOA.scanOffer'),
        ];
    }

}
