<?php

namespace App\Orchid\Screens\Shops;

use App\Models\rwShop;
use App\Orchid\Layouts\Shops\ShopsTable;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class ShopsScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {

        $currentUser = Auth::user();

        $dbShopsList = rwShop::where('sh_domain_id', $currentUser->domain_id)
            ->with('getOwner');

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {


        } else {

            $dbShopsList = $dbShopsList->where('sh_user_id', $currentUser->id);

        }

        return [
            'shopsList' => $dbShopsList->paginate(50),
        ];

    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Список магазинов');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Добавить новый магазин'))
                ->icon('bs.plus-circle')
                ->route('platform.shops.create'),
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
            ShopsTable::class,
        ];
    }
}
