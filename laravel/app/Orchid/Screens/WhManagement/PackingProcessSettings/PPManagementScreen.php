<?php

namespace App\Orchid\Screens\WhManagement\PackingProcessSettings;

use App\Models\rwSettingsProcPacking;
use App\Models\rwSettingsSoa;
use App\Models\rwWarehouse;
use App\Models\User;
use App\Orchid\Layouts\WhManagement\PackingProcessSettings\PPManagementTable;
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
        return CustomTranslator::get('Настройка очереди упаковки');
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
                        Input::make('queue.spp_name')
                            ->title(CustomTranslator::get('Название очереди'))
                            ->required(),

                        Input::make('queue.spp_priority')
                            ->title(CustomTranslator::get('Приоритет (чем больше, тем выше)'))
                            ->type('number')
                            ->min(0)
                            ->max(1000)
                            ->required(),

                        Select::make('queue.spp_wh_id')
                            ->title(CustomTranslator::get('Склад'))
                            ->fromModel(rwWarehouse::where('wh_type', 2)->where('wh_domain_id', $currentUser->domain_id), 'wh_name', 'wh_id')
                            ->empty(CustomTranslator::get('Не выбрано'), 0),

                    ]),
                    Layout::rows([
                        Select::make('queue.spp_start_place_type')
                            ->title(CustomTranslator::get('Место начала упаковки'))
                            ->popover(CustomTranslator::get('Из какого места будут поступать заказы на упаковку.'))
                            ->options([
                                102 => CustomTranslator::get('Упаковка с самостоятельным подбором'),
                                104 => CustomTranslator::get('Полка сортировки'),
                                105 => CustomTranslator::get('Стол упаковки'),
                            ])
                            ->empty(CustomTranslator::get('Не выбрано'), 0),

                        Select::make('queue.spp_packing_type')
                            ->title(CustomTranslator::get('Тип пикинга'))
                            ->popover(CustomTranslator::get('Выберите будет ли упаковщик сканировать каждый товар или только артикул и вводить количество.'))
                            ->options([
                                0 => CustomTranslator::get('Скан артикула (под пересчет)'),
                                1 => CustomTranslator::get('Скан каждого товара'),
                                2 => CustomTranslator::get('Со сканом честного знака'),
                            ])
                            ->value(0),

                        Select::make('queue.spp_user_id')
                            ->title(CustomTranslator::get('Конкретный пользователь'))
                            ->fromModel(User::where('parent_id', $currentUser->id)->where('domain_id', $currentUser->domain_id), 'name', 'id')
                            ->empty(CustomTranslator::get('Не выбрано'), 0),

                    ]),
                ]),
                Layout::rows([

                    Input::make('queue.spp_ds_id')
                        ->title(CustomTranslator::get('Служба доставки'))
                        ->type('number'),
                ]),

            ])
                ->title(CustomTranslator::get('Создать новую очередь упаковки'))
                ->applyButton(CustomTranslator::get('Сохранить'))
                ->closeButton(CustomTranslator::get('Отмена'))
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

        Toast::info(CustomTranslator::get('Очередь успешно создана!'));
    }
}
