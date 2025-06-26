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
use Orchid\Screen\Fields\Matrix;
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

        // Безопасно декодируем JSON или получаем пустой массив
        $decodedRates = json_decode($this->billing->bs_rates ?? '{}', true) ?? [];

        // Обработка "accepting"
        $accepting = collect($decodedRates['accepting'] ?? [])
            ->map(fn($v,$rate) => array_merge(['rate'=>$rate], $v))
            ->values()
            ->all();

        return [
            'billing'         => $this->billing,
            'accepting_rates' => $accepting,   // <-- новое имя
        ];
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
        $checkedFields = collect(explode(',', $this->billing->bs_fields ?? ''));

        $tabs[CustomTranslator::get('Основное')] = [
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

        $tabs[CustomTranslator::get('Что считаем')] = [
            Layout::rows([
                Label::make('info_label')
                    ->title('Выберите опции, которые будут считаться по данному тарифу:'),

                CheckBox::make('accepting')
                    ->checked($checkedFields->contains('accepting'))
                    ->placeholder(' - ' . CustomTranslator::get('Приемка товара'))
                    ->help(CustomTranslator::get('Расчет стоимости приемки товара на склад.'))
                    ->sendTrueOrFalse(),

                CheckBox::make('picking')
                    ->checked($checkedFields->contains('picking'))
                    ->placeholder(' - ' . CustomTranslator::get('Подбор товара'))
                    ->sendTrueOrFalse(),

                CheckBox::make('packing')
                    ->checked($checkedFields->contains('packing'))
                    ->placeholder(' - ' . CustomTranslator::get('Упаковка заказов'))
                    ->sendTrueOrFalse(),

                CheckBox::make('storage_items')
                    ->checked($checkedFields->contains('storage_items'))
                    ->placeholder(' - ' . CustomTranslator::get('Хранение товара (по экземплярное)'))
                    ->sendTrueOrFalse(),

                CheckBox::make('storage_places')
                    ->checked($checkedFields->contains('storage_places'))
                    ->placeholder(' - ' . CustomTranslator::get('Хранение товара (полочное)'))
                    ->sendTrueOrFalse(),

                Button::make(CustomTranslator::get('Сохранить'))
                    ->method('saveBookmarks')
                    ->class('btn btn-primary btn-sm')
                    ->parameters([
                        'billingId' => $this->billing->bs_id,
                    ]),
            ]),

        ];

        // Блок приемки
        if ($checkedFields->contains('accepting')) {
            $tabs[CustomTranslator::get('Приемка товара')] = [
                Layout::rows([
                    Label::make('info_label')
                        ->title('Заполните таблицу с весогабартиными характеристиками товара и ценами. Расчет начинается с 0 объема и 0 веса. Каждый следующей объем и вес считаются от предыдущего:'),

                    Matrix::make('accepting_rates')        // ← новое имя
                    ->columns([
                        'Тариф'              => 'rate',
                        'Объём до (см³)'     => 'volume_to',
                        'Вес до (г)'         => 'weight_to',
                        'Стоимость'          => 'price',
                    ])
                        ->fields([
                            'rate'        => Input::make()->type('string'),
                            'volume_to'   => Input::make()->type('number'),
                            'weight_to'   => Input::make()->type('number'),
                            'price'       => Input::make()->type('number'),
                        ]),

                    Button::make(CustomTranslator::get('Сохранить'))
                        ->method('saveRates')
                        ->class('btn btn-primary btn-sm')
                        ->parameters([
                            'billingId' => $this->billing->bs_id,
                        ]),
                ]),

            ];
        }

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

    public function saveRates(Request $request)
    {
        $arRates = [];

        if ($rows = $request->input('accepting_rates')) {   // ← новое имя
            $arRates['accepting'] = [];
            foreach ($rows as $row) {
                $key = $row['rate'];
                unset($row['rate']);
                $arRates['accepting'][$key] = $row;
            }
        }

        rwBillingSetting::findOrFail($request->billingId)
            ->update(['bs_rates' => json_encode($arRates, JSON_UNESCAPED_UNICODE)]);

        Alert::success('Настройки биллинга обновлены');
    }

    public function saveBookmarks(Request $request)
    {
        // Список всех возможных чекбоксов
        $checkboxes = [
            'accepting',
            'picking',
            'packing',
            'storage_items',
            'storage_places',
        ];

        // Оставляем только те, которые реально установлены (== '1' или true)
        $checked = collect($checkboxes)
            ->filter(fn($field) => $request->input($field) == '1');

        // Преобразуем в строку
        $fieldsString = $checked->implode(',');

        // Сохраняем
        rwBillingSetting::findOrFail($request->input('billingId'))
            ->update(['bs_fields' => $fieldsString]);

        Alert::success('Настройки биллинга обновлены');
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
