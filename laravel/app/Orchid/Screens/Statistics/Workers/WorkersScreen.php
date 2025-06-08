<?php

namespace App\Orchid\Screens\Statistics\Workers;

use App\Models\rwUserAction;
use App\Models\User;
use App\Services\CustomTranslator;
use Carbon\Carbon;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\RadioButtons;
use Orchid\Screen\Fields\Select;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class WorkersScreen extends Screen
{
    private $filterCurrentDate, $filterCurrentUserId, $currentDateIsDay;

    public function query(): iterable
    {
        $currentUser = Auth::user();

        $this->filterCurrentDate = date('Y-m-d', time());
        $this->currentDateIsDay = true;

        if (session()->has('filterCurrentDate'))
            $this->filterCurrentDate = session('filterCurrentDate');
        if (session()->has('currentDateIsDay'))
            $this->currentDateIsDay = session('currentDateIsDay');

        $startDate = date('Y-m-01', strtotime($this->filterCurrentDate)) . ' 00:00:00';
        $endDate   = date('Y-m-t', strtotime($this->filterCurrentDate)) . ' 23:59:59';

        $dbActionsList = rwUserAction::where('ua_time_start', '>=', $startDate)
            ->where('ua_time_start', '<=', $endDate)
            ->where('ua_domain_id', $currentUser->domain_id)
            ->with('user')
            ->get();

        $arUsersList = [];
        $arDaysCount = [];
        $arDaysStats = [];
        $arUserStats = [];
        $maxCount = 0;

        $date = Carbon::parse($this->filterCurrentDate);
        $maxDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $date->month, $date->year);

        for($day = 1; $day <= $maxDaysInMonth; $day++) {
            if ($day < 10) $dd = "0" . $day; else $dd = $day;
            if (!isset($arDaysCount[$dd])) $arDaysCount[$dd] = 0;
            if (!isset($arDaysStats[$dd])) $arDaysStats[$dd] = 0;
        }

        foreach ($dbActionsList as $dbAction) {
            if (!isset($arUsersList[$dbAction->ua_user_id])) $arUsersList[$dbAction->ua_user_id] = $dbAction->user->name;

            $currentDay = date('d', strtotime($dbAction->ua_time_start)); // будет '06'

            $arDaysStats[$currentDay] += $dbAction->ua_quantity;
            if ($arDaysStats[$currentDay] > $maxCount) $maxCount = $arDaysStats[$currentDay];

            if ($this->currentDateIsDay == 0) {
                // Если статистика текущего дня
                if (date('d', strtotime($this->filterCurrentDate)) == $currentDay) {
                    if (!isset($arUserStats[$dbAction->ua_user_id][$dbAction->ua_lat_id])) $arUserStats[$dbAction->ua_user_id][$dbAction->ua_lat_id] = 0;
                    $arUserStats[$dbAction->ua_user_id][$dbAction->ua_lat_id] += $dbAction->ua_quantity;
                }
            } else {
                //  Если статистика за весь месяц
                if (!isset($arUserStats[$dbAction->ua_user_id][$dbAction->ua_lat_id])) $arUserStats[$dbAction->ua_user_id][$dbAction->ua_lat_id] = 0;
                $arUserStats[$dbAction->ua_user_id][$dbAction->ua_lat_id] += $dbAction->ua_quantity;
            }
        }

        for ($day = 1; $day <= $maxDaysInMonth; $day++) {

            $dd = $day;
            if ($day < 10) $dd = "0" . $day;
            $arDaysCount[$dd] = $this->calculateColumnHeight($arDaysStats[$dd], $maxCount, 200);

        }

        return [
            'daysInMonth' => $maxDaysInMonth,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'arDaysCount' => $arDaysCount,
            'arDaysStats' => $arDaysStats,
            'arUsersList' => $arUsersList,
            'arUserStats' => $arUserStats,
            'currentDateIsDay' => $this->currentDateIsDay,
            'currentDay' => date('d', strtotime($this->filterCurrentDate)),
            'currentMonth' => $date->month,
            'currentYear' => $date->year,
        ];
    }

    // Считаем среднюю сумму
    protected function calculateColumnHeight(float $sum, float $maxSum, int $maxHeight = 200): int
    {
        // Если максимальная сумма равна 0, возвращаем минимальную высоту (0 пикселей)
        if ($maxSum <= 0) {
            return 0;
        }

        // Нормализуем сумму относительно максимальной суммы
        $height = ($sum / $maxSum) * $maxHeight;

        // Убедимся, что результат не превышает максимальную высоту
        return min((int)round($height), $maxHeight);
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Статистика по работникам склада');
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        $currentUser = Auth::user();

        return [
            Layout::accordion([
                CustomTranslator::get('Фильтр') => [
                    Layout::rows([

                        Group::make([
                            DateTimer::make('filterCurrentDate')
                                ->title(CustomTranslator::get('Дата:'))
                                ->format('Y-m-d')
                                ->value($this->filterCurrentDate),

                            RadioButtons::make('currentDateIsDay')
                                ->title(CustomTranslator::get('Диаппазон:'))
                                ->width('30px')
                                ->value($this->currentDateIsDay)
                                ->options([
                                    0 => CustomTranslator::get('Один день'),
                                    1 => CustomTranslator::get('Весь месяц'),
                                ]),

                            Button::make(CustomTranslator::get('Фильтр'))
                                ->type(Color::DARK)
                                ->method('getFilterData'),

                        ])->fullWidth(),

                    ]),
                ],
            ]),

            Layout::view('Screens.Statistics.Workers.WorkersMonthСhart'),
            Layout::view('Screens.Statistics.Workers.WorkersList'),
        ];
    }


    public function getFilterData(Request $request)
    {
        $this->filterCurrentDate = $request->filterCurrentDate;
        session(['filterCurrentDate' => $this->filterCurrentDate]);

        $this->currentDateIsDay = $request->currentDateIsDay;
        session(['currentDateIsDay' => $this->currentDateIsDay]);

        // Перезагрузка экрана для применения фильтра
        return redirect()->route('platform.statistics.workers');
    }
}
