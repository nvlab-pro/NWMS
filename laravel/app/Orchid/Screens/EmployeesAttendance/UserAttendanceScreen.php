<?php

namespace App\Orchid\Screens\EmployeesAttendance;

use App\Models\EmployeeAttendanceRest;
use App\Services\CustomTranslator;
use Carbon\Carbon;
use App\Models\MistralLocations;
use App\Models\User;
use App\Models\WhEmployeesAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\RadioButtons;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;

class UserAttendanceScreen extends Screen
{

    protected $filterCurrentDate, $filterCurrentUserId;

    public function query(Request $request): iterable
    {

        $fltDate = date('Y-m-d', time());

        $this->filterCurrentUserId = 0;

        if (session()->has('filterCurrentDate'))
            $fltDate = session('filterCurrentDate');

        if (session()->has('filterCurrentDate'))
            $this->filterCurrentUserId = session('filterCurrentUserId');

        $this->filterCurrentDate = $fltDate;

        // Формируем статистику за месяц
        $arMonthStats = $this->monthStats($fltDate, $this->filterCurrentUserId);

        // Формируем массив с пропускамми
        $arUsersRests = $this->usersRests($fltDate);

        $date = Carbon::parse($this->filterCurrentDate);
        $maxDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $date->month, $date->year);

