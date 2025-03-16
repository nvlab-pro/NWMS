<?php

namespace App\Orchid\Screens\WhManagement\SingleOrderAssembly;

use App\Models\rwSettingsSoa;
use App\Models\rwWarehouse;
use App\Models\User;
use App\Orchid\Layouts\WhManagement\SingleOrderAssembly\SOAManagementTable;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Illuminate\Http\Request;

class SOAManagementScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     */
    public function query(): iterable
    {
        $currentUser = Auth::user();

        $dbWaveAssemblyList = rwSettingsSoa::where('ssoa_domain_id', $currentUser->domain_id)
            ->with('getWarehouse')
            ->with('getUser')
            ->with('getDS')
            ->with('getFinishPlace')
            ->get();

        return [
            'dbWaveAssemblyList' => $dbWaveAssemblyList,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return CustomTranslator::get('Настройка очереди позаказной сборки');
    }

    /**
     * The screen's action buttons.
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make(CustomTranslator::get('Создать новую очередь'))
                ->modal('createQueueModal')
                ->method('createQueue')
                ->icon('bs.plus-circle'),
        ];
    }

    /**
     * The screen's layout elements.
     */
    public function layout(): iterable
    {
        $currentUser = Auth::user();

        return [
            Layout::modal('createQueueModal', [
                Layout::columns([
                    Layout::rows([
                        Input::make('queue.ssoa_name')
                            ->title(CustomTranslator::get('Название очереди'))
                            ->required(),

                        Input::make('queue.ssoa_priority')
                            ->title(CustomTranslator::get('Приоритет (чем больше, тем выше)'))
                            ->type('number')
                            ->min(0)
                            ->max(1000)
                            ->required(),

                        Select::make('queue.ssoa_wh_id')
                            ->title(CustomTranslator::get('Склад'))
                            ->fromModel(rwWarehouse::where('wh_type', 2)->where('wh_domain_id', $currentUser->domain_id), 'wh_name', 'wh_id')
                            ->empty(CustomTranslator::get('Не выбрано'), 0),

                    ]),
                    Layout::rows([
                        Select::make('queue.ssoa_finish_place_type')
                            ->title(CustomTranslator::get('Место завершения сборки'))
                            ->popover(CustomTranslator::get('Если вы хотите собрать заказ и сразу отгрузить, то выберите "Место завершения сборки". Если же заказ затем нужно будет упаковывать на упаковочном столе, то выберите "Полка сортировки" или "Стол упаковки".'))
                            ->options([
                                104 => CustomTranslator::get('Полка сортировки'),
                                105 => CustomTranslator::get('Стол упаковки'),
                                107 => CustomTranslator::get('Место завершения сборки'),
                            ])
                            ->empty(CustomTranslator::get('Не выбрано'), 0),

                        Select::make('queue.ssoa_all_offers')
                            ->title(CustomTranslator::get('Разрешить неполную сборку товара'))
                            ->popover(CustomTranslator::get('Если проставить "Да", то система позволит закончить сборку заказа, даже если товара не хватает.'))
                            ->options([
                                1 => CustomTranslator::get('Нет'),
                                0 => CustomTranslator::get('Да'),
                            ])
                            ->value(1),

                        Select::make('queue.ssoa_picking_type')
                            ->title(CustomTranslator::get('Тип пикинга'))
                            ->popover(CustomTranslator::get('Выберите будет ли кладовщик сканировать каждый товар или только артикул и вводить количество.'))
                            ->options([
                                0 => CustomTranslator::get('Скан артикула (под пересчет)'),
                                1 => CustomTranslator::get('Скан каждого товара'),
                            ])
                            ->value(0),

                    ]),
                ]),
                Layout::rows([

                    Select::make('queue.ssoa_user_id')
                        ->title(CustomTranslator::get('Конкретный пользователь'))
                        ->fromModel(User::where('parent_id', $currentUser->id)->where('domain_id', $currentUser->domain_id), 'name', 'id')
                        ->empty(CustomTranslator::get('Не выбрано'), 0),

                    Input::make('queue.ssoa_ds_id')
                        ->title(CustomTranslator::get('Служба доставки'))
                        ->type('number'),
                ]),

                Layout::columns([
                    Layout::rows([
                        Input::make('queue.ssoa_date_from')
                            ->title(CustomTranslator::get('Дата от'))
                            ->type('date')
                            ->required(),

                        Input::make('queue.ssoa_offers_count_from')
                            ->title(CustomTranslator::get('Количество товаров в заказе от'))
                            ->type('number'),

                        Input::make('queue.ssoa_order_from')
                            ->title(CustomTranslator::get('ID заказа от'))
                            ->type('number'),

                    ]),
                    Layout::rows([
                        Input::make('queue.ssoa_date_to')
                            ->title(CustomTranslator::get('Дата до'))
                            ->type('date')
                            ->required(),

                        Input::make('queue.ssoa_offers_count_to')
                            ->title(CustomTranslator::get('Количество товаров в заказе до'))
                            ->type('number'),

                        Input::make('queue.ssoa_order_to')
                            ->title(CustomTranslator::get('ID заказа до'))
                            ->type('number'),
                    ]),
                ]),
            ])
                ->title(CustomTranslator::get('Создать новую очередь позаказной сборки'))
                ->applyButton(CustomTranslator::get('Сохранить'))
                ->closeButton(CustomTranslator::get('Отмена'))
                ->size(Modal::SIZE_XL),

            SOAManagementTable::class,

        ];
    }

    /**
     * Обработчик формы создания очереди.
     */
    public function createQueue(Request $request)
    {
        $currentUser = Auth::user();

        $validated = $request->validate([
            'queue.ssoa_name' => 'required|string|max:255',
            'queue.ssoa_priority' => 'required|integer|min:1|max:1000',
            'queue.ssoa_wh_id' => 'required|integer|min:1',
            'queue.ssoa_user_id' => 'nullable|integer',
            'queue.ssoa_ds_id' => 'nullable|integer',
            'queue.ssoa_date_from' => 'nullable|date',
            'queue.ssoa_date_to' => 'nullable|date',
            'queue.ssoa_offers_count_from' => 'nullable|integer',
            'queue.ssoa_offers_count_to' => 'nullable|integer',
            'queue.ssoa_order_from' => 'nullable|integer',
            'queue.ssoa_order_to' => 'nullable|integer',
            'queue.ssoa_finish_place_type' => 'nullable|integer',
            'queue.ssoa_all_offers' => 'nullable|integer',
            'queue.ssoa_picking_type' => 'nullable|integer',
        ]);

        // Создание записи в базе
        rwSettingsSoa::create([
            'ssoa_name' => $validated['queue']['ssoa_name'],
            'ssoa_priority' => $validated['queue']['ssoa_priority'],
            'ssoa_wh_id' => $validated['queue']['ssoa_wh_id'],
            'ssoa_user_id' => $validated['queue']['ssoa_user_id'],
            'ssoa_ds_id' => $validated['queue']['ssoa_ds_id'],
            'ssoa_date_from' => $validated['queue']['ssoa_date_from'],
            'ssoa_date_to' => $validated['queue']['ssoa_date_to'],
            'ssoa_offers_count_from' => $validated['queue']['ssoa_offers_count_from'],
            'ssoa_offers_count_to' => $validated['queue']['ssoa_offers_count_to'],
            'ssoa_order_from' => $validated['queue']['ssoa_order_from'],
            'ssoa_order_to' => $validated['queue']['ssoa_order_to'],
            'ssoa_status_id' => 1, // По умолчанию ставим статус 1 (можно изменить логику)
            'ssoa_domain_id' => $currentUser->domain_id, // Здесь можно подставить реальный домен, если нужно
            'ssoa_finish_place_type' => $validated['queue']['ssoa_finish_place_type'],
            'ssoa_all_offers' => $validated['queue']['ssoa_all_offers'],
            'ssoa_picking_type' => $validated['queue']['ssoa_picking_type'],
        ]);

        Toast::info(CustomTranslator::get('Очередь успешно создана!'));
    }
}
