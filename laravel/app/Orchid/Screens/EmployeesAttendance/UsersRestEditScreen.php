<?php

namespace App\Orchid\Screens\EmployeesAttendance;

use App\Models\EmployeeAttendanceRest;
use App\Models\User;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Fields\DateRange;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Support\Color;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\TextArea;
use Illuminate\Http\Request;

class UsersRestEditScreen extends Screen
{

    public ?EmployeeAttendanceRest $attendance = null;

    public function query($restId = null): array
    {
        $attendance = EmployeeAttendanceRest::find($restId);

        // Если запись не найдена, создаем пустую модель для нового добавления
        if (!$attendance) {
            $attendance = new EmployeeAttendanceRest();
        }

        return [
            'attendance' => $attendance,
            'attendance.ear_date_range' => [
                'start' => $attendance->ear_date_from,
                'end' => $attendance->ear_date_to,
            ],
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Редактирование записи');
    }

    public function description(): ?string
    {
        return CustomTranslator::get('Создание или редактирование записи о пропуске');
    }

    public function commandBar(): array
    {
        return [
        ];
    }

    public function layout(): array
    {
        $currentUser = Auth::user();

        return [
            Layout::rows([
                Input::make('attendance.ear_id')
                    ->type('hidden'),

                Select::make('attendance.ear_type')
                    ->title(CustomTranslator::get('Тип'))
                    ->options([
                        0 => CustomTranslator::get('Пропуск'),
                        1 => CustomTranslator::get('Отпросился'),
                        2 => CustomTranslator::get('Отпуск'),
                        3 => CustomTranslator::get('Больничный'),
                    ])
                    ->required(),

                DateRange::make('attendance.ear_date_range')
                    ->title(CustomTranslator::get('Диапазон дат'))
                    ->required()
                    ->format('Y-m-d')
                    ->placeholder(CustomTranslator::get('Выберите даты'))
                    ->help(CustomTranslator::get('Укажите диапазон дат от и до')),

                Select::make('attendance.ear_user_id')
                    ->title(CustomTranslator::get('Выберите сотрудника:'))
                    ->width('100px')
                    ->fromModel(User::where('wh_id', $currentUser['wh_id']), 'name', 'id')
                    ->empty(CustomTranslator::get('Не выбрано'), '0')
                    ->required(),

                TextArea::make('attendance.ear_comment')
                    ->title(CustomTranslator::get('Комментарий'))
                    ->rows(3),

                Button::make(CustomTranslator::get('Сохранить'))
                    ->type(Color::DARK)
                    ->icon('check')->method('save'),

                Link::make(CustomTranslator::get('Назад'))->icon('arrow-left')->route('platform.ea.rests'),
            ]),
        ];
    }

    public function save(Request $request)
    {
        $data = $request->get('attendance');

        // Если передан id и запись существует, то обновляем её, иначе создаём новую
        $attendance = EmployeeAttendanceRest::find($data['ear_id'] ?? null)
            ?? new EmployeeAttendanceRest();

        // Получаем диапазон дат
        $dateRange = $data['ear_date_range'] ?? null;

        // Если диапазон дат задан, сохраняем его
        if ($dateRange) {
            $attendance->ear_date_from = $dateRange['start'];
            $attendance->ear_date_to = $dateRange['end'];
        }

        $attendance->fill($data)->save();

        Alert::success(CustomTranslator::get('Данные успешно сохранены'));

        return redirect()->route('platform.ea.rests');
    }


}
