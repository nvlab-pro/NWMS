<?php

namespace App\Orchid\Layouts\Orders;

use App\Http\Middleware\RoleMiddleware;
use App\Models\rwLibStatus;
use App\Models\rwOffer;
use App\Models\rwOrder;
use App\Models\rwOrderStatus;
use App\Models\rwOrderType;
use App\Models\rwShop;
use App\Models\rwWarehouse;
use App\Orchid\Services\OrderService;
use App\Services\CustomTranslator;
use App\WhCore\WhCore;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Alert;
use Illuminate\Http\Request;

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
                ->style('white-space: nowrap;')
                ->filter(TD::FILTER_TEXT)
                ->align('center')
                ->render(function (rwOrder $order) {
                    return Link::make($order->o_id)
                        ->route('platform.orders.edit', $order->o_id);
                }),

            TD::make('o_status_id', CustomTranslator::get('Статус'))
                ->sort()
                ->style('white-space: nowrap;')
                ->align('center')
                ->filter(
                    TD::FILTER_SELECT,
                    rwOrderStatus::all()->mapWithKeys(function ($status) {
                        return [$status->os_id => CustomTranslator::get($status->os_name)]; // Применяем перевод
                    })->toArray()
                )
                ->render(function (rwOrder $modelName) {
                    return '<div onClick="window.location=\'' . route('platform.orders.edit', $modelName->o_id) . '\'" style="color: ' . $modelName->getStatus->os_color . ';
                        background-color: ' . $modelName->getStatus->os_bgcolor . ';
                        padding: 5px;
                        border-radius: 5px;"><b>' . CustomTranslator::get($modelName->getStatus->os_name) . '</b></div>';
                }),

            TD::make('o_ext_id', CustomTranslator::get('Внешний ID'))
                ->sort()
                ->align('center')
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwOrder $order) {
                    return $order->o_ext_id ? Link::make($order->o_ext_id)
                        ->route('platform.orders.edit', $order->o_id)
                        : Link::make('-')
                            ->route('platform.orders.edit', $order->o_id);
                }),

            TD::make('o_parcel_id', CustomTranslator::get('Посылка'))
                ->sort()
                ->align('center')
                ->render(function (rwOrder $order) {
                    return $order->o_parcel_id ? Link::make($order->o_parcel_id)
                        ->route('platform.orders.edit', $order->o_id)
                        : Link::make('-')
                            ->route('platform.orders.edit', $order->o_id);
                }),

            TD::make('o_type_id', CustomTranslator::get('Тип'))
                ->sort()
                ->align('center')
                ->filter(
                    TD::FILTER_SELECT,
                    rwOrderType::all()->mapWithKeys(function ($type) {
                        return [$type->ot_id => CustomTranslator::get($type->ot_name)]; // Применяем перевод
                    })->toArray()

                )
                ->render(function (rwOrder $order) {
                    return $order->getType->ot_name ? Link::make($order->getType->ot_name)
                        ->route('platform.orders.edit', $order->o_id)
                        : Link::make('-')
                            ->route('platform.orders.edit', $order->o_id);
                }),

            TD::make('o_customer_type', CustomTranslator::get('Тип клиента'))
                ->sort()
                ->align('center')
                ->filter(
                    TD::FILTER_SELECT,
                    [
                        '0' => CustomTranslator::get('Физическое лицо'),
                        '1' => CustomTranslator::get('Юридическое лицо'),
                    ]

                )
                ->render(function (rwOrder $order) {
                    $str = CustomTranslator::get('Физическое лицо');
                    if ($order->o_customer_type == 1) $str = CustomTranslator::get('Юридическое лицо');
                    return Link::make($str)
                        ->route('platform.orders.edit', $order->o_id);
                }),

            TD::make('o_date', CustomTranslator::get('Дата заказа'))
                ->sort()
                ->align('center')
                ->filter(DateRange::make())
                ->render(function (rwOrder $order) {
                    return $order->o_date ? Link::make($order->o_date)
                        ->route('platform.orders.edit', $order->o_id)
                        : Link::make('-')
                            ->route('platform.orders.edit', $order->o_id);
                }),

            TD::make('o_date_send', CustomTranslator::get('Дата отправки'))
                ->sort()
                ->align('center')
                ->filter(DateRange::make())
                ->render(function (rwOrder $order) {
                    return $order->o_date_send ? Link::make($order->o_date_send)
                        ->route('platform.orders.edit', $order->o_id)
                        : Link::make('-')
                            ->route('platform.orders.edit', $order->o_id);
                }),

            TD::make('o_count', CustomTranslator::get('Товара заказано'))
                ->sort()
                ->align('center')
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwOrder $modelName) {
                    return Link::make(isset($modelName->o_count) ? $modelName->o_count : '-')
                        ->route('platform.orders.edit', $modelName->o_id);
                }),

            TD::make('wh_count', CustomTranslator::get('Товара отправлено'))
                ->sort()
                ->align('center')
                ->filter(TD::FILTER_TEXT)
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                ->render(function (rwOrder $modelName) {

                    $whCore = new WhCore($modelName->o_wh_id);

                    $bgcolor = ' style="background-color: #ef2828; color: #FFFFFF; border-radius: 10px;"';
                    $offerQtySend = $whCore->getDocRest($modelName->o_id, 2);

                    if ($offerQtySend == $modelName->o_count || $modelName->o_status_id < 20) $bgcolor = '';
                        else {
                            // Пересчитываем по умолчанию
//                            $serviceOrder = new OrderService($modelName->o_id);
//                            $serviceOrder->resaveOrderRests();
                        }

                    return '<div ' . $bgcolor . '><b>' . $offerQtySend . '</b></div>';

//                    return Link::make(isset($modelName->o_count) ? $modelName->o_count : '-')
//                        ->route('platform.orders.edit', $modelName->o_id);
                }),

            TD::make('o_sum', CustomTranslator::get('Сумма'))
                ->sort()
                ->align('center')
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwOrder $modelName) {
                    return Link::make(isset($modelName->o_sum) ? $modelName->o_sum : '-')
                        ->route('platform.orders.edit', $modelName->o_id);
                }),

            TD::make('o_shop_id', CustomTranslator::get('Магазин'))
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

            TD::make('o_wh_id', CustomTranslator::get('Склад'))
                ->sort()
                ->align('center')
                ->filter(TD::FILTER_SELECT, rwWarehouse::pluck('wh_name', 'wh_id')->toArray())
                ->render(function (rwOrder $order) {
                    return $order->getWarehouse->wh_name ? Link::make($order->getWarehouse->wh_name)
                        ->route('platform.orders.edit', $order->o_id)
                        : Link::make('-')
                            ->route('platform.orders.edit', $order->o_id);
                }),

            TD::make(CustomTranslator::get('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn(rwOrder $order) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(CustomTranslator::get('Ред.'))
                            ->route('platform.orders.index', $order->id)
                            ->icon('bs.pencil'),
                        Button::make(CustomTranslator::get('Пересчитать'))
                            ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                            ->method('recalcOrder', [
                                'orderId' => $order->o_id
                            ])
                            ->icon('bs.plus-slash-minus'),

                    ])),
        ];
    }

}
