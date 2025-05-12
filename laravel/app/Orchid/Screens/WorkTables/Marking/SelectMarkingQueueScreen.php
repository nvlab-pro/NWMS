<?php
// ***********************************************
// Шаг 1: Выбираем очередь маркировки
// ***********************************************

namespace App\Orchid\Screens\WorkTables\Marking;

use App\Models\rwSettingsMarking;
use Illuminate\Support\Facades\Auth;
use App\Models\rwWarehouse;
use App\Services\CustomTranslator;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class SelectMarkingQueueScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $currentUser = Auth::user();
        $queuesList = null;

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager') || $currentUser->hasRole('warehouse_worker')) {

            $warehouseIds = rwWarehouse::where('wh_id', $currentUser->wh_id)
                ->orWhere('wh_parent_id', $currentUser->wh_id)
                ->pluck('wh_id');

            $queuesList = rwSettingsMarking::where('sm_domain_id', $currentUser->domain_id)
                ->where('sm_status_id', 1)
                ->with('getDS')
                ->get();

        }

        return [
            'queuesList' => $queuesList,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return CustomTranslator::get('Выбор очереди маркировки');
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
            Layout::view('Screens.WorkTables.Marking.QueueList'),
        ];
    }
}
