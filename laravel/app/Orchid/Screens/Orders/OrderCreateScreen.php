<?php

namespace App\Orchid\Screens\Orders;

use App\Models\rwOrder;
use App\Models\rwOrderStatus;
use App\Models\rwOrderType;
use App\Models\rwShop;
use App\Models\rwWarehouse;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Layouts\Rows;
use Orchid\Support\Facades\Alert;

class OrderCreateScreen extends Screen
{
    public $order;

    public function query(): array
    {
        return [
            'order' => new rwOrder(),
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Создание заказа');
    }

    public function commandBar(): array
    {
        return [];
    }

    public function layout(): array
    {
        $currentUser = Auth::user();
        return [
            Layout::rows([
                Input::make('order.o_status_id')
                    ->type('hidden')
                    ->value(10),

                Input::make('order.o_domain_id')
                    ->type('hidden')
                    ->value($currentUser->domain_id),

                Input::make('order.o_user_id')
                    ->type('hidden')
                    ->value($currentUser->id),

                Select::make('order.o_type_id')
                    ->title(CustomTranslator::get('Тип заказа'))
                    ->options(
                        rwOrderType::all()
                            ->pluck('ot_name', 'ot_id')
                            ->map(fn($name) => CustomTranslator::get($name)) // Переводим название типа заказа
                    )
                    ->required(),

                Select::make('order.o_shop_id')
                    ->title(CustomTranslator::get('Магазин'))
                    ->fromModel(
                        $currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')
                            ? rwShop::where('sh_domain_id', $currentUser->domain_id)
                            : rwShop::where('sh_domain_id', $currentUser->domain_id)
                            ->whereIn('sh_user_id', [$currentUser->id, $currentUser->parent_id]),
                        'sh_name',
                        'sh_id'
                    )
                    ->required(),

                Select::make('order.o_wh_id')
                    ->title(CustomTranslator::get('Склад'))
                    ->fromModel(
                        $currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')
                        ? rwWarehouse::where('wh_domain_id', $currentUser->domain_id)->where('wh_type', 2)
                        : rwWarehouse::where('wh_domain_id', $currentUser->domain_id)
                            ->where('wh_type', 2)
                            ->whereIn('wh_user_id', [$currentUser->id, $currentUser->parent_id]),
                        'wh_name',
                        'wh_id'
                    )
                    ->required(),

                Input::make('order.o_ext_id')
                    ->title(CustomTranslator::get('Внешний ID')),

                DateTimer::make('order.o_date')
                    ->title(CustomTranslator::get('Дата заказа'))
                    ->format('Y-m-d')
                    ->value(now()->format('Y-m-d'))
                    ->required(),

                DateTimer::make('order.o_date_send')
                    ->title(CustomTranslator::get('Дата отправки'))
                    ->format('Y-m-d'),

                Button::make(CustomTranslator::get('Сохранить'))
                    ->type(Color::DARK)
                    ->style('margin-bottom: 20px;')
                    ->method('create'),

            ]),
        ];
    }

    public function create()
    {
        request()->validate([
            'order.o_status_id' => 'required',
            'order.o_type_id' => 'required',
            'order.o_shop_id' => 'required',
            'order.o_wh_id' => 'required',
            'order.o_date' => 'required|date',
        ]);

        rwOrder::create(request('order'));

        Alert::info(CustomTranslator::get('Заказ успешно создан'));

        return redirect()->route('platform.orders.index');
    }
}
