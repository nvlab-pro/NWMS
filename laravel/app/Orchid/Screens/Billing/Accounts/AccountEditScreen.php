<?php

namespace App\Orchid\Screens\Billing\Accounts;

use App\Models\rwBillingTransactions;
use App\Models\rwCompany;
use App\Models\rwInvoce;
use App\Models\rwLibActionType;
use App\Models\rwWarehouse;
use App\Models\rwWhBilling;
use App\Orchid\Layouts\Billings\Accounts\AccountEditTable;
use App\Orchid\Layouts\Billings\Accounts\AccountTotalTable;
use App\Orchid\Layouts\Billings\Accounts\CustomerCompanyTable;
use App\Orchid\Layouts\Billings\Accounts\InvoceTable;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Sight;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Screen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Alert;

class AccountEditScreen extends Screen
{
    private $whId;

    public function query($whId): iterable
    {
        $this->whId = $whId;
        $transactionsList = [];
        $companyRequisites = [];

        $currentUser = Auth::user();

        $dbWhList = rwWarehouse::where('wh_id', $this->whId);

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {


        } else {

            $dbWhList = $dbWhList->where('wh_user_id', $currentUser->id);

        }

        $resWhList = $dbWhList->with(['getCompany', 'getParent.getCompany'])
            ->first();

        if (isset($resWhList->wh_id)) {

            $transactionsList = rwBillingTransactions::where('bt_wh_id', $this->whId)
                ->with(['actionType', 'customerCompany', 'executorCompany'])
                ->orderBy('bt_date', 'desc')
                ->filters()
                ->paginate(100);

            $totalTransList = rwBillingTransactions::select(
                'bt_service_id',
                DB::raw('SUM(bt_total_sum) as total_sum')
            )
                ->where('bt_wh_id', $this->whId)
                ->with(['actionType'])
                ->groupBy('bt_service_id')
                ->filters()
                ->get();

            $invoicesList = rwInvoce::where('in_wh_id', $this->whId)
                ->with(['getCustomerCompany', 'getExecutorCompany'])
                ->paginate(100);

            $companyRequisites = $resWhList->getCompany;
            $executorRequisites = $resWhList->getParent->getCompany;

        }

        return [
            'transactionsList' => $transactionsList,
            'totalTransList' => $totalTransList,
            'invoicesList' => $invoicesList,
            'companyRequisites' => $companyRequisites,
            'executorRequisites' => $executorRequisites,
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Детали счета');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            // ***********************
            // Диалоговые окна

            // Редактирование транзакции
            Layout::modal('editTransactionModal', [
                Layout::rows([
                    Input::make('id')
                        ->type('text'),

                    Input::make('bt_sum')
                        ->title(CustomTranslator::get('Сумма'))
                        ->type('number')
                        ->step('any')
                        ->required(),

                    Input::make('bt_desc')
                        ->title(CustomTranslator::get('Описание'))
                        ->type('text'),
                    ])
                ])
                ->title(CustomTranslator::get('Редактировать транзакцию'))
                ->applyButton(CustomTranslator::get('Сохранить'))
                ->method('editTransaction')
                ->async('asyncGetTransaction'),

            // Создание транзакции
            Layout::modal('createTransactionModal', [
                Layout::rows([
                    Input::make('whId')
                        ->value($this->whId)
                        ->type('hidden'),

                    Select::make('bt_service_id')
                        ->fromModel(rwLibActionType::class, 'lat_name', 'lat_id')
                        ->title(CustomTranslator::get('Тип транзакции'))
                        ->required(),

                    Input::make('bt_sum')
                        ->title(CustomTranslator::get('Сумма'))
                        ->type('number')
                        ->step('any')
                        ->required(),

                    Input::make('bt_desc')
                        ->title(CustomTranslator::get('Описание'))
                        ->type('text'),
                    ])
                ])
                ->title(CustomTranslator::get('Добавить транзакцию'))
                ->applyButton(CustomTranslator::get('Сохранить')),

            // Диалоговое окно добавления счёта
            Layout::modal('createInvoceModal', [
                Layout::rows([
                    Input::make('whId')
                        ->value($this->whId)
                        ->type('hidden'),

                    Input::make('in_sum')
                        ->title(CustomTranslator::get('Сумма без учета НДС'))
                        ->type('number')
                        ->step('any')
                        ->required(),
                ])
            ])
                ->title(CustomTranslator::get('Добавить счёт'))
                ->applyButton(CustomTranslator::get('Сохранить'))
                ->method('createInvoce'),

            // ***********************
            // Закладки

            Layout::tabs([

                // Список начислений
                CustomTranslator::get('Список начислений') => [
                    Layout::rows([
                        ModalToggle::make(CustomTranslator::get('Добавить транзакцию'))
                            ->modal('createTransactionModal')
                            ->method('createTransaction')
                            ->style('margin-left: auto;')
                            ->icon('plus')
                            ->type(Color::SUCCESS),

                    ]),
                    AccountEditTable::class,
                ],

                // Счет
                CustomTranslator::get('Счета') => [
                    Layout::rows([
                        ModalToggle::make(CustomTranslator::get('Добавить счет'))
                            ->modal('createInvoceModal')
                            ->method('createInvoce')
                            ->style('margin-left: auto;')
                            ->icon('receipt')
                            ->type(Color::PRIMARY),

                    ]),

                    InvoceTable::class,
                ],

                // Акты
                CustomTranslator::get('Акты') => [
                    AccountEditTable::class,
                ],

                // Итого
                CustomTranslator::get('Итого') => [
                    AccountTotalTable::class,
                ],

                // Текущие тарифы клиента
                CustomTranslator::get('Текущие тарифы') => [
                    // тут будут тарифы
                ],

                CustomTranslator::get('Реквизиты') => [
                    Layout::columns([
                        // --- левая колонка: клиент -----------------
                        Layout::legend('companyRequisites', [
                            Sight::make('co_name', CustomTranslator::get('Название')),
                            Sight::make('co_legal_name', CustomTranslator::get('Юридическое название')),
                            Sight::make('co_vat_number', CustomTranslator::get('ИНН / VAT')),
                            Sight::make('co_vat_availability', CustomTranslator::get('НДС'))
                                ->render(fn($c) => $c->co_vat_availability ? 'С НДС' : 'Без НДС'),
                            Sight::make('co_vat_proc', CustomTranslator::get('Ставка НДС'))
                                ->render(fn($c) => $c->co_vat_proc . ' %'),
                            Sight::make('co_registration_number', CustomTranslator::get('Регистрационный номер')),
                            Sight::make('co_country_id', CustomTranslator::get('Страна')),
                            Sight::make('co_city_id', CustomTranslator::get('Город'))
                                ->render(fn($c) => $c->getCity->lcit_name ?? '-'),
                            Sight::make('co_postcode', CustomTranslator::get('Индекс')),
                            Sight::make('co_address', CustomTranslator::get('Адрес')),
                            Sight::make('co_phone', CustomTranslator::get('Телефон')),
                            Sight::make('co_email', CustomTranslator::get('Email')),
                            Sight::make('co_website', CustomTranslator::get('Сайт')),
                            Sight::make('co_bank_account', CustomTranslator::get('Расчётный счёт')),
                            Sight::make('co_bank_ks', CustomTranslator::get('Кор.счёт')),
                            Sight::make('co_bank_name', CustomTranslator::get('Банк')),
                            Sight::make('co_swift_bic', CustomTranslator::get('SWIFT/BIC')),
                            Sight::make('co_contact_person', CustomTranslator::get('Контактное лицо')),
                        ])->title('Реквизиты клиента'),

                        // --- правая колонка: исполнитель ----------
                        Layout::legend('executorRequisites', [
                            Sight::make('co_name', CustomTranslator::get('Название')),
                            Sight::make('co_legal_name', CustomTranslator::get('Юридическое название')),
                            Sight::make('co_vat_number', CustomTranslator::get('ИНН / VAT')),
                            Sight::make('co_vat_availability', CustomTranslator::get('НДС'))
                                ->render(fn($c) => $c->co_vat_availability ? 'С НДС' : 'Без НДС'),
                            Sight::make('co_vat_proc', CustomTranslator::get('Ставка НДС'))
                                ->render(fn($c) => $c->co_vat_proc . ' %'),
                            Sight::make('co_registration_number', CustomTranslator::get('Регистрационный номер')),
                            Sight::make('co_country_id', CustomTranslator::get('Страна')),
                            Sight::make('co_city_id', CustomTranslator::get('Город'))
                                ->render(fn($c) => $c->getCity->lcit_name ?? '-'),
                            Sight::make('co_postcode', CustomTranslator::get('Индекс')),
                            Sight::make('co_address', CustomTranslator::get('Адрес')),
                            Sight::make('co_phone', CustomTranslator::get('Телефон')),
                            Sight::make('co_email', CustomTranslator::get('Email')),
                            Sight::make('co_website', CustomTranslator::get('Сайт')),
                            Sight::make('co_bank_account', CustomTranslator::get('Расчётный счёт')),
                            Sight::make('co_bank_ks', CustomTranslator::get('Кор.счёт')),
                            Sight::make('co_bank_name', CustomTranslator::get('Банк')),
                            Sight::make('co_swift_bic', CustomTranslator::get('SWIFT/BIC')),
                            Sight::make('co_contact_person', CustomTranslator::get('Контактное лицо')),
                        ])->title('Реквизиты исполнителя'),
                    ]),
                ],


            ]),
        ];
    }

