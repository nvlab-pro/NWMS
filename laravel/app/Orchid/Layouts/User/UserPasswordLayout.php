<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use App\Services\CustomTranslator;
use Orchid\Platform\Models\User;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Password;
use Orchid\Screen\Layouts\Rows;

class UserPasswordLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        /** @var User $user */
        $user = $this->query->get('user');

        $placeholder = $user->exists
            ? CustomTranslator::get('Оставьте пустым, чтобы сохранить текущий пароль.')
            : CustomTranslator::get('Введите пароль, который нужно установить');

        return [
            Password::make('user.password')
                ->placeholder($placeholder)
                ->title(CustomTranslator::get('Пароль')),
        ];
    }
}
