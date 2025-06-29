<?php

namespace App\Orchid\Screens\Statistics\Workers;

use App\Models\rwUserAction;
use App\Models\User;
use App\Services\CustomTranslator;
use Orchid\Screen\Screen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Layout;
use Carbon\Carbon;


class CurrentWorkerScreen extends Screen
{
    private $workerName, $startDate, $endDate;

    public function query($workerId, Request $request): iterable
    {
        $currentUser = Auth::user();

        // Получаем диаппазон дат
        if (isset($request->startDate))
            $this->startDate = $request->startDate;
        else
            $this->startDate = date('Y-m-d', time()) . ' 00:00:00';

        if (isset($request->endDate))
            $this->endDate = $request->endDate;
        else
            $this->startDate = date('Y-m-d', time()) . ' 23:59:59';

        // Получаем данные работника
        $worker = User::where('id', $workerId)->first();
        $this->workerName = $worker->name;

        // Загружаем саму статистику
        $dbActionsList = rwUserAction::where('ua_time_start', '>=', $this->startDate)
            ->where('ua_time_start', '<=', $this->endDate)
            ->where('ua_domain_id', $currentUser->domain_id)
            ->with(['actionType', 'place', 'offer'])
            ->orderBy('ua_time_start', 'ASC');

        if (isset($request->type) && $request->type > 0)
            $dbActionsList->where('ua_lat_id', $request->type);

        return [
            'dbActionsList'    => $dbActionsList->get(),
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Статистика по работнику склада') . ': ' . $this->workerName;
    }

    public function description(): ?string
    {
        return CustomTranslator::get('За период с ') . ' ' .
            Carbon::parse($this->startDate)->format('d.m.Y H:i:s') .
            ' до ' .
            Carbon::parse($this->endDate)->format('d.m.Y H:i:s');
    }

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
            Layout::view('Screens.Statistics.Workers.WorkerActions'),
        ];
    }
}