    // ************************************************************************
    // *** Блок асинхронной выдачи данных для диалоговых окон
    // ************************************************************************

    public function asyncGetTransaction(Request $request): array
    {
        return [
            'id' => 1,
            'bt_sum' => 2,
            'bt_desc' => '3',
        ];

        $tx = rwBillingTransactions::find($request->integer('id'));

        return [
            'id' => $tx->bt_id,
            'bt_sum' => $tx->bt_sum,
            'bt_desc' => $tx->bt_desc,
        ];
    }

    // ************************************************************************
    // *** Блок сохранения данных
    // ************************************************************************

    public function deleteTransaction(Request $request): RedirectResponse
    {
        $tx = rwBillingTransactions::find($request->get('id'));

        if ($tx && $tx->bt_act_id == 0) {
            $tx->delete();
            Alert::success(CustomTranslator::get('Транзакция удалена'));
        } else {
            Alert::error(CustomTranslator::get('Удаление невозможно'));
        }

        return back();
    }

    public function editTransaction(Request $request): RedirectResponse
    {
        $tx = rwBillingTransactions::with('getWarehouse.getParent.getCompany')
            ->find($request->get('id'));


        if ($tx && $tx->bt_act_id == 0) {
            $tx->bt_sum = $request->get('bt_sum');

            if (isset($tx->getWarehouse->getParent->getCompany->co_vat_proc) && $tx->getWarehouse->getParent->getCompany->co_vat_proc > 0) {
                $tx->bt_tax = round($request->get('bt_sum') * ($tx->getWarehouse->getParent->getCompany->co_vat_proc / 100), 2);
                $tx->bt_total_sum = $request->get('bt_sum') + round($request->get('bt_sum') * ($tx->getWarehouse->getParent->getCompany->co_vat_proc / 100), 2);
            } else {
                $tx->bt_tax = 0;
                $tx->bt_total_sum = $request->get('bt_sum');
            }

            $tx->bt_desc = $request->get('bt_desc');
            $tx->save();

            Alert::success(CustomTranslator::get('Транзакция обновлена'));
        } else {
            Alert::error(CustomTranslator::get('Редактирование невозможно'));
        }

        return back();
    }

