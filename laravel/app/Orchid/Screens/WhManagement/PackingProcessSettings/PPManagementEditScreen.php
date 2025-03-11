<?php

namespace App\Orchid\Screens\WhManagement\PackingProcessSettings;

use App\Models\rwSettingsProcPacking;
use App\Models\rwWarehouse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class PPManagementEditScreen extends Screen
{
    private $ppSettings;

    public function query($ppId): iterable
    {
        $this->ppSettings = rwSettingsProcPacking::where('spp_id', $ppId)
            ->first();

        return [
            'dbPpSettings' => $this->ppSettings,
        ];
    }

    public function name(): ?string
    {
        return __('Очередь: ') . $this->ppSettings->spp_id;
    }

    public function description(): string
    {
        return $this->ppSettings->spp_name;
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        $currentUser = Auth::user();

        return [
            Layout::rows([

                Input::make('queue.spp_id')
                    ->hidden()
                    ->value($this->ppSettings->spp_id),

                Group::make([
                    Input::make('queue.spp_name')
                        ->title('Название очереди:')
                        ->value($this->ppSettings->spp_name)
                        ->required(),

                    Input::make('queue.spp_priority')
                        ->title('Приоритет (чем больше, тем выше):')
                        ->type('number')
                        ->min(0)
                        ->max(1000)
                        ->value($this->ppSettings->spp_priority)
                        ->required(),

                ]),

                Group::make([

                    Select::make('queue.spp_wh_id')
                        ->title(__('Склад:'))
                        ->fromModel(rwWarehouse::where('wh_type', 2)->where('wh_domain_id', $currentUser->domain_id), 'wh_name', 'wh_id')
                        ->empty('Не выбрано', 0)
                        ->value($this->ppSettings->spp_wh_id),

                    Select::make('queue.spp_packing_type')
                        ->title(__('Тип пикинга:'))
                        ->popover(__('Выберите будет ли упаковщик сканировать каждый товар или только артикул и вводить количество.'))
                        ->options([
                            0 => __('Скан артикула (под пересчет)'),
                            1 => __('Скан каждого товара'),
                            2 => __('Со сканом честного знака'),
                        ])
                        ->value($this->ppSettings->spp_packing_type),

                ]),
                Group::make([
                    Select::make('queue.spp_start_place_type')
                        ->title(__('Место начала упаковки:'))
                        ->popover(__('Из какого места будут поступать заказы на упаковку.'))
                        ->options([
                            102 => __('Упаковка с самостоятельным подбором'),
                            104 => __('Полка сортировки'),
                            105 => __('Стол упаковки'),
                        ])
                        ->empty('Не выбрано', 0)
                        ->value($this->ppSettings->spp_start_place_type),
                ]),

                Select::make('queue.spp_user_id')
                    ->title(__('Конкретный пользователь:'))
                    ->fromModel(User::where('parent_id', $currentUser->id)->where('domain_id', $currentUser->domain_id), 'name', 'id')
                    ->empty('Не выбрано', 0)
                    ->value($this->ppSettings->spp_user_id),

                Input::make('queue.spp_ds_id')
                    ->title(__('Служба доставки:'))
                    ->type('number')
                    ->value($this->ppSettings->spp_ds_id),



                Button::make('Сохранить изменения')
                    ->class('btn btn-primary d-block mx-auto')
                    ->method('saveChanges')
                    ->parameters([
                        '_token' => csrf_token(),
                    ]),

            ]),
        ];
    }


    public function saveChanges(Request $request)
    {
        $data = $request->input('queue');

        $pp = rwSettingsProcPacking::find($data['spp_id']);

        $pp->update([
            'spp_name' => $data['spp_name'] ?? $pp->spp_name,
            'spp_priority' => $data['spp_priority'] ?? $pp->spp_priority,
            'spp_wh_id' => $data['spp_wh_id'] ?? $pp->spp_wh_id,
            'spp_user_id' => $data['spp_user_id'] ?? $pp->spp_user_id,
            'spp_ds_id' => $data['spp_ds_id'] ?? $pp->spp_ds_id,
            'spp_start_place_type' => $data['spp_start_place_type'] ?? $pp->spp_start_place_type,
            'spp_packing_type' => $data['spp_packing_type'] ?? $pp->spp_packing_type,
        ]);

        Alert::success(__('Данные сохранены!'));

        return redirect()->route('platform.whmanagement.packing-process-settings.index');
    }

}