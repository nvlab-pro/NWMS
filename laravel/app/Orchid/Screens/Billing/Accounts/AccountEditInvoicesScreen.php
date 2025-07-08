<?php

namespace App\Orchid\Screens\Billing\Accounts;

use App\Models\rwBillingTransactions;
use App\Models\rwInvoce;
use App\Models\rwWarehouse;
use App\Orchid\Layouts\Billings\Accounts\AccountNavigation;
use App\Orchid\Layouts\Billings\Accounts\InvoceTable;
use App\Services\CustomTranslator;
use Illuminate\Http\RedirectResponse;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Illuminate\Support\Facades\Auth;
use Orchid\Support\Color;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;


class AccountEditInvoicesScreen extends Screen
{
    private $whId;

    public function query($whId): iterable
    {
        $this->whId = $whId;

        $currentUser = Auth::user();

        $dbWhList = rwWarehouse::where('wh_id', $this->whId);

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {


        } else {

            $dbWhList = $dbWhList->where('wh_user_id', $currentUser->id);

        }

        $resWhList = $dbWhList->first();

        if (isset($resWhList->wh_id)) {

            $invoicesList = rwInvoce::where('in_wh_id', $this->whId)
                ->with(['getCustomerCompany', 'getExecutorCompany'])
                ->orderBy('in_id', 'DESC')
                ->paginate(100);

        }

        return [
            'invoicesList' => $invoicesList,
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Выставленные счета');
    }

    public function commandBar(): iterable
    {
        return [
            ModalToggle::make(CustomTranslator::get('Добавить счет'))
                ->modal('createInvoceModal')
                ->method('createInvoce')
                ->style('margin-left: auto;')
                ->icon('receipt')
                ->type(Color::PRIMARY),
        ];
    }

    public function layout(): iterable
    {
        return [

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

            AccountNavigation::class,

            InvoceTable::class,
        ];
    }

    // ************************************************************************
    // *** Блок сохранения данных
    // ************************************************************************

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

    public function markInvoicePaid(Request $request): RedirectResponse
    {
        if ($request->get('id')) {

            $in = rwInvoce::find($request->get('id'));
            $in->in_status = 1;
            $in->save();

            Alert::success(CustomTranslator::get('Оплата счёта проставлена!'));

        }

        return back();
    }

    public function deleteInvoice(Request $request): RedirectResponse
    {
        if ($request->get('id')) {

            $in = rwInvoce::where('in_id', $request->get('id'))
                ->where('in_status', 0)
                ->first();

            if ($in) {
                $in->delete();
            }

            Alert::error(CustomTranslator::get(' Счёта удален!'));

        }

        return back();
    }
}
