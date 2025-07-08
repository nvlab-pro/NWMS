<?php

namespace App\Orchid\Screens\Billing\Accounts;

use App\Http\Middleware\RoleMiddleware;
use App\Models\rwBillingTransactions;
use App\Models\rwCompany;
use App\Models\rwInvoce;
use App\Models\rwLibActionType;
use App\Models\rwWarehouse;
use App\Models\rwWhBilling;
use App\Orchid\Layouts\Billings\Accounts\AccountEditTable;
use App\Orchid\Layouts\Billings\Accounts\AccountNavigation;
use App\Orchid\Layouts\Billings\Accounts\AccountTotalTable;
use App\Orchid\Layouts\Billings\Accounts\CustomerCompanyTable;
use App\Orchid\Layouts\Billings\Accounts\InvoceTable;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\TabMenu;
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
        $executorRequisites = [];

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
        }

        return [
            'transactionsList' => $transactionsList,
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Список транзакций по складу');
    }

    public function commandBar(): iterable
    {
        return [
            ModalToggle::make(CustomTranslator::get('Добавить транзакцию'))
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                ->modal('createTransactionModal')
                ->method('createTransaction')
                ->style('margin-left: auto;')
                ->icon('plus')
                ->type(Color::SUCCESS),

        ];
    }

    public function layout(): iterable
    {

        return [
            // ***********************
            // Диалоговые окна

            Layout::modal('editTransactionModal', [
                Layout::rows([
                    Input::make('transaction_id')->type('hidden'),
                    Input::make('bt_sum')
                        ->title('Сумма')->type('number')->step('any')->required(),
                    Input::make('bt_desc')->title('Описание'),
                ]),
            ])
                ->title('Редактировать транзакцию')
                ->method('editTransaction')
                ->applyButton('Сохранить')
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

            AccountNavigation::class,
            AccountEditTable::class,


        ];
    }


    // ************************************************************************
    // *** Блок асинхронной выдачи данных для диалоговых окон
    // ************************************************************************

    public function asyncGetTransaction(Request $request): array
    {
        $tx = rwBillingTransactions::find($request->integer('transaction_id'));
        abort_if(!$tx, 404, 'Transaction not found');

        return [
            'transaction_id' => $tx->bt_id,
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
            ->find($request->get('transaction_id'));


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

}
