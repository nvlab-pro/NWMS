<?php

namespace App\Orchid\Layouts\Orders;

use App\Models\rwLibStatus;
use App\Models\rwOffer;
use App\Models\rwOrder;
use App\Models\rwOrderStatus;
use App\Models\rwOrderType;
use App\Models\rwShop;
use App\Models\rwWarehouse;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\TD;

class OrdersTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'ordersList';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        $currentUser = Auth::user();
        $dbShopsList = rwShop::where('sh_domain_id', $currentUser->domain_id);

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {


        } else {

            $dbShopsList = $dbShopsList->where('sh_user_id', $currentUser->id);

        }
        return [
            TD::make('o_id', 'ID')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->align('center')
                ->render(function (rwOrder $order) {
                    return Link::make($order->o_id)
                        ->route('platform.orders.edit', $order->o_id);
                }),

            TD::make('o_status_id', __('Статус'))
                ->sort()
                ->align('center')
                ->filter(
                    TD::FILTER_SELECT,
                    rwOrderStatus::pluck('os_name', 'os_id')
                        ->toArray() // Замените 'id' на ключевое поле вашей таблицы
                )
                ->render(function (rwOrder $modelName) {
                    return '<div onClick="window.location=\'' . route('platform.orders.edit', $modelName->o_id) . '\'" style="color: ' . $modelName->getStatus->os_color . ';
                        background-color: ' . $modelName->getStatus->os_bgcolor . ';
                        padding: 5px;
                        border-radius: 5px;"><b>' . $modelName->getStatus->os_name . '</b></div>';
                }),

            TD::make('o_ext_id', __('Внешний ID'))
                ->sort()
                ->align('center')
                ->render(function (rwOrder $order) {
                    return $order->o_ext_id ? Link::make($order->o_ext_id)
                        ->route('platform.orders.edit', $order->o_id)
                        : Link::make('-')
                            ->route('platform.orders.edit', $order->o_id);
                }),

            TD::make('o_parcel_id', __('Посылка'))
                ->sort()
                ->align('center')
                ->render(function (rwOrder $order) {
                    return $order->o_parcel_id ? Link::make($order->o_parcel_id)
                        ->route('platform.orders.edit', $order->o_id)
                        : Link::make('-')
                            ->route('platform.orders.edit', $order->o_id);
                }),

            TD::make('o_type_id', __('Тип'))
                ->sort()
                ->align('center')
                ->filter(TD::FILTER_SELECT, rwOrderType::pluck('ot_name', 'ot_id')->toArray())
                ->render(function (rwOrder $order) {
                    return $order->getType->ot_name ? Link::make($order->getType->ot_name)
                        ->route('platform.orders.edit', $order->o_id)
                        : Link::make('-')
                            ->route('platform.orders.edit', $order->o_id);
                }),

            TD::make('o_date', __('Дата заказа'))
                ->sort()
                ->align('center')
                ->render(function (rwOrder $order) {
                    return $order->o_date ? Link::make($order->o_date)
                        ->route('platform.orders.edit', $order->o_id)
                        : Link::make('-')
                            ->route('platform.orders.edit', $order->o_id);
                }),

            TD::make('o_date_send', __('Дата отправки'))
                ->sort()
                ->align('center')
                ->render(function (rwOrder $order) {
                    return $order->o_date_send ? Link::make($order->o_date_send)
                        ->route('platform.orders.edit', $order->o_id)
                        : Link::make('-')
                            ->route('platform.orders.edit', $order->o_id);
                }),

            TD::make('o_count', 'Кол-во твара')
                ->sort()
                ->align('center')
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwOrder $modelName) {
                    return Link::make(isset($modelName->o_count) ? $modelName->o_count : '-')
                        ->route('platform.orders.edit', $modelName->o_id);
                }),

            TD::make('o_sum', 'Сумма')
                ->sort()
                ->align('center')
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwOrder $modelName) {
                    return Link::make(isset($modelName->o_sum) ? $modelName->o_sum : '-')
                        ->route('platform.orders.edit', $modelName->o_id);
                }),

            TD::make('o_shop_id', __('Магазин'))
                ->sort()
                ->align('center')
                ->filter(
                    TD::FILTER_SELECT,
                    $currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')
                        ? $dbShopsList->pluck('sh_name', 'sh_id')->toArray()
                        : $dbShopsList->whereIn('sh_user_id', [$currentUser->id, $currentUser->parent_id])->pluck('sh_name', 'sh_id')->toArray()
                )
                ->render(function (rwOrder $order) {
                    return $order->getShop->sh_name ? Link::make($order->getShop->sh_name)
                        ->route('platform.orders.edit', $order->o_id)
                        : Link::make('-')
                            ->route('platform.orders.edit', $order->o_id);
                }),

            TD::make('o_wh_id', __('Склад'))
                ->sort()
                ->align('center')
                ->filter(TD::FILTER_SELECT, rwWarehouse::pluck('wh_name', 'wh_id')->toArray())
                ->render(function (rwOrder $order) {
                    return $order->getWarehouse->wh_name ? Link::make($order->getWarehouse->wh_name)
                        ->route('platform.orders.edit', $order->o_id)
                        : Link::make('-')
                            ->route('platform.orders.edit', $order->o_id);
                }),

            TD::make(__('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn(rwOrder $order) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(__('Ред.'))
                            ->route('platform.orders.index', $order->id)
                            ->icon('bs.pencil'),
                    ])),
        ];
    }
}
