<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use App\Services\CustomTranslator;
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
                ->placeholder(CustomTranslator::get('Введите текущий пароль'))
                ->title(CustomTranslator::get('Текущий пароль'))
                ->help(CustomTranslator::get('Это ваш пароль, установленный на данный момент.')),

            Password::make('password')
                ->placeholder(CustomTranslator::get('Введите пароль, который нужно установить'))
                ->title(CustomTranslator::get('Новый пароль')),

            Password::make('password_confirmation')
                ->placeholder(CustomTranslator::get('Введите пароль, который нужно установить'))
                ->title(CustomTranslator::get('Подтвердите новый пароль'))
                ->help(CustomTranslator::get('Хороший пароль должен содержать не менее 15 символов или не менее 8 символов, включая цифру и строчную букву.')),
        ];
    }
}
