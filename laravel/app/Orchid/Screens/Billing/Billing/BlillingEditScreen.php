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
use Orchid\Screen\TD;
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
            $result[$key . '_datamatrix'] = $data['datamatrix'] ?? '';
            $result[$key . '_template'] = $data['template'] ?? '';
            $result[$key . '_proc_dimensions'] = $data['proc_dimensions'] ?? '';
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

                    ModalToggle::make($this->statusDateType($this->billing->bs_date_type))
                        ->modal('editDataTypeModal')
                        ->method('saveDateType')
                        ->title(CustomTranslator::get('Как считается дата'))
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

                    Input::make('accepting_datamatrix')
                        ->type('number')
                        ->step('0.01')
                        ->help(CustomTranslator::get('Укажите стоимость только если используете DataMatrix при приемки товара.'))
                        ->title(CustomTranslator::get('Стоимость приемки товара с DataMatrix')),

                    Input::make('accepting_template')
                        ->type('string')
                        ->title(CustomTranslator::get('Шаблон для отчетов')),

                    Label::make('tag_description')
                        ->value(CustomTranslator::get('Используйте следующие теги для построения шаблона для отчетов:'))
                        ->style('font-weight: bold; adding-top: 20px;'),

                    Label::make('tag_description')
                        ->value("{doc_id} — " . CustomTranslator::get('Номер документа'))
                        ->style('margin-top: 0px; margin-bottom: 0px; padding-left: 30px;'),

                    Label::make('tag_description')
                        ->value("{count_offers} — " . CustomTranslator::get('Количество товаров в документе'))
                        ->style('margin-top: 0px; margin-bottom: 0px; padding-left: 30px;'),

                    Label::make('tag_description')
                        ->value("{rate} — " . CustomTranslator::get('Тариф'))
                        ->style('margin-top: 0px; margin-bottom: 0px; padding-left: 30px;'),

                    Label::make('tag_description')
                        ->value("{sum} — " . CustomTranslator::get('Начисленная сумма'))
                        ->style('margin-top: 0px; margin-bottom: 0px; padding-left: 30px;'),

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

                    Input::make('picking_datamatrix')
                        ->type('number')
                        ->step('0.01')
                        ->help(CustomTranslator::get('Укажите стоимость только если используете DataMatrix при сборке товара.'))
                        ->title(CustomTranslator::get('Стоимость приемки товара с DataMatrix')),

                    Input::make('picking_template')
                        ->type('string')
                        ->title(CustomTranslator::get('Шаблон для отчетов')),

                    Label::make('tag_description')
                        ->value(CustomTranslator::get('Используйте следующие теги для построения шаблона для отчетов:'))
                        ->style('font-weight: bold; adding-top: 20px;'),

                    Label::make('tag_description')
                        ->value("{doc_id} — " . CustomTranslator::get('Номер документа'))
                        ->style('margin-top: 0px; margin-bottom: 0px; padding-left: 30px;'),

                    Label::make('tag_description')
                        ->value("{count_offers} — " . CustomTranslator::get('Количество товаров в документе'))
                        ->style('margin-top: 0px; margin-bottom: 0px; padding-left: 30px;'),

                    Label::make('tag_description')
                        ->value("{rate} — " . CustomTranslator::get('Тариф'))
                        ->style('margin-top: 0px; margin-bottom: 0px; padding-left: 30px;'),

                    Label::make('tag_description')
                        ->value("{sum} — " . CustomTranslator::get('Начисленная сумма'))
                        ->style('margin-top: 0px; margin-bottom: 0px; padding-left: 30px;'),

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

                    Input::make('packing_proc_dimensions')
                        ->type('number')
                        ->step('1')
                        ->help(CustomTranslator::get('В случе если у заказа не заданы габариты, то система посчитает сумму габаритов товара этого заказа и увеличит полученную сумму на заданный выше процент.'))
                        ->title(CustomTranslator::get('Укажите процент надбавки на габариты товара')),

                    Input::make('packing_datamatrix')
                        ->type('number')
                        ->step('0.01')
                        ->help(CustomTranslator::get('Укажите стоимость только если используете DataMatrix при упаковки товара.'))
                        ->title(CustomTranslator::get('Стоимость приемки товара с DataMatrix')),

                    Input::make('packing_template')
                        ->type('string')
                        ->title(CustomTranslator::get('Шаблон для отчетов')),

                    Label::make('tag_description')
                        ->value(CustomTranslator::get('Используйте следующие теги для построения шаблона для отчетов:'))
                        ->style('font-weight: bold; adding-top: 20px;'),

                    Label::make('tag_description')
                        ->value("{doc_id} — " . CustomTranslator::get('Номер документа'))
                        ->style('margin-top: 0px; margin-bottom: 0px; padding-left: 30px;'),

                    Label::make('tag_description')
                        ->value("{rate} — " . CustomTranslator::get('Тариф'))
                        ->style('margin-top: 0px; margin-bottom: 0px; padding-left: 30px;'),

                    Label::make('tag_description')
                        ->value("{doc_weight} — " . CustomTranslator::get('Вес заказа (объемный или физический) используемый в расчетах (sm3 или kg будут добавлены автоматически).'))
                        ->style('margin-top: 0px; margin-bottom: 0px; padding-left: 30px;'),

                    Label::make('tag_description')
                        ->value("{sum} — " . CustomTranslator::get('Начисленная сумма'))
                        ->style('margin-top: 0px; margin-bottom: 0px; padding-left: 30px;'),

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

            Layout::modal('editDataTypeModal', [
                Layout::rows([
                    Input::make('billing.bs_id')->type('hidden'),
                    Select::make('billing.bs_date_type')
                        ->title(CustomTranslator::get('Как считается дата'))
                        ->options([
                            0 => CustomTranslator::get('Дата документа'),
                            1 => CustomTranslator::get('Текущая дата'),
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
                'datamatrix' => $request->input($key . '_datamatrix'),
                'template' => $request->input($key . '_template'),
                'proc_dimensions' => $request->input($key . '_proc_dimensions'),
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

    public function saveDateType(Request $request)
    {
        rwBillingSetting::findOrFail($request->input('billing.bs_id'))
            ->update(['bs_date_type' => $request->input('billing.bs_date_type')]);

        Alert::success(CustomTranslator::get('Тип расчета даты обновлен'));
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

    private function statusDateType($status): string
    {
        return match ((int)$status) {
            0 => CustomTranslator::get('Дата документа'),
            1 => CustomTranslator::get('Текущая дата'),
            default => CustomTranslator::get('Неизвестно'),
        };
    }
}
