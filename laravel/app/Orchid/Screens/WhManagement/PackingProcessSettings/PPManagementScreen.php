<?php

namespace App\Orchid\Screens\WhManagement\PackingProcessSettings;

use App\Models\rwSettingsProcPacking;
use App\Models\rwSettingsSoa;
use App\Models\rwWarehouse;
use App\Models\User;
use App\Orchid\Layouts\WhManagement\PackingProcessSettings\PPManagementTable;
use App\Orchid\Layouts\WhManagement\SingleOrderAssembly\SOAManagementTable;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Illuminate\Http\Request;

class PPManagementScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     */
    public function query(): iterable
    {
        $currentUser = Auth::user();

        $dbSettingsList = rwSettingsProcPacking::where('spp_domain_id', $currentUser->domain_id)
            ->with('getWarehouse')
            ->with('getUser')
            ->with('getStartPlace')
            ->get();

        return [
            'dbSettingsList' => $dbSettingsList,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Настройка очереди упаковки';
    }

    /**
     * The screen's action buttons.
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make(__('Создать новую очередь'))
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
                        Input::make('queue.spp_name')
                            ->title('Название очереди:')
                            ->required(),

                        Input::make('queue.spp_priority')
                            ->title('Приоритет (чем больше, тем выше):')
                            ->type('number')
                            ->min(0)
                            ->max(1000)
                            ->required(),

                        Select::make('queue.spp_wh_id')
                            ->title(__('Склад:'))
                            ->fromModel(rwWarehouse::where('wh_type', 2)->where('wh_domain_id', $currentUser->domain_id), 'wh_name', 'wh_id')
                            ->empty('Не выбрано', 0),

                    ]),
                    Layout::rows([
                        Select::make('queue.spp_start_place_type')
                            ->title(__('Место начала упаковки:'))
                            ->popover(__('Из какого места будут поступать заказы на упаковку.'))
                            ->options([
                                102 => __('Упаковка с самостоятельным подбором'),
                                104 => __('Полка сортировки'),
                                105 => __('Стол упаковки'),
                            ])
                            ->empty('Не выбрано', 0),

                        Select::make('queue.spp_packing_type')
                            ->title(__('Тип пикинга:'))
                            ->popover(__('Выберите будет ли упаковщик сканировать каждый товар или только артикул и вводить количество.'))
                            ->options([
                                0 => __('Скан артикула (под пересчет)'),
                                1 => __('Скан каждого товара'),
                                2 => __('Со сканом честного знака'),
                            ])
                            ->value(0),

                        Select::make('queue.spp_user_id')
                            ->title(__('Конкретный пользователь:'))
                            ->fromModel(User::where('parent_id', $currentUser->id)->where('domain_id', $currentUser->domain_id), 'name', 'id')
                            ->empty('Не выбрано', 0),

                    ]),
                ]),
                Layout::rows([

                    Input::make('queue.spp_ds_id')
                        ->title(__('Служба доставки:'))
                        ->type('number'),
                ]),

            ])
                ->title(__('Создать новую очередь упаковки'))
                ->applyButton('Сохранить')
                ->closeButton('Отмена')
                ->size(Modal::SIZE_XL),

            PPManagementTable::class,

        ];
    }

    /**
     * Обработчик формы создания очереди.
     */
    public function createQueue(Request $request)
    {
        $currentUser = Auth::user();

        $validated = $request->validate([
            'queue.spp_name' => 'required|string|max:255',
            'queue.spp_priority' => 'required|integer|min:1|max:1000',
            'queue.spp_wh_id' => 'required|integer|min:1',
            'queue.spp_user_id' => 'nullable|integer',
            'queue.spp_ds_id' => 'nullable|integer',
            'queue.spp_start_place_type' => 'nullable|integer',
            'queue.spp_packing_type' => 'nullable|integer',
        ]);

        // Создание записи в базе
        rwSettingsProcPacking::create([
            'spp_name' => $validated['queue']['spp_name'],
            'spp_priority' => $validated['queue']['spp_priority'],
            'spp_wh_id' => $validated['queue']['spp_wh_id'],
            'spp_user_id' => $validated['queue']['spp_user_id'],
            'spp_ds_id' => $validated['queue']['spp_ds_id'],
            'spp_start_place_type' => $validated['queue']['spp_start_place_type'],
            'spp_packing_type' => $validated['queue']['spp_packing_type'],
            'spp_status_id' => 1, // По умолчанию ставим статус 1 (можно изменить логику)
            'spp_domain_id' => $currentUser->domain_id, // Здесь можно подставить реальный домен, если нужно
        ]);

        Toast::info('Очередь успешно создана!');
    }
}
