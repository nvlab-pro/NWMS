<?php

namespace App\Orchid\Screens\WhManagement\MarkingSettings;

use App\Models\rwDeliveryService;
use App\Models\rwSettingsMarking;
use App\Models\User;
use App\Orchid\Layouts\WhManagement\MarkingSettings\MarkingSettingsTable;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Layouts\Modal;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class MarkingSettingsScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     */
    public function query(): iterable
    {
        $currentUser = Auth::user();

        $dbMarkingList = rwSettingsMarking::where('sm_domain_id', $currentUser->domain_id)
            ->with('getUser')
            ->with('getDS')
            ->get();

        return [
            'dbMarkingList' => $dbMarkingList,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return CustomTranslator::get('Настройка очереди маркировки');
    }

    /**
     * The screen's action buttons (command bar).
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make(CustomTranslator::get('Создать новую очередь'))
                ->modal('createMarkingModal')
                ->method('createMarking')
                ->icon('bs.plus-circle'),
        ];
    }

    /**
     * The screen's layout elements, including modals and table layout.
     */
    public function layout(): iterable
    {
        $currentUser = Auth::user();

        return [
            // Modal for creating a new marking setting
            Layout::modal('createMarkingModal', [
                Layout::columns([
                    Layout::rows([
                        Input::make('queue.sm_name')
                            ->title(CustomTranslator::get('Название очереди'))
                            ->required(),

                        Input::make('queue.sm_priority')
                            ->title(CustomTranslator::get('Приоритет (чем больше, тем выше)'))
                            ->type('number')
                            ->min(0)
                            ->max(1000)
                            ->required(),

                        Select::make('queue.sm_user_id')
                            ->title(CustomTranslator::get('Конкретный пользователь'))
                            ->fromModel(User::where('domain_id', $currentUser->domain_id), 'name', 'id')
                            ->empty(CustomTranslator::get('Не выбрано'), 0),

                    ]),
                    Layout::rows([
                        DateTimer::make('queue.sm_date_from')
                            ->title(CustomTranslator::get('Дата от'))
                            ->format('Y-m-d'),

                        DateTimer::make('queue.sm_date_to')
                            ->title(CustomTranslator::get('Дата до'))
                            ->format('Y-m-d'),

                        Select::make('queue.sm_ds_id')
                            ->title(CustomTranslator::get('Служба доставки'))
                            ->fromModel(rwDeliveryService::class, 'ds_name', 'ds_id')
                            ->empty(CustomTranslator::get('Не выбрано'), 0),

                    ]),
                ]),
            ])
                ->title(CustomTranslator::get('Создать новую очередь маркировки'))
                ->applyButton(CustomTranslator::get('Сохранить'))
                ->closeButton(CustomTranslator::get('Отмена'))
                ->size(Modal::SIZE_XL),

            // Modal for editing an existing marking setting (asynchronous load)
            Layout::modal('editMarkingModal', [
                Layout::columns([
                    Layout::rows([
                        Input::make('queue.sm_name')
                            ->title(CustomTranslator::get('Название очереди'))
                            ->required(),

                        Input::make('queue.sm_priority')
                            ->title(CustomTranslator::get('Приоритет (чем больше, тем выше)'))
                            ->type('number')
                            ->min(0)
                            ->max(1000)
                            ->required(),

                        Select::make('queue.sm_user_id')
                            ->title(CustomTranslator::get('Конкретный пользователь'))
                            ->fromModel(User::where('domain_id', $currentUser->domain_id), 'name', 'id')
                            ->empty(CustomTranslator::get('Не выбрано'), 0),

                    ]),
                    Layout::rows([
                        DateTimer::make('queue.sm_date_from')
                            ->title(CustomTranslator::get('Дата от'))
                            ->format('Y-m-d'),

                        DateTimer::make('queue.sm_date_to')
                            ->title(CustomTranslator::get('Дата до'))
                            ->format('Y-m-d'),

                        Select::make('queue.sm_ds_id')
                            ->title(CustomTranslator::get('Служба доставки'))
                            ->fromModel(rwDeliveryService::class, 'ds_name', 'ds_id')
                            ->empty(CustomTranslator::get('Не выбрано'), 0),
                    ]),
                ]),
                Layout::rows([
                    Input::make('queue.sm_id')->type('hidden'),  // hold the ID
                ]),
            ])
                ->deferred('asyncGetMarking')  // load data when modal opens
                ->applyButton(CustomTranslator::get('Сохранить'))
                ->closeButton(CustomTranslator::get('Отмена'))
                ->size(Modal::SIZE_XL),

            // Table layout displaying the list of markings
            MarkingSettingsTable::class,
        ];
    }

    /**
     * Asynchronous method to load a marking record for editing.
     * This replaces the screen data when opening the edit modal.
     */
    public function asyncGetMarking(int $smId): iterable
    {
        $marking = rwSettingsMarking::find($smId);

        // Return the record data under 'queue' to populate the form fields
        return [
            'queue' => $marking ? $marking->toArray() : [],
        ];
    }

    /**
     * Handler for creating a new marking setting (called on create modal save).
     */
    public function createMarking(Request $request)
    {
        $currentUser = Auth::user();

        $validated = $request->validate([
            'queue.sm_name'      => 'required|string|max:255',
            'queue.sm_priority'  => 'required|integer|min:1|max:1000',
            'queue.sm_user_id'   => 'nullable|integer',
            'queue.sm_ds_id'     => 'nullable|integer',
            'queue.sm_date_from' => 'nullable|date',
            'queue.sm_date_to'   => 'nullable|date',
        ]);

        // Создание записи в базе
        rwSettingsMarking::create([
            'sm_name'      => $validated['queue']['sm_name'],
            'sm_priority'  => $validated['queue']['sm_priority'],
            'sm_user_id'   => $validated['queue']['sm_user_id'] ?? null,
            'sm_ds_id'     => $validated['queue']['sm_ds_id'] ?? null,
            'sm_date_from' => $validated['queue']['sm_date_from'] ?? null,
            'sm_date_to'   => $validated['queue']['sm_date_to'] ?? null,
            'sm_status_id' => 1,  // По умолчанию статус 1 (активно)
            'sm_domain_id' => $currentUser->domain_id,
        ]);

        Alert::success(CustomTranslator::get('Очередь успешно создана!'));
    }

    /**
     * Handler for saving changes to an existing marking (called on edit modal save).
     */
    public function updateMarking(Request $request)
    {
        $data = $request->input('queue');
        $marking = rwSettingsMarking::find($data['sm_id'] ?? null);

        if ($marking) {
            // Обновление записи в базе
            $marking->update([
                'sm_name'      => $data['sm_name']      ?? $marking->sm_name,
                'sm_priority'  => $data['sm_priority']  ?? $marking->sm_priority,
                'sm_user_id'   => $data['sm_user_id']   ?? $marking->sm_user_id,
                'sm_ds_id'     => $data['sm_ds_id']     ?? $marking->sm_ds_id,
                'sm_date_from' => $data['sm_date_from'] ?? $marking->sm_date_from,
                'sm_date_to'   => $data['sm_date_to']   ?? $marking->sm_date_to,
            ]);
            Alert::success(CustomTranslator::get('Данные сохранены!'));
        }
        // Refresh the screen by redirecting to the index route
        return redirect()->route('platform.whmanagement.marking-settings.index');
    }
}