        return array_merge([
            'fltDate' => $fltDate,
            'daysInMonth' => $maxDaysInMonth,
            'currentMonth' => $date->month,
            'currentYear' => $date->year,
            'arUsersRests'  => $arUsersRests,
        ], $arMonthStats);
    }

    // Формируем массив с пропускамми
    protected function usersRests($fltDate)
    {
        $arUsersRests = [];

        $date = Carbon::parse($fltDate);
        $currentMonth = $date->month;
        $currentYear = $date->year;

        $firstDayOfRange = $date->clone()->subMonths(6)->startOfMonth(); // начало диапазона
        $lastDayOfRange = $date->clone()->addMonths(6)->endOfMonth();   // конец диапазона
        $firstDayOfRangeString = $firstDayOfRange->toDateString(); // начало диапазона в виде строки
        $lastDayOfRangeString = $lastDayOfRange->toDateString(); // конец диапазона в виде строки

        // Получаем ВСЕ записи из БД, у которых есть хоть какое-то пересечение с нашим диапазоном.
        $dbUsersRests = EmployeeAttendanceRest::where('ear_date_to', '>=', $firstDayOfRangeString)
            ->where('ear_date_from', '<=', $lastDayOfRangeString)
            ->get();

        foreach ($dbUsersRests as $dbUserRest) {
            $userRestDateFrom = Carbon::parse($dbUserRest->ear_date_from);
            $userRestDateTo = Carbon::parse($dbUserRest->ear_date_to);

            // Определяем, какой день месяца является началом для текущей записи
            $startDay = max($firstDayOfRange, $userRestDateFrom);
            // Определяем, какой день месяца является концом для текущей записи
            $endDay = min($lastDayOfRange, $userRestDateTo);

            // Проверка, что начало или конец входят в выбранный месяц
            if($startDay->month <= $currentMonth && $endDay->month >= $currentMonth){

                $startDayInCurrentMonth = $startDay->month == $currentMonth ? $startDay : Carbon::create($currentYear, $currentMonth, 1);
                $endDayInCurrentMonth = $endDay->month == $currentMonth ? $endDay : Carbon::create($currentYear, $currentMonth, cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear));

                for ($d = $startDayInCurrentMonth->day; $d <= $endDayInCurrentMonth->day; $d++) {
                    $dd = $d;
                    if ($d < 10) $dd = '0' . (int)$d;
                    $arUsersRests[$dbUserRest->ear_user_id][$dd] = $dbUserRest->ear_type;
                }
            }
        }

        return $arUsersRests;
    }

    // Формируем статистику за месяц
    protected function monthStats($fltDate, $userId)
    {
        $arUsersName = [];
        $arUsersHours = [];

        $currentUser = Auth::user();

        $date = Carbon::parse($fltDate);
        $maxDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $date->month, $date->year);

        $arUsersName = User::where('wh_id', $currentUser->wh_id)
            ->where('name', 'LIKE', '% %')
            ->orderBy('name', 'ASC')
            ->pluck('name', 'id')
            ->toArray();

        for ($day = 1; $day <= $maxDaysInMonth; $day++) {

            $dd = $day;
            if ($day < 10) $dd = "0" . $day;
            $tmp = 0;

            $arUsersStartTime = [];
            $arUsersEndTime = [];

            // Беру статистику посещаемости всех работников за конкретный день
            if ($userId > 0) {
                $dbEAList = WhEmployeesAttendance::where('ea_date', '>=', $date->year . '-' . $date->month . '-' . $dd . ' 00:00:00')
                    ->where('ea_date', '<=', $date->year . '-' . $date->month . '-' . $dd . ' 23:59:59')
                    ->where('ea_user_id', $userId)
                    ->with('user')
                    ->orderby('ea_date', 'ASC')
                    ->get();
            } else {
                $dbEAList = WhEmployeesAttendance::where('ea_date', '>=', $date->year . '-' . $date->month . '-' . $dd . ' 00:00:00')
                    ->where('ea_date', '<=', $date->year . '-' . $date->month . '-' . $dd . ' 23:59:59')
                    ->with('user')
                    ->orderby('ea_date', 'ASC')
                    ->get();
            }

            foreach ($dbEAList as $currentTime) {

                try {
                    if ($currentTime->user) {
                        $arUsersName[$currentTime->ea_user_id] = $currentTime->user->name;
                    } else {
                        $arUsersName[$currentTime->ea_user_id] = 'Я ХЗ кто это';
                    }
                } catch (\Throwable $e) {
                    \Log::error(CustomTranslator::get('Ошибка при получении имени пользователя: ') . $e->getMessage());
                    $arUsersName[$currentTime->ea_user_id] = 'Ошибка';
                }

                if (!isset($arUsersStartTime[$currentTime->ea_user_id]) && $currentTime->ea_type == 1)
                    $arUsersStartTime[$currentTime->ea_user_id] = $currentTime->ea_date;

                if ($currentTime->ea_type == 2)
                    $arUsersEndTime[$currentTime->ea_user_id] = $currentTime->ea_date;

                $tmp = 1;

            }

            // Считаем кол-во отработанных часов
            if ($tmp > 0) {

                foreach ($arUsersName as $key => $name) {

                    if (isset($arUsersStartTime[$key]) && isset($arUsersEndTime[$key])) {

                        $startTime = trim($arUsersStartTime[$key]);
                        $endTime = trim($arUsersEndTime[$key]);

                        $format = str_contains($startTime, '-') ? 'Y-m-d H:i:s' : 'H:i:s';
                        $start = Carbon::createFromFormat($format, $startTime);
                        $end = Carbon::createFromFormat($format, $endTime);

                        $userTime = round($start->diffInHours($end), 2);



                        $arUsersHours[$dd][$key] = $userTime;

                    }
                }
            }
       }

        return [
            'arUsersName' => $arUsersName,
            'arUsersHours' => $arUsersHours,
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
        return CustomTranslator::get('Статистика посещаемости сотрудников');
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

                            Select::make('filterCurrentUserId')
                                ->title(CustomTranslator::get('Выберите сотрудника (если нужно):'))
                                ->width('100px')
                                ->value($this->filterCurrentUserId)
                                ->fromModel(User::where('wh_id', $currentUser['wh_id']), 'name', 'id')
                                ->empty(CustomTranslator::get('Все'), '0'),

                            Button::make('Фильтр')
                                ->type(Color::DARK)
                                ->method('getFilterData'),

                        ])->fullWidth(),

                    ]),
                ],
            ]),

            Layout::view('Screens.EmployeesAttendance.EAAttendance'),

        ];
    }

    public function getFilterData(Request $request)
    {
        $this->filterCurrentDate = $request->filterCurrentDate;
        session(['filterCurrentDate' => $this->filterCurrentDate]);

        $this->filterCurrentUserId = $request->filterCurrentUserId;
        session(['filterCurrentUserId' => $this->filterCurrentUserId]);

        // Перезагрузка экрана для применения фильтра
        return redirect()->route('platform.ea.attendance');
    }
}
