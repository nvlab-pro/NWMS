<?php

namespace App\Orchid\Screens\WhManagement\MarkingSettings;

use App\Models\rwSettingsMarking;
use App\Models\User;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class MarkingSettingsEditScreen extends Screen
{
    private $markingSettings;

    public function query($smId): iterable
    {
        $this->markingSettings = rwSettingsMarking::where('sm_id', $smId)->first();

        return [
            'dbMarkingSettings' => $this->markingSettings,
        ];
    }

    public function name(): ?string
    {
        // Header title: e.g., "Очередь: 5"
        return CustomTranslator::get('Очередь') . ': ' . ($this->markingSettings->sm_id ?? '');
    }

    public function description(): string
    {
        // Subheader: the name of the marking queue
        return $this->markingSettings->sm_name ?? '';
    }

    public function commandBar(): iterable
    {
        return [];  // No extra buttons on edit screen
    }

    public function layout(): iterable
    {
        $currentUser = Auth::user();

        return [
            Layout::rows([
                // Hidden ID field
                Input::make('queue.sm_id')->type('hidden')->value($this->markingSettings->sm_id),

                // Grouped fields: Name and Priority in one row
                Group::make([
                    Input::make('queue.sm_name')
                        ->title(CustomTranslator::get('Название очереди'))
                        ->value($this->markingSettings->sm_name)
                        ->required(),

                    Input::make('queue.sm_priority')
                        ->title(CustomTranslator::get('Приоритет (чем больше, тем выше)'))
                        ->type('number')
                        ->min(0)->max(1000)
                        ->value($this->markingSettings->sm_priority)
                        ->required(),
                ]),

                // User selection (full width row)
                Select::make('queue.sm_user_id')
                    ->title(CustomTranslator::get('Конкретный пользователь'))
                    ->fromModel(
                        User::where('parent_id', $currentUser->id)
                            ->where('domain_id', $currentUser->domain_id),
                        'name', 'id'
                    )
                    ->empty(CustomTranslator::get('Не выбрано'), 0)
                    ->value($this->markingSettings->sm_user_id),

                // DS input (full width row)
                Input::make('queue.sm_ds_id')
                    ->title(CustomTranslator::get('Служба доставки'))
                    ->type('number')
                    ->value($this->markingSettings->sm_ds_id),

                // Grouped fields: Date From and Date To in one row
                Group::make([
                    DateTimer::make('queue.sm_date_from')
                        ->title(CustomTranslator::get('Дата от'))
                        ->format('Y-m-d')
                        ->value($this->markingSettings->sm_date_from),

                    DateTimer::make('queue.sm_date_to')
                        ->title(CustomTranslator::get('Дата до'))
                        ->format('Y-m-d')
                        ->value($this->markingSettings->sm_date_to),
                ]),

                // Save button
                Button::make(CustomTranslator::get('Сохранить изменения'))
                    ->class('btn btn-primary d-block mx-auto')
                    ->method('saveChanges')
                    ->parameters([
                        '_token' => csrf_token(),
                    ]),
            ]),
        ];
    }

    /**
     * Handle the form submission to save changes to the marking setting.
     */
    public function saveChanges(Request $request)
    {
        $data = $request->input('queue');
        $marking = rwSettingsMarking::find($data['sm_id'] ?? null);

        if ($marking) {
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
        return redirect()->route('platform.whmanagement.marking-settings.index');
    }
}
