<?php

namespace App\Orchid\Screens\terminal\Acceptance;

use App\Models\rwAcceptance;
use App\Orchid\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class SelectAcceptanceScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
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

//                    $dbAccept = rwAcceptance::where('acc_domain_id', $currentUser->domain_id)
//                        ->where('acc_id', $validatedData['docId'])
//                        ->first('acc_wh_id');

                    $currentDocument = new DocumentService($validatedData['docId']);
                    $currentDocument->updateRest(1);

                    Alert::info(__('Накладная закрыта!'));

                }

            }

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