    public function createTransaction(Request $request): RedirectResponse
    {
        if ($request->get('whId')) {

            $resWh = rwWarehouse::with(['getParent'])
                ->find($request->get('whId'));

            $vat = optional(optional($resWh->getParent)->getCompany)->co_vat_proc ?? 0; // Получаем ставку НДС

            $tx = new rwBillingTransactions();
            $tx->bt_date = now();
            $tx->bt_shop_id = 0;
            $tx->bt_wh_id = $request->get('whId');
            $tx->bt_service_id = $request->get('bt_service_id');
            $tx->bt_billing_id = $resWh->wh_billing_id;
            $tx->bt_customer_company_id = $resWh->wh_company_id;
            $tx->bt_executor_company_id = $resWh->getParent->wh_company_id ?? 0;
            $tx->bt_doc_id = 0;
            $tx->bt_entity_count = 0;

            $sumDoc = $request->get('bt_sum');
            $tx->bt_sum = $sumDoc;

            // считаем налоги
            if ($vat > 0) {
                $tx->bt_tax = round($sumDoc * ($vat / 100), 2);
                $tx->bt_total_sum = $sumDoc + round($sumDoc * ($vat / 100), 2);
            } else {
                $tx->bt_tax = 0;
                $tx->bt_total_sum = $sumDoc;
            }

            $tx->bt_desc = $request->get('bt_desc');
            $tx->bt_act_id = 0;
            $tx->save();

            Alert::success(CustomTranslator::get('Транзакция добавлена'));

        }

        return back();
    }

    public function createInvoce(Request $request): RedirectResponse
    {
        if ($request->get('whId')) {

            $resWh = rwWarehouse::with(['getParent'])
                ->find($request->get('whId'));

            $vat = optional(optional($resWh->getParent)->getCompany)->co_vat_proc ?? 0; // Получаем ставку НДС

            $sumDoc = $request->get('in_sum');

            // считаем налоги
            if ($vat > 0) {
                $tax = round($sumDoc * ($vat / 100), 2);
                $totalSum = $sumDoc + round($sumDoc * ($vat / 100), 2);
            } else {
                $tax = 0;
                $totalSum = $sumDoc;
            }

            rwInvoce::create([
                'in_date' => now(),
                'in_wh_id' => $request->get('whId'),
                'in_customer_company_id' => $resWh->wh_company_id,
                'in_executor_company_id' => $resWh->getParent->wh_company_id ?? 0,
                'in_sum' => $sumDoc,
                'in_tax' => $tax,
                'in_total_sum' => $totalSum,
            ]);

            Alert::success(CustomTranslator::get('Счёт успешно добавлен'));

        }

        return back();
    }
}
