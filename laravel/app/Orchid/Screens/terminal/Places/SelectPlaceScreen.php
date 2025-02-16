<?php

namespace App\Orchid\Screens\terminal\Places;

use App\Models\rwAcceptance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class SelectPlaceScreen extends Screen
{

    public function query(Request $request): iterable
    {
        $validatedData = $request->validate([
            'docId' => 'nullable|numeric',
            'action' => 'nullable|string',
        ]);

        $currentUser = Auth::user();

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager') || $currentUser->hasRole('warehouse_worker')) {

            // Закрывам накладную
            if (isset($validatedData['docId']) && isset($validatedData['action'])) {

                if ($validatedData['action'] == 'close' && $validatedData['docId']) {

                    rwAcceptance::where('acc_id', $validatedData['docId'])
                        ->where('acc_status', 2)
                        ->update([
                            'acc_status' => 4,
                        ]);

                    Alert::info(__('Накладная закрыта!'));

                }

            }

            $dbAcceptList = rwAcceptance::where('acc_domain_id', $currentUser->domain_id)
                ->whereIn('acc_status', [2, 3, 4])
                ->where('acc_type', 1)
                ->where('acc_count_accepted', '!=',  'acc_count_placed')
                ->with('getAccStatus')
                ->with('getWarehouse')
                ->orderByDesc('acc_id');


            return [
                'dbAcceptList' => $dbAcceptList->get(),
            ];

        }

        return [];
    }


    public function name(): ?string
    {
        return __('Выберите приемку');
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
            Layout::view('Screens.Terminal.Places.SelectPlaces'),
        ];
    }
}
