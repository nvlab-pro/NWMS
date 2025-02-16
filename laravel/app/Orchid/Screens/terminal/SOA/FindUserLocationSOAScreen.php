<?php

namespace App\Orchid\Screens\terminal\SOA;

use App\Models\rwOrder;
use App\Models\rwOrderOffer;
use App\Models\rwPlaces;
use App\Orchid\Services\SOAService;
use App\WhPlaces\WhPlaces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

// Определяем текущее положение человека на складе
class FindUserLocationSOAScreen extends Screen
{
    private $orderId;

    public function screenBaseView(): string
    {
        return 'loyouts.base';

    }

    public function query($soaId, $orderId, Request $request): iterable
    {

        $currentUser = Auth::user();

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
        }

        $this->orderId = $dbOrder->o_id;

        return [
            'soaId'             => $soaId,
            'dbOrder'           => $dbOrder,
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
            Layout::view('Screens.Terminal.SOA.actionFindUserLocation'),
        ];
    }

}
