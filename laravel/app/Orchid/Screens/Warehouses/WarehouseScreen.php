<?php

namespace App\Orchid\Screens\Warehouses;

use App\Models\rwShop;
use App\Models\rwWarehouse;
use App\Orchid\Layouts\Warehouses\WarehouseTable;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class WarehouseScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $currentUser = Auth::user();

        $dbWhList = rwWarehouse::with('getCompany');

        if (!$currentUser->hasRole('admin')) {
            if ($currentUser->hasRole('warehouse_manager')) {

                $dbWhList->where('wh_domain_id', $currentUser->domain_id);

            } else {

                $dbWhList->where('wh_user_id', $currentUser->id)
                    ->where('wh_domain_id', $currentUser->domain_id);

            }
        }

        return [
            'whList' => $dbWhList->paginate(50),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Список складов';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(CustomTranslator::get('Добавить новый склад'))
                ->icon('bs.plus-circle')
                ->route('platform.warehouses.create'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            WarehouseTable::class,
        ];
    }
}
