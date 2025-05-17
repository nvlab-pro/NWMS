<?php

namespace App\Orchid\Screens\EmployeesAttendance;

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
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class UsersListScreen extends Screen
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

        // Формируем список работников
        $arUsersList = $this->usersList($fltDate);

        $date = Carbon::parse($this->filterCurrentDate);
        $maxDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $date->month, $date->year);

        return array_merge($arUsersList, [
            'fltDate' => $fltDate,
            'daysInMonth' => $maxDaysInMonth,
            'currentMonth' => $date->month,
            'currentYear' => $date->year,
        ], $arMonthStats);
    }

    // Формируем статистику за месяц
    protected function monthStats($fltDate, $userId)
    {
        $arDaysStats = [];
        $arDaysCount = [];
        $arUsersName = [];
        $maxCount = 0;

        $date = Carbon::parse($fltDate);
        $maxDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $date->month, $date->year);

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
                    \Log::error('Ошибка при получении имени пользователя: ' . $e->getMessage());
                    $arUsersName[$currentTime->ea_user_id] = 'Ошибка';
                }

                if (!isset($arUsersStartTime[$currentTime->ea_user_id]) && $currentTime->ea_type == 1)
                    $arUsersStartTime[$currentTime->ea_user_id] = $currentTime->ea_date;

                if ($currentTime->ea_type == 2)
                    $arUsersEndTime[$currentTime->ea_user_id] = $currentTime->ea_date;

                $tmp = 1;

            }

            // Считаем кол-во отработанных часов
            $sumTime = 0;

            if ($tmp > 0) {

                foreach ($arUsersName as $key => $name) {

                    if (isset($arUsersStartTime[$key]) && isset($arUsersEndTime[$key])) {

                        $startTime = trim($arUsersStartTime[$key]);
                        $endTime = trim($arUsersEndTime[$key]);

                        $format = str_contains($startTime, '-') ? 'Y-m-d H:i:s' : 'H:i:s';
                        $start = Carbon::createFromFormat($format, $startTime);
                        $end = Carbon::createFromFormat($format, $endTime);

                        $sumTime += round($start->diffInHours($end), 2);
                    }
                }
            }

            $arDaysStats[$dd] = $sumTime;
            if ($maxCount < $sumTime) $maxCount = $sumTime;
        }

        for ($day = 1; $day <= $maxDaysInMonth; $day++) {

            $dd = $day;
            if ($day < 10) $dd = "0" . $day;
            $arDaysCount[$dd] = $this->calculateColumnHeight($arDaysStats[$dd], $maxCount, 200);

        }

        return [
            'arDaysStats' => $arDaysStats,
            'maxCount' => $maxCount,
            'arDaysCount' => $arDaysCount,
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

    // Сделаем список работников
    protected function usersList($fltDate)
    {
        $arUsersName = [];
        $arUsersStartTime = [];
        $arUsersEndTime = [];
        $arUsersDifferent = [];
        $arUsersCount = [];

        $currentUser = Auth::user();

        // Беру статистику посещаемости всех работников за конкретный день
        $dbEAList = WhEmployeesAttendance::where('ea_date', '>=', $fltDate . ' 00:00:00')
            ->where('ea_date', '<=', $fltDate . ' 23:59:59')
            ->with('user')
            ->orderby('ea_date', 'ASC')
            ->get();

        foreach ($dbEAList as $currentTime) {

            $arUsersName[$currentTime->ea_user_id] = $currentTime->user->name;

            if (!isset($arUsersStartTime[$currentTime->ea_user_id]) && $currentTime->ea_type == 1) {
                $arUsersStartTime[$currentTime->ea_user_id] = str_replace($fltDate . ' ', '', $currentTime->ea_date);
            }

            if ($currentTime->ea_type == 2) {
                $arUsersEndTime[$currentTime->ea_user_id] = str_replace($fltDate . ' ', '', $currentTime->ea_date);
            }

        }

        // Считаем кол-во отработанных часов
        $maxTime = 0;

        foreach ($arUsersName as $key => $name) {

            $arUsersDifferent[$key] = '-';
            $arUsersCount[$key] = 0;

            if (isset($arUsersStartTime[$key]) && isset($arUsersEndTime[$key])) {

                $start = Carbon::createFromFormat('H:i:s', $arUsersStartTime[$key]);
                $end = Carbon::createFromFormat('H:i:s', $arUsersEndTime[$key]);

                $arUsersDifferent[$key] = round($start->diffInHours($end), 2);

                if ($arUsersDifferent[$key] > $maxTime) $maxTime = $arUsersDifferent[$key];

            }

        }

        $dbUsers = User::where('wh_id', $currentUser->wh_id)->get();
        foreach ($dbUsers as $user) {

            if (!isset($arUsersName[$user->id]) && (strpos($user->name, ' ') !== false)) {

                $arUsersName[$user->id] = $user->name;
                $arUsersStartTime[$user->id] = '-';
                $arUsersEndTime[$user->id] = '-';
                $arUsersDifferent[$user->id] = '-';
                $arUsersCount[$user->id] = 0;

            }
        }

        arsort($arUsersDifferent);

        // Считаю размер столбца
        foreach ($arUsersDifferent as $key => $value) {

            if ($value > 0) {

                $arUsersCount[$key] = $this->calculateColumnHeight($arUsersDifferent[$key], $maxTime, 250);

            }

        }

        return [
            'arUsersName' => $arUsersName,
            'arUsersStartTime' => $arUsersStartTime,
            'arUsersEndTime' => $arUsersEndTime,
            'arUsersDifferent' => $arUsersDifferent,
            'arUsersCount' => $arUsersCount,
        ];

    }


    public function name(): ?string
    {
        return CustomTranslator::get('Статистика сотрудников');
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
                                ->empty(CustomTranslator::get('Не выбрано'), '0'),

                            Button::make(CustomTranslator::get('Фильтр'))
                                ->type(Color::DARK)
                                ->method('getFilterData'),

                        ])->fullWidth(),

                    ]),
                ],
            ]),

            Layout::view('Screens.EmployeesAttendance.EAMonthStats'),
            Layout::view('Screens.EmployeesAttendance.EAUsersList'),

        ];
    }

    public function getFilterData(Request $request)
    {
        $this->filterCurrentDate = $request->filterCurrentDate;
        session(['filterCurrentDate' => $this->filterCurrentDate]);

        $this->filterCurrentUserId = $request->filterCurrentUserId;
        session(['filterCurrentUserId' => $this->filterCurrentUserId]);

        // Перезагрузка экрана для применения фильтра
        return redirect()->route('platform.ea.users');
    }

    // Метод для сохранения времени
    public function saveTime(Request $request)
    {
        $request->validate([
            'userId' => 'required|integer',
            'date' => 'required|date',
            'startTime' => ['nullable', 'regex:/^([0-9]{2}:[0-9]{2}:[0-9]{2})?$/'],
            'endTime' => ['nullable', 'regex:/^([0-9]{2}:[0-9]{2}:[0-9]{2}|-)?$/'],
        ]);

        $userId = $request->input('userId');
        $date = $request->input('date');
        $timeStart = $request->input('startTime');
        $timeEnd = $request->input('endTime');

        // Сохраняю начала рабочего дня
        if ($timeStart != '-') {

            $current = WhEmployeesAttendance::where('ea_user_id', $userId)
                ->where('ea_date', '>=', $date . ' 00:00:00')
                ->where('ea_date', '<=', $date . ' 23:59:59')
                ->where('ea_type', 1)
                ->first();

            if (isset($current->ea_id)) {
                $current->update([
                    'ea_date' => $date . ' ' . $timeStart,
                ]);
            } else {
                WhEmployeesAttendance::insert([
                    'ea_user_id' => $userId,
                    'ea_date' => $date . ' ' . $timeStart,
                    'ea_type' => 1,
                ]);
            }
        } else {
            WhEmployeesAttendance::where('ea_user_id', $userId)
                ->where('ea_date', '>=', $date . ' 00:00:00')
                ->where('ea_date', '<=', $date . ' 23:59:59')
                ->where('ea_type', 2)
                ->delete();
        }

        // Сохраняю конец рабочего дня
        if ($timeEnd != '-') {

            $current = WhEmployeesAttendance::where('ea_user_id', $userId)
                ->where('ea_date', '>=', $date . ' 00:00:00')
                ->where('ea_date', '<=', $date . ' 23:59:59')
                ->where('ea_type', 2)
                ->first();

            if (isset($current->ea_id)) {
                $current->update([
                    'ea_date' => $date . ' ' . $timeEnd,
                ]);
            } else {
                WhEmployeesAttendance::insert([
                    'ea_user_id' => $userId,
                    'ea_date' => $date . ' ' . $timeEnd,
                    'ea_type' => 2,
                ]);
            }
        } else {
            WhEmployeesAttendance::where('ea_user_id', $userId)
                ->where('ea_date', '>=', $date . ' 00:00:00')
                ->where('ea_date', '<=', $date . ' 23:59:59')
                ->where('ea_type', 2)
                ->delete();
        }

//        Alert::success('Данные успешно сохранены!');
        return response()->json(['success' => true, 'message' => CustomTranslator::get('Время сохранено')]);
    }

}
