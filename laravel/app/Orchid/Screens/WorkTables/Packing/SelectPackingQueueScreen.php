<?php

namespace App\Orchid\Screens\WorkTables\Packing;

use App\Models\rwSettingsProcPacking;
use App\Models\rwWarehouse;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class SelectPackingQueueScreen extends Screen
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

            $queuesList = rwSettingsProcPacking::where([
                ['spp_domain_id', $currentUser->domain_id],
            ])
                ->whereIn('spp_wh_id', $warehouseIds)
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
        return __('Выбор очереди упаковки');
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
            Layout::view('Screens.WorkTables.Packing.QueueList'),
        ];
    }
}
