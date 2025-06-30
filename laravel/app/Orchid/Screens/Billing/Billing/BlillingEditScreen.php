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
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class BlillingEditScreen extends Screen
{
    public $billing;
    protected array $tariffTypes = [
        'accepting' => 'Приемка товара',
        'picking' => 'Подбор товара',
        'packing' => 'Упаковка заказов',
        'storage_items' => 'Хранение поэкземпларное',
        'storage_places' => 'Хранение полочное'
    ];

    public function query($billing): iterable
    {
        $this->billing = rwBillingSetting::find($billing);

        $decodedRates = json_decode($this->billing->bs_rates ?? '{}', true) ?? [];
        $result = [
            'billing' => $this->billing
        ];

        foreach ($this->tariffTypes as $key => $label) {
            $data = $decodedRates[$key] ?? [];
            $rates = collect($data['rates'] ?? [])
                ->filter(fn($v) => is_array($v)) // оставить только массивы
                ->map(fn($v, $rate) => array_merge(['rate' => $rate], $v))
                ->values()
                ->all();
            $result[$key . '_rates'] = $rates;
            $result[$key . '_code'] = $data['code'] ?? '';
            $result[$key . '_template'] = $data['template'] ?? '';
            if ($key === 'storage_places') {
                $result[$key . '_price'] = $data['price'] ?? '';
            }
        }

        return $result;
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Настройка биллинга №') . $this->billing->bs_id;
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
                        ->title(CustomTranslator::get('Название'))
                        ->asyncParameters(['billing' => $this->billing->bs_id]),

                    ModalToggle::make($this->billing->bs_data)
                        ->modal('editDateModal')
                        ->method('saveDate')
                        ->title(CustomTranslator::get('Дата'))
                        ->asyncParameters(['billing' => $this->billing->bs_id]),

                    ModalToggle::make($this->statusLabel($this->billing->bs_status))
                        ->modal('editStatusModal')
                        ->method('saveStatus')
                        ->title(CustomTranslator::get('Статус'))
                        ->asyncParameters(['billing' => $this->billing->bs_id]),
                ]),
            ]),
        ];

        $tabs[CustomTranslator::get('Что считаем')] = [
            Layout::rows([
                Label::make('info_label')
                    ->title(CustomTranslator::get('Выберите опции, которые будут считаться по данному тарифу:')),

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
                    ->placeholder(' - ' . CustomTranslator::get('Хранение товара (поэкземплярное)'))
                    ->sendTrueOrFalse(),

                CheckBox::make('storage_places')
                    ->checked($checkedFields->contains('storage_places'))
                    ->placeholder(' - ' . CustomTranslator::get('Хранение товара (полочное)'))
                    ->sendTrueOrFalse(),

                Button::make(CustomTranslator::get('Сохранить'))
                    ->method('saveBookmarks')
                    ->class('btn btn-primary btn-sm')
                    ->parameters(['billingId' => $this->billing->bs_id]),
            ]),
        ];

        // ********************************************************
        // *** Приемка товара (accepting)

        if ($checkedFields->contains('accepting')) {
            $tabs[CustomTranslator::get('Приемка товара')] = [
                Layout::rows([
                    Input::make('accepting_code')
                        ->type('string')
                        ->title(CustomTranslator::get('Код')),

                    Input::make('accepting_template')
                        ->type('string')
                        ->help(CustomTranslator::get('Используйте следующие теги') . ': {rate}, {volume}, {weight}, {price}, {currency}')
                        ->title(CustomTranslator::get('Шаблон для отчетов')),

                    Label::make('info_label')
                        ->title(CustomTranslator::get('Заполните таблицу с весогабартиными характеристиками товара и ценами. Расчет начинается с 0 объема и 0 веса. Каждый следующий объем и вес считаются от предыдущего:')),

                    Matrix::make('accepting_rates')
                        ->columns([
                            CustomTranslator::get('Тариф') => 'rate',
                            CustomTranslator::get('Объём до (см³)') => 'volume_to',
                            CustomTranslator::get('Вес до (кг.)') => 'weight_to',
                            CustomTranslator::get('Стоимость') => 'price',
                        ])
                        ->fields([
                            'rate' => Input::make()->type('string'),
                            'volume_to' => Input::make()->type('number'),
                            'weight_to' => Input::make()->type('number')->step('0.001'),
                            'price' => Input::make()->type('number')->step('0.01'),
                        ]),

                    Button::make(CustomTranslator::get('Сохранить'))
                        ->method('saveRates')
                        ->class('btn btn-primary btn-sm')
                        ->parameters(['billingId' => $this->billing->bs_id]),
                ]),
            ];
        }

        // ********************************************************
        // *** Подбор товара (picking)

        if ($checkedFields->contains('picking')) {
            $tabs[CustomTranslator::get('Подбор товара')] = [
                Layout::rows([
                    Input::make('picking_code')
                        ->type('string')
                        ->title(CustomTranslator::get('Код')),

                    Input::make('picking_template')
                        ->type('string')
                        ->help(CustomTranslator::get('Используйте следующие теги') . ': {rate}, {volume}, {weight}, {price}, {currency}')
                        ->title(CustomTranslator::get('Шаблон для отчетов')),

                    Label::make('info_label')
                        ->title(CustomTranslator::get('Заполните таблицу с весогабартиными характеристиками товара и ценами. Расчет начинается с 0 объема и 0 веса. Каждый следующий объем и вес считаются от предыдущего:')),

                    Matrix::make('picking_rates')
                        ->columns([
                            CustomTranslator::get('Тариф') => 'rate',
                            CustomTranslator::get('Объём до (см³)') => 'volume_to',
                            CustomTranslator::get('Вес до (кг.)') => 'weight_to',
                            CustomTranslator::get('Стоимость') => 'price',
                        ])
                        ->fields([
                            'rate' => Input::make()->type('string'),
                            'volume_to' => Input::make()->type('number'),
                            'weight_to' => Input::make()->type('number')->step('0.001'),
                            'price' => Input::make()->type('number')->step('0.01'),
                        ]),

                    Button::make(CustomTranslator::get('Сохранить'))
                        ->method('saveRates')
                        ->class('btn btn-primary btn-sm')
                        ->parameters(['billingId' => $this->billing->bs_id]),
                ]),
            ];
        }

        // ********************************************************
        // *** Упаковка заказов (packing)

        if ($checkedFields->contains('packing')) {
            $tabs[CustomTranslator::get('Упаковка заказов')] = [
                Layout::rows([
                    Input::make('packing_code')
                        ->type('string')
                        ->title(CustomTranslator::get('Код')),

                    Input::make('packing_template')
                        ->type('string')
                        ->help(CustomTranslator::get('Используйте следующие теги') . ': {rate}, {volume}, {weight}, {price}, {currency}')
                        ->title(CustomTranslator::get('Шаблон для отчетов')),

                    Label::make('info_label')
                        ->title(CustomTranslator::get('Заполните таблицу с весогабартиными характеристиками товара и ценами. Расчет начинается с 0 объема и 0 веса. Каждый следующий объем и вес считаются от предыдущего:')),

                    Matrix::make('packing_rates')
                        ->columns([
                            CustomTranslator::get('Тариф') => 'rate',
                            CustomTranslator::get('Объём до (см³)') => 'volume_to',
                            CustomTranslator::get('Вес до (кг.)') => 'weight_to',
                            CustomTranslator::get('Стоимость') => 'price',
                        ])
                        ->fields([
                            'rate' => Input::make()->type('string'),
                            'volume_to' => Input::make()->type('number'),
                            'weight_to' => Input::make()->type('number')->step('0.001'),
                            'price' => Input::make()->type('number')->step('0.01'),
                        ]),

                    Button::make(CustomTranslator::get('Сохранить'))
                        ->method('saveRates')
                        ->class('btn btn-primary btn-sm')
                        ->parameters(['billingId' => $this->billing->bs_id]),
                ]),
            ];
        }

        // ********************************************************
        // *** Хранение товара (поэкземплярное) (storage_items)

        if ($checkedFields->contains('storage_items')) {
            $tabs[CustomTranslator::get('Хранение поэкземпларное')] = [
                Layout::rows([
                    Input::make('storage_items_code')
                        ->type('string')
                        ->title(CustomTranslator::get('Код')),

                    Input::make('storage_items_template')
                        ->type('string')
                        ->help(CustomTranslator::get('Используйте следующие теги') . ': {rate}, {volume}, {weight}, {price}, {currency}')
                        ->title(CustomTranslator::get('Шаблон для отчетов')),

                    Label::make('info_label')
                        ->title(CustomTranslator::get('Заполните таблицу с весогабартиными характеристиками товара и ценами. Расчет начинается с 0 объема и 0 веса. Каждый следующий объем и вес считаются от предыдущего:')),

                    Matrix::make('storage_items_rates')
                        ->columns([
                            CustomTranslator::get('Тариф') => 'rate',
                            CustomTranslator::get('Объём до (см³)') => 'volume_to',
                            CustomTranslator::get('Вес до (кг.)') => 'weight_to',
                            CustomTranslator::get('Стоимость') => 'price',
                        ])
                        ->fields([
                            'rate' => Input::make()->type('string'),
                            'volume_to' => Input::make()->type('number'),
                            'weight_to' => Input::make()->type('number')->step('0.001'),
                            'price' => Input::make()->type('number')->step('0.01'),
                        ]),

                    Button::make(CustomTranslator::get('Сохранить'))
                        ->method('saveRates')
                        ->class('btn btn-primary btn-sm')
                        ->parameters(['billingId' => $this->billing->bs_id]),
                ]),
            ];
        }

        // ********************************************************
        // *** Хранение товара (полочное) (storage_places)

        if ($checkedFields->contains('storage_places')) {
            $tabs[CustomTranslator::get('Хранение полочное')] = [
                Layout::rows([
                    Input::make('storage_places_code')
                        ->type('string')
                        ->title(CustomTranslator::get('Код')),

                    Input::make('storage_places_template')
                        ->type('string')
                        ->help(CustomTranslator::get('Используйте следующие теги') . ': {count_of_cells}, {price}, {currency}')
                        ->title(CustomTranslator::get('Шаблон для отчетов')),

                    Input::make('storage_places_price')
                        ->type('string')
                        ->title(CustomTranslator::get('Стимость одной занятой полки в день')),

                    Button::make(CustomTranslator::get('Сохранить'))
                        ->method('saveRates')
                        ->class('btn btn-primary btn-sm')
                        ->parameters(['billingId' => $this->billing->bs_id]),
                ]),
            ];
        }

        return [
            Layout::tabs($tabs),

            Layout::modal('editNameModal', [
                Layout::rows([
                    Input::make('billing.bs_id')->type('hidden'),
                    Input::make('billing.bs_name')->title(CustomTranslator::get('Название'))->required(),
                ])
            ])->title(CustomTranslator::get('Редактировать название'))->applyButton(CustomTranslator::get('Сохранить'))->async('asyncGetBilling'),

            Layout::modal('editDateModal', [
                Layout::rows([
                    Input::make('billing.bs_id')->type('hidden'),
                    DateTimer::make('billing.bs_data')->title(CustomTranslator::get('Дата'))->format('Y-m-d')->required(),
                ])
            ])->title(CustomTranslator::get('Редактировать дату'))->applyButton(CustomTranslator::get('Сохранить'))->async('asyncGetBilling'),

            Layout::modal('editStatusModal', [
                Layout::rows([
                    Input::make('billing.bs_id')->type('hidden'),
                    Select::make('billing.bs_status')
                        ->title(CustomTranslator::get('Статус'))
                        ->options([
                            1 => CustomTranslator::get('Активный'),
                            2 => CustomTranslator::get('Не активный'),
                            3 => CustomTranslator::get('Удален'),
                        ])
                        ->required(),
                ])
            ])->title(CustomTranslator::get('Редактировать статус'))->applyButton(CustomTranslator::get('Сохранить'))->async('asyncGetBilling'),
        ];
    }

    public function saveRates(Request $request)
    {
        $decodedRates = [];

        foreach ($this->tariffTypes as $key => $label) {
            $rows = $request->input($key . '_rates', []);

            if ($key === 'storage_places' && !$rows && !$request->input($key . '_price')) {
                continue;
            }

            if (!$rows && $key !== 'storage_places') {
                continue;
            }

            $prevVolume = 0;
            $prevWeight = 0;

            foreach ($rows as $index => $row) {
                $vTo = (float)($row['volume_to'] ?? 0);
                $wTo = (float)($row['weight_to'] ?? 0);

                if ($vTo <= $prevVolume) {
                    Alert::error(CustomTranslator::get("{$key} строка " . ($index + 1) . ": «Объём до» ($vTo) должен быть больше предыдущего значения ($prevVolume)."));
                    return;
                }

                if ($wTo <= $prevWeight) {
                    Alert::error(CustomTranslator::get("{$key} строка " . ($index + 1) . ": «Вес до» ($wTo) должен быть больше предыдущего значения ($prevWeight)."));
                    return;
                }

                $prevVolume = $vTo;
                $prevWeight = $wTo;
            }

            $section = [
                'code' => $request->input($key . '_code'),
                'template' => $request->input($key . '_template'),
            ];

            if ($key === 'storage_places') {
                $section['price'] = $request->input($key . '_price');
            } else {
                foreach ($rows as $row) {
                    $rateKey = $row['rate'];
                    unset($row['rate']);
                    $section['rates'][$rateKey] = $row;
                }
            }

            $decodedRates[$key] = $section;
        }

        rwBillingSetting::findOrFail($request->billingId)
            ->update(['bs_rates' => json_encode($decodedRates, JSON_UNESCAPED_UNICODE)]);

        Alert::success(CustomTranslator::get('Настройки биллинга обновлены'));
    }

    public function saveBookmarks(Request $request)
    {
        $checkboxes = ['accepting', 'picking', 'packing', 'storage_items', 'storage_places'];

        $checked = collect($checkboxes)
            ->filter(fn($field) => $request->input($field) == '1');

        $fieldsString = $checked->implode(',');

        rwBillingSetting::findOrFail($request->input('billingId'))
            ->update(['bs_fields' => $fieldsString]);

        Alert::success(CustomTranslator::get('Настройки биллинга обновлены'));
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

        Alert::success(CustomTranslator::get('Название обновлено'));
    }

    public function saveDate(Request $request)
    {
        rwBillingSetting::findOrFail($request->input('billing.bs_id'))
            ->update(['bs_data' => $request->input('billing.bs_data')]);

        Alert::success(CustomTranslator::get('Дата обновлена'));
    }

    public function saveStatus(Request $request)
    {
        rwBillingSetting::findOrFail($request->input('billing.bs_id'))
            ->update(['bs_status' => $request->input('billing.bs_status')]);

        Alert::success(CustomTranslator::get('Статус обновлен'));
    }

    private function statusLabel($status): string
    {
        return match ((int)$status) {
            1 => CustomTranslator::get('Активный'),
            2 => CustomTranslator::get('Не активный'),
            3 => CustomTranslator::get('Удален'),
            default => CustomTranslator::get('Неизвестно'),
        };
    }
}
