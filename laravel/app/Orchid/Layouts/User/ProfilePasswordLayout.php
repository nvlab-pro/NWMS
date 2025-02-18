<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Password;
use Orchid\Screen\Layouts\Rows;

class ProfilePasswordLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Password::make('old_password')
                ->placeholder(__('Введите текущий пароль'))
                ->title(__('Текущий пароль'))
                ->help('Это ваш пароль, установленный на данный момент.'),

            Password::make('password')
                ->placeholder(__('Введите пароль, который нужно установить'))
                ->title(__('Новый пароль')),

            Password::make('password_confirmation')
                ->placeholder(__('Введите пароль, который нужно установить'))
                ->title(__('Подтвердите новый пароль'))
                ->help('Хороший пароль должен содержать не менее 15 символов или не менее 8 символов, включая цифру и строчную букву.'),
        ];
    }
}
