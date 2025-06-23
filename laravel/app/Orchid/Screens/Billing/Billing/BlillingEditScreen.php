<?php

namespace App\Orchid\Screens\Billing\Billing;

use App\Models\rwBillingSetting;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Support\Color;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class BlillingEditScreen extends Screen
{
    public $billing;

    public function query($billing): iterable
    {
        $this->billing = rwBillingSetting::find($billing);
        return ['billing' => $this->billing];
    }

    public function name(): ?string
    {
        return 'Настройка биллинга №' . $this->billing->bs_id;
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {

        $tabs['Основное'] = [
            Layout::rows([
                Group::make([
                    ModalToggle::make($this->billing->bs_name)
                        ->modal('editNameModal')
                        ->method('saveName')
                        ->title('Название')
                        ->asyncParameters(['billing' => $this->billing->bs_id]),

                    ModalToggle::make($this->billing->bs_data)
                        ->modal('editDateModal')
                        ->method('saveDate')
                        ->title('Дата')
                        ->asyncParameters(['billing' => $this->billing->bs_id]),

                    ModalToggle::make($this->statusLabel($this->billing->bs_status))
                        ->modal('editStatusModal')
                        ->method('saveStatus')
                        ->title('Статус')
                        ->asyncParameters(['billing' => $this->billing->bs_id]),
                ]),
            ]),
        ];

        $tabs['Что считаем'] = [
            Layout::rows([
                Label::make('info_label')
                    ->title('Выберите опции, которые будут считаться по данному тарифу:'),

                CheckBox::make('accepting_calc')
                    ->value(0)
                    ->placeholder(' - ' . CustomTranslator::get('Приемка товара'))
                    ->help(CustomTranslator::get('Расчет стоимости приемки товара на склад.'))

                    ->sendTrueOrFalse(),

                CheckBox::make('accepting_calc')
                    ->value(0)
                    ->placeholder(' - ' . CustomTranslator::get('Подбор товара'))
                    ->sendTrueOrFalse(),

                CheckBox::make('accepting_calc')
                    ->value(0)
                    ->placeholder(' - ' . CustomTranslator::get('Упаковка заказов'))
                    ->sendTrueOrFalse(),

                CheckBox::make('accepting_calc')
                    ->value(0)
                    ->placeholder(' - ' . CustomTranslator::get('Хранение товара (поэкземплярное)'))
                    ->sendTrueOrFalse(),

                CheckBox::make('accepting_calc')
                    ->value(0)
                    ->placeholder(' - ' . CustomTranslator::get('Хранение товара (полочное)'))
                    ->sendTrueOrFalse(),
            ]),
        ];

        return [
            Layout::tabs($tabs),

            Layout::modal('editNameModal', [
                Layout::rows([
                    Input::make('billing.bs_id')->type('hidden'),
                    Input::make('billing.bs_name')->title('Название')->required(),
                ])
            ])->title('Редактировать название')->applyButton('Сохранить')->async('asyncGetBilling'),

            Layout::modal('editDateModal', [
                Layout::rows([
                    Input::make('billing.bs_id')->type('hidden'),
                    DateTimer::make('billing.bs_data')->title('Дата')->format('Y-m-d')->required(),
                ])
            ])->title('Редактировать дату')->applyButton('Сохранить')->async('asyncGetBilling'),

            Layout::modal('editStatusModal', [
                Layout::rows([
                    Input::make('billing.bs_id')->type('hidden'),
                    Select::make('billing.bs_status')
                        ->title('Статус')
                        ->options([
                            1 => 'Активный',
                            2 => 'Не активный',
                            3 => 'Удален',
                        ])
                        ->required(),
                ])
            ])->title('Редактировать статус')->applyButton('Сохранить')->async('asyncGetBilling'),
        ];
    }

    public function asyncGetBilling(Request $request): iterable
    {
        $billing = rwBillingSetting::findOrFail($request->input('billing'));
        return ['billing' => $billing];
    }

    public function saveName(Request $request)
    {
        rwBillingSetting::findOrFail($request->input('billing.bs_id'))
            ->update(['bs_name' => $request->input('billing.bs_name')]);

        Alert::success('Название обновлено');
    }

    public function saveDate(Request $request)
    {
        rwBillingSetting::findOrFail($request->input('billing.bs_id'))
            ->update(['bs_data' => $request->input('billing.bs_data')]);

        Alert::success('Дата обновлена');
    }

    public function saveStatus(Request $request)
    {
        rwBillingSetting::findOrFail($request->input('billing.bs_id'))
            ->update(['bs_status' => $request->input('billing.bs_status')]);

        Alert::success('Статус обновлен');
    }

    private function statusLabel($status): string
    {
        return match ((int)$status) {
            1 => 'Активный',
            2 => 'Не активный',
            3 => 'Удален',
            default => 'Неизвестно',
        };
    }
}
