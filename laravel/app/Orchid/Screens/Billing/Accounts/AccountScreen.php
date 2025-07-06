<?php

namespace App\Orchid\Screens\Billing\Accounts;

use App\Models\rwShop;
use App\Models\rwWarehouse;
use App\Orchid\Layouts\Billings\Accounts\AccountsTable;
use App\Orchid\Layouts\Warehouses\WarehouseTable;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class AccountScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $currentUser = Auth::user();

        $dbWhList = rwWarehouse::with(['getCompany', 'getParent.getCompany']);

        if (!$currentUser->hasRole('admin')) {
            if ($currentUser->hasRole('warehouse_manager')) {

                $dbWhList->where('wh_domain_id', $currentUser->domain_id);

            } else {

                $dbWhList->where('wh_user_id', $currentUser->id)
                    ->where('wh_user_id', $currentUser->parent_id)
                    ->where('wh_domain_id', $currentUser->domain_id);

            }
        }

        return [
            'whList' => $dbWhList->paginate(50),
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Список счетов');
    }

    public function commandBar(): iterable
    {
        return [
            Link::make(CustomTranslator::get('Добавить новый склад'))
                ->icon('bs.plus-circle')
                ->route('platform.warehouses.create'),
        ];
    }

    public function layout(): iterable
    {
        return [
            AccountsTable::class,
        ];
    }
}
