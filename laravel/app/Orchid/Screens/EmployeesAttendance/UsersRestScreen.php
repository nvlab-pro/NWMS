<?php

namespace App\Orchid\Screens\EmployeesAttendance;

use App\Models\EmployeeAttendanceRest;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class UsersRestScreen extends Screen
{

    public function query(): array
    {
        $eaRests = EmployeeAttendanceRest::with('user')->paginate();

        return [
            'attendances' => $eaRests,
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Пропуски сотрудников');
    }

    public function description(): ?string
    {
        return CustomTranslator::get('Список пропусков, отпусков и больничных');
    }

    public function commandBar(): array
    {
        return [
            Link::make(CustomTranslator::get('Добавить пропуск'))->icon('plus')->route('platform.ea.rests.add'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::table('attendances', [
                TD::make('ear_id', 'ID')->sort(),

                TD::make('user.name', CustomTranslator::get('Сотрудник')),

                TD::make('ear_type', CustomTranslator::get('Тип'))
                    ->render(function ($model) {
                        return match ((int) $model->ear_type) {
                            0 => CustomTranslator::get('Пропуск'),
                            1 => CustomTranslator::get('Отпросился'),
                            2 => CustomTranslator::get('Отпуск'),
                            3 => CustomTranslator::get('Больничный'),
                            default => CustomTranslator::get('Неизвестный тип'),
                        };
                    }),


                TD::make('ear_date_from', CustomTranslator::get('С'))
                    ->render(fn($model) => $model->ear_date_from->format('d.m.Y')),
                TD::make('ear_date_to', CustomTranslator::get('По'))
                    ->render(fn($model) => $model->ear_date_to->format('d.m.Y')),
                TD::make('ear_comment', CustomTranslator::get('Комментарий')),
                TD::make('')
                    ->align(TD::ALIGN_CENTER)
                    ->width('100px')
                    ->render(fn($model) => Link::make('')
                        ->class('btn btn-primary')
                        ->icon('bs.pencil')
                        ->route('platform.ea.rests.edit', $model)),
                TD::make('')
                    ->align(TD::ALIGN_CENTER)
                    ->width('100px')
                    ->render(fn($model) => Button::make('')
                        ->class('btn btn-danger')
                        ->icon('bs.trash')
                        ->method('delete')
                        ->confirm(CustomTranslator::get('Вы уверены?'))
                        ->parameters(['restId' => $model->ear_id])),
            ]),
        ];
    }

    public function delete($restId)
    {
        EmployeeAttendanceRest::where('ear_id', $restId)->delete();

        Alert::error(CustomTranslator::get('Данные успешно удалены'));

    }
}

