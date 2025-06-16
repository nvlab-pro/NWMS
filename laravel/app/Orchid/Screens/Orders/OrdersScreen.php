<?php

namespace App\Orchid\Screens\Orders;

use App\Models\rwAcceptance;
use App\Models\rwOffer;
use App\Models\rwOrder;
use App\Models\rwWarehouse;
use App\Orchid\Layouts\Orders\OrdersTable;
use App\Orchid\Services\OrderService;
use App\Services\CustomTranslator;
use App\WhCore\WhCore;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Alert;

class OrdersScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $currentUser = Auth::user();
        $dbOrders = rwOrder::query()
            ->where('o_domain_id', $currentUser->domain_id);

        if (!$currentUser->hasRole('admin')) {
            if ($currentUser->hasRole('warehouse_manager') || $currentUser->hasRole('warehouse_worker')) {
                $arWhList = rwWarehouse::where('wh_parent_id', $currentUser->wh_id)
                    ->pluck('wh_id')
                    ->toArray();
                $dbOrders = $dbOrders->whereIn('o_wh_id', $arWhList);
            } else {
                $dbOrders = $dbOrders->whereHas('getShop', function ($query) use ($currentUser) {
                    $query->whereIn('sh_user_id', [$currentUser->id, $currentUser->parent_id]);
                });
            }
        }

        return [
            'ordersList' => $dbOrders->filters()->defaultSort('o_id', 'desc')->paginate(50),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return CustomTranslator::get('Список заказов');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(CustomTranslator::get('Создать новый заказ'))
                ->icon('bs.plus-circle')
                ->route('platform.orders.create.index'),

            Link::make(CustomTranslator::get('Импорт заказа'))
                ->icon('bs.cloud-upload')
                ->route('platform.orders.import', 0),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            OrdersTable::class,
        ];
    }

    private function WhCore(int $int)
    {
    }

    public function recalcOrder(Request $request)
    {
        $orderId = $request->get('orderId');

        $serviceOrder = new OrderService($orderId);
        $serviceOrder->resaveOrderRests();


        Alert::info(CustomTranslator::get('Заказ') . ' ' . $orderId . ' '  . CustomTranslator::get('успешно обновлен!'));
    }
}
