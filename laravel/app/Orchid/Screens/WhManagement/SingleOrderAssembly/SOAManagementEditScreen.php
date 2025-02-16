<?php

namespace App\Orchid\Screens\WhManagement\SingleOrderAssembly;

use App\Models\rwSettingsSoa;
use App\Models\rwWarehouse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Group;
use Illuminate\Http\Request;

class SOAManagementEditScreen extends Screen
{

    private $soaSettings;

    public function query($soaId): iterable
    {

        $this->soaSettings = rwSettingsSoa::where('ssoa_id', $soaId)
            ->first();

        return [
            'dbSoaSettings' => $this->soaSettings,
        ];
    }

    public function name(): ?string
    {
        return __('Очередь: ') . $this->soaSettings->ssoa_id;
    }

    public function description(): string
    {
        return $this->soaSettings->ssoa_name;
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

                Input::make('queue.ssoa_id')
                    ->hidden()
                    ->value($this->soaSettings->ssoa_id),

                Input::make('queue.ssoa_name')
                    ->title('Название очереди:')
                    ->value($this->soaSettings->ssoa_name)
                    ->required(),

                Input::make('queue.ssoa_priority')
                    ->title('Приоритет (чем больше, тем выше):')
                    ->type('number')
                    ->min(0)
                    ->max(1000)
                    ->value($this->soaSettings->ssoa_priority)
                    ->required(),

                Select::make('queue.ssoa_wh_id')
                    ->title(__('Склад:'))
                    ->fromModel(rwWarehouse::where('wh_type', 2)->where('wh_domain_id', $currentUser->domain_id), 'wh_name', 'wh_id')
                    ->empty('Не выбрано', 0)
                    ->value($this->soaSettings->ssoa_wh_id),

                Select::make('queue.ssoa_user_id')
                    ->title(__('Конкретный пользователь:'))
                    ->fromModel(User::where('parent_id', $currentUser->id)->where('domain_id', $currentUser->domain_id), 'name', 'id')
                    ->empty('Не выбрано', 0)
                    ->value($this->soaSettings->ssoa_user_id),

                Input::make('queue.ssoa_ds_id')
                    ->title(__('Служба доставки:'))
                    ->type('number')
                    ->value($this->soaSettings->ssoa_ds_id),

                Group::make([
                    Input::make('queue.ssoa_date_from')
                        ->title(__('Дата от:'))
                        ->type('date')
                        ->value($this->soaSettings->ssoa_date_from)
                        ->required(),

                    Input::make('queue.ssoa_date_to')
                        ->title(__('Дата до:'))
                        ->type('date')
                        ->value($this->soaSettings->ssoa_date_to)
                        ->required(),
                ]),

                Group::make([
                    Input::make('queue.ssoa_offers_count_from')
                        ->title(__('Количество товаров в заказе от:'))
                        ->type('number')
                        ->value($this->soaSettings->ssoa_offers_count_from),

                    Input::make('queue.ssoa_offers_count_to')
                        ->title(__('Количество товаров в заказе до:'))
                        ->type('number')
                        ->value($this->soaSettings->ssoa_offers_count_to),
                ]),

                Group::make([
                    Input::make('queue.ssoa_order_from')
                        ->title(__('ID заказа от:'))
                        ->type('number')
                        ->value($this->soaSettings->ssoa_order_from),

                    Input::make('queue.ssoa_order_to')
                        ->title(__('ID заказа до:'))
                        ->type('number')
                        ->value($this->soaSettings->ssoa_order_to),
                ]),

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

        $soa = rwSettingsSoa::find($data['ssoa_id']);

        $soa->update([
            'ssoa_name' => $data['ssoa_name'] ?? $soa->ssoa_name,
            'ssoa_priority' => $data['ssoa_priority'] ?? $soa->ssoa_priority,
            'ssoa_wh_id' => $data['ssoa_wh_id'] ?? $soa->ssoa_wh_id,
            'ssoa_user_id' => $data['ssoa_user_id'] ?? $soa->ssoa_user_id,
            'ssoa_ds_id' => $data['ssoa_ds_id'] ?? $soa->ssoa_ds_id,
            'ssoa_date_from' => $data['ssoa_date_from'] ?? $soa->ssoa_date_from,
            'ssoa_date_to' => $data['ssoa_date_to'] ?? $soa->ssoa_date_to,
            'ssoa_offers_count_from' => $data['ssoa_offers_count_from'] ?? $soa->ssoa_offers_count_from,
            'ssoa_offers_count_to' => $data['ssoa_offers_count_to'] ?? $soa->ssoa_offers_count_to,
            'ssoa_order_from' => $data['ssoa_order_from'] ?? $soa->ssoa_order_from,
            'ssoa_order_to' => $data['ssoa_order_to'] ?? $soa->ssoa_order_to,
        ]);

        Alert::success(__('Данные волны сохранены!'));

        return redirect()->route('platform.whmanagement.single-order-assembly.index');
    }

}
