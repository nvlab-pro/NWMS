<?php

namespace App\Orchid\Screens\Billing\Accounts;

use App\Models\rwBillingTransactions;
use App\Models\rwInvoce;
use App\Models\rwWarehouse;
use App\Orchid\Layouts\Billings\Accounts\AccountNavigation;
use App\Orchid\Layouts\Billings\Accounts\AccountTotalTable;
use App\Services\CustomTranslator;
use Orchid\Screen\Screen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class AccountEditTotalScreen extends Screen
{
    private $whId;

    public function query($whId): iterable
    {
        $this->whId = $whId;
        $totalTransList = [];
        $totalInvoice = [];

        $currentUser = Auth::user();

        $dbWhList = rwWarehouse::where('wh_id', $this->whId);

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {


        } else {

            $dbWhList = $dbWhList->where('wh_user_id', $currentUser->id);

        }

        $resWhList = $dbWhList->with(['getCompany', 'getParent.getCompany'])
            ->first();

        if (isset($resWhList->wh_id)) {

            $totalTransList = rwBillingTransactions::select(
                'bt_service_id',
                DB::raw('SUM(bt_total_sum) as total_sum')
            )
                ->where('bt_wh_id', $this->whId)
                ->with(['actionType'])
                ->groupBy('bt_service_id')
                ->filters()
                ->get();

            $totalInvoice = rwInvoce::where('in_wh_id', $this->whId)
                ->where('in_status', 1)
                ->sum('in_total_sum');

        }

        return [
            'totalTransList' => $totalTransList,
            'totalInvoice' => $totalInvoice,
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Список транзакций по складу');
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
            AccountNavigation::class,

            Layout::view('Screens.Billing.Accounts.AccountTotal'),

        ];
    }
}
