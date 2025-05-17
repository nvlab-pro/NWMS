<?php

namespace App\Orchid\Screens\EmployeesAttendance;

use App\Models\User;
use App\Models\WhEmployeesAttendance;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Carbon\Carbon;

class EAScreen extends Screen
{
    public function screenBaseView(): string
    {
        return 'loyouts.base';
    }

    public function query(Request $request): iterable
    {

        $type = 0;
        $currentUser = [];
        $message = null;

        if (isset($request->barcode)) {
            $type = -1;
            $currentUser = User::where('barcode', $request->barcode)->first();

            if ($currentUser) {
                $lastTime = WhEmployeesAttendance::where('ea_user_id', $currentUser->id)
                    ->orderBy('ea_date', 'DESC')
                    ->first();

                $now = Carbon::now('Europe/Sofia'); // Текущее время в Europe/Sofia

                if (!$lastTime) {
                    // Нет записей для этого пользователя - создаем запись о приходе
                    $type = 1;
                    WhEmployeesAttendance::create([
                        'ea_user_id' => $currentUser->id,
                        'ea_date' => $now,
                        'ea_type' => $type,
                    ]);

                } elseif ($lastTime->ea_type == 1) {
                    // Есть запись с типом 1 (пришел)
                    $lastTimeDate = Carbon::parse($lastTime->ea_date);
                    $diffInMinutes = $now->diffInMinutes($lastTimeDate);

                    if ($diffInMinutes < 200 && $diffInMinutes > 0) {
                        // Прошло меньше часа - запрещаем добавление записи с типом 2
                        $type = -2;
                        $message = CustomTranslator::get("Для сотрудника: ") . $currentUser->name . CustomTranslator::get(" - еще не прошло часа с момента начала рабочего дня.");
                    } else {
                        // Прошел час - добавляем запись с типом 2 (ушел)
                        $type = 2;
                        WhEmployeesAttendance::create([
                            'ea_user_id' => $currentUser->id,
                            'ea_date' => $now,
                            'ea_type' => $type,
                        ]);
                        $message = CustomTranslator::get("Сотрудник: ") . $currentUser->name . CustomTranslator::get(" - Ушел с работы");
                    }
                } else {
                    // Если тип не равен 1 - то выставляем 1
                    $type = 1;
                    WhEmployeesAttendance::create([
                        'ea_user_id' => $currentUser->id,
                        'ea_date' => $now,
                        'ea_type' => $type,
                    ]);
                    $message = CustomTranslator::get("Сотрудник: ") . $currentUser->name . CustomTranslator::get(" - Пришел на работу");
                }
            }
        }

        return [
            'type' => $type,
            'user' => $currentUser,
            'message' => $message,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return CustomTranslator::get('Фиксация времени сотрудников');
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
            Layout::view('Screens.EmployeesAttendance.EAScanInput'),

        ];
    }
}
