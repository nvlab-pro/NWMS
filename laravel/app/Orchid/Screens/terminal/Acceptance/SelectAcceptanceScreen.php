<?php

namespace App\Orchid\Screens\terminal\Acceptance;

use App\Models\rwAcceptance;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class SelectAcceptanceScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {

        $currentUser = Auth::user();

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager') || $currentUser->hasRole('warehouse_worker')) {

            $dbAcceptList = rwAcceptance::where('acc_domain_id', $currentUser->domain_id)
                ->whereIn('acc_status', [1, 2])
                ->where('acc_type', 1)
                ->with('getAccStatus')
                ->with('getWarehouse')
                ->orderByDesc('acc_id');


            return [
                'dbAcceptList' => $dbAcceptList->get(),
            ];

        }

        return [];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Выберите накладную для приемки');
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
            Layout::view('Screens.Terminal.Acceptance.SelectAcceptance'),
        ];
    }
}
