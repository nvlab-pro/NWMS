<?php

namespace App\Orchid\Screens\Acceptances;

use App\Models\rwAcceptance;
use App\Models\rwWarehouse;
use App\Orchid\Layouts\Acceptances\AcceptancesTable;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class AcceptancesScreen2 extends Screen
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

        if (!$currentUser->hasRole('admin')) {

            if ($currentUser->hasRole('warehouse_manager') || $currentUser->hasRole('warehouse_worker')) {

                $dbAcceptList = rwAcceptance::where('acc_domain_id', $currentUser->domain_id);

//                $arWhList = rwWarehouse::where('wh_parent_id', $currentUser->wh_id)
//                    ->pluck('wh_id')
//                    ->toArray();
//
//                $dbAcceptList = rwAcceptance::whereIn('acc_wh_id', $arWhList);

            } else {

                $dbAcceptList->whereIn('acc_user_id', [$currentUser->id, $currentUser->parent_id]);

            }
        }

        return [
            'acceptList' => $dbAcceptList
                ->with('getUser')
                ->with('getWarehouse')
                ->with('getShop')
                ->filters()
                ->defaultSort('acc_id', 'desc')
                ->paginate(50),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return CustomTranslator::get('Приемка товара');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(CustomTranslator::get('Создать новую накладную'))
                ->icon('bs.plus-circle')
                ->route('platform.acceptances.create'),

            Link::make(CustomTranslator::get('Импорт документа'))
                ->icon('bs.cloud-upload')
                ->route('platform.acceptances.import'),

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
