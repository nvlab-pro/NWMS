<?php

namespace App\Orchid\Screens\terminal\SOAM;

use App\Models\rwSettingsSoa;
use App\Models\rwWarehouse;
use App\Orchid\Services\SOAService;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

// Кладовщик выбирает очередь сборки
class SelectSOAMScreen extends Screen
{

    public function query(): iterable
    {

        $currentUser = Auth::user();


        $resSettingsList = [];

        // Только для работников склада
        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager') || $currentUser->hasRole('warehouse_worker')) {

            SOAService::calcAllSettings($currentUser->domain_id, $currentUser->wh_id);

            $dbSettingsList = rwSettingsSoa::where('ssoa_domain_id', $currentUser->domain_id)
                ->where('ssoa_count_ready', '>', 0)
                ->where('ssoa_status_id', 1);

            $arWhList = rwWarehouse::where('wh_parent_id', $currentUser->wh_id)
                ->pluck('wh_id')
                ->toArray();

            $dbSettingsList->whereIn('ssoa_wh_id', $arWhList);

            $resSettingsList = $dbSettingsList->orderBy('ssoa_priority', 'DESC')->get();

        }

        return [
            'dbSettingsList' => $resSettingsList,
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Выберите очередь для сборки');
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        return [
            Layout::view('Screens.Terminal.SOAM.SelectSOAM'),
        ];
    }
}
