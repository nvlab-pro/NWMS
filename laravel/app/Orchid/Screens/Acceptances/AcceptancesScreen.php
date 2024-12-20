<?php

namespace App\Orchid\Screens\Acceptances;

use App\Models\rwAcceptance;
use App\Models\rwWarehouse;
use App\Orchid\Layouts\Acceptances\AcceptancesTable;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class AcceptancesScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $currentUser = Auth::user();

        $dbAcceptList = rwAcceptance::query();

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {


        } else {

//            $dbWhList = $dbAcceptList->where('wh_user_id', $currentUser->id);

        }

        return [
            'acceptList' => $dbAcceptList->paginate(50),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Приемка товара';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Создать новую накладную'))
                ->icon('bs.plus-circle')
                ->route('platform.acceptances.create'),
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
            AcceptancesTable::class,
        ];
    }
}
