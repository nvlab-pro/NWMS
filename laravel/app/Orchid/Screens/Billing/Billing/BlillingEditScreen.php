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

        $mainIcon = '<span  style="padding-right: 10px;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-currency-exchange" viewBox="0 0 16 16">
  <path d="M0 5a5 5 0 0 0 4.027 4.905 6.5 6.5 0 0 1 .544-2.073C3.695 7.536 3.132 6.864 3 5.91h-.5v-.426h.466V5.05q-.001-.07.004-.135H2.5v-.427h.511C3.236 3.24 4.213 2.5 5.681 2.5c.316 0 .59.031.819.085v.733a3.5 3.5 0 0 0-.815-.082c-.919 0-1.538.466-1.734 1.252h1.917v.427h-1.98q-.004.07-.003.147v.422h1.983v.427H3.93c.118.602.468 1.03 1.005 1.229a6.5 6.5 0 0 1 4.97-3.113A5.002 5.002 0 0 0 0 5m16 5.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0m-7.75 1.322c.069.835.746 1.485 1.964 1.562V14h.54v-.62c1.259-.086 1.996-.74 1.996-1.69 0-.865-.563-1.31-1.57-1.54l-.426-.1V8.374c.54.06.884.347.966.745h.948c-.07-.804-.779-1.433-1.914-1.502V7h-.54v.629c-1.076.103-1.808.732-1.808 1.622 0 .787.544 1.288 1.45 1.493l.358.085v1.78c-.554-.08-.92-.376-1.003-.787zm1.96-1.895c-.532-.12-.82-.364-.82-.732 0-.41.311-.719.824-.809v1.54h-.005zm.622 1.044c.645.145.943.38.943.796 0 .474-.37.8-1.02.86v-1.674z"/>
</svg></span>';

        $selectIcon = '<span  style="padding-right: 10px;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-tools" viewBox="0 0 16 16">
  <path d="M1 0 0 1l2.2 3.081a1 1 0 0 0 .815.419h.07a1 1 0 0 1 .708.293l2.675 2.675-2.617 2.654A3.003 3.003 0 0 0 0 13a3 3 0 1 0 5.878-.851l2.654-2.617.968.968-.305.914a1 1 0 0 0 .242 1.023l3.27 3.27a.997.997 0 0 0 1.414 0l1.586-1.586a.997.997 0 0 0 0-1.414l-3.27-3.27a1 1 0 0 0-1.023-.242L10.5 9.5l-.96-.96 2.68-2.643A3.005 3.005 0 0 0 16 3q0-.405-.102-.777l-2.14 2.141L12 4l-.364-1.757L13.777.102a3 3 0 0 0-3.675 3.68L7.462 6.46 4.793 3.793a1 1 0 0 1-.293-.707v-.071a1 1 0 0 0-.419-.814zm9.646 10.646a.5.5 0 0 1 .708 0l2.914 2.915a.5.5 0 0 1-.707.707l-2.915-2.914a.5.5 0 0 1 0-.708M3 11l.471.242.529.026.287.445.445.287.026.529L5 13l-.242.471-.026.529-.445.287-.287.445-.529.026L3 15l-.471-.242L2 14.732l-.287-.445L1.268 14l-.026-.529L1 13l.242-.471.026-.529.445-.287.287-.445.529-.026z"/>
</svg></span>';

        $acceptanceIcon = '<span  style="padding-right: 10px;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-building-add" viewBox="0 0 16 16">
  <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0"/>
  <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6.5a.5.5 0 0 1-1 0V1H3v14h3v-2.5a.5.5 0 0 1 .5-.5H8v4H3a1 1 0 0 1-1-1z"/>
  <path d="M4.5 2a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-6 3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-6 3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5z"/>
</svg></span>';

        $pickingIcon = '<span  style="padding-right: 10px;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-dropbox" viewBox="0 0 16 16">
  <path d="M8.01 4.555 4.005 7.11 8.01 9.665 4.005 12.22 0 9.651l4.005-2.555L0 4.555 4.005 2zm-4.026 8.487 4.006-2.555 4.005 2.555-4.005 2.555zm4.026-3.39 4.005-2.556L8.01 4.555 11.995 2 16 4.555 11.995 7.11 16 9.665l-4.005 2.555z"/>
</svg></span>';

        $packingIcon = '<span  style="padding-right: 10px;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-box-seam" viewBox="0 0 16 16">
  <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2zm3.564 1.426L5.596 5 8 5.961 14.154 3.5zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464z"/>
</svg></span>';

        $storageIcon = '<span  style="padding-right: 10px;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-inboxes" viewBox="0 0 16 16">
  <path d="M4.98 1a.5.5 0 0 0-.39.188L1.54 5H6a.5.5 0 0 1 .5.5 1.5 1.5 0 0 0 3 0A.5.5 0 0 1 10 5h4.46l-3.05-3.812A.5.5 0 0 0 11.02 1zm9.954 5H10.45a2.5 2.5 0 0 1-4.9 0H1.066l.32 2.562A.5.5 0 0 0 1.884 9h12.234a.5.5 0 0 0 .496-.438zM3.809.563A1.5 1.5 0 0 1 4.981 0h6.038a1.5 1.5 0 0 1 1.172.563l3.7 4.625a.5.5 0 0 1 .105.374l-.39 3.124A1.5 1.5 0 0 1 14.117 10H1.883A1.5 1.5 0 0 1 .394 8.686l-.39-3.124a.5.5 0 0 1 .106-.374zM.125 11.17A.5.5 0 0 1 .5 11H6a.5.5 0 0 1 .5.5 1.5 1.5 0 0 0 3 0 .5.5 0 0 1 .5-.5h5.5a.5.5 0 0 1 .496.562l-.39 3.124A1.5 1.5 0 0 1 14.117 16H1.883a1.5 1.5 0 0 1-1.489-1.314l-.39-3.124a.5.5 0 0 1 .121-.393zm.941.83.32 2.562a.5.5 0 0 0 .497.438h12.234a.5.5 0 0 0 .496-.438l.32-2.562H10.45a2.5 2.5 0 0 1-4.9 0z"/>
</svg></span>';

        $tabs[$mainIcon . CustomTranslator::get('Основное')] = [
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

        $tabs[$selectIcon . CustomTranslator::get('Что считаем')] = [
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
            $tabs[$acceptanceIcon . CustomTranslator::get('Приемка товара')] = [
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
            $tabs[$pickingIcon . CustomTranslator::get('Подбор товара')] = [
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
            $tabs[$packingIcon . CustomTranslator::get('Упаковка заказов')] = [
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
            $tabs[$storageIcon . CustomTranslator::get('Хранение поэкземпларное')] = [
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
            $tabs[$storageIcon . CustomTranslator::get('Хранение полочное')] = [
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
