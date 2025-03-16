<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use App\Services\CustomTranslator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class UserEditLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        $currentUser = Auth::user();

        // ✅ Загружаем список языков из конфига
        $languages = Config::get('languages', $currentUser->lang);

        return [
            Input::make('user.name')
                ->type('text')
                ->max(255)
                ->required()
                ->title(CustomTranslator::get('Имя пользователя'))
                ->placeholder(CustomTranslator::get('Name')),

            Input::make('user.email')
                ->type('email')
                ->required()
                ->title(CustomTranslator::get('Email'))
                ->placeholder(CustomTranslator::get('Email')),

            Select::make('user.lang')
                ->options($languages)
                ->title(CustomTranslator::get('Язык интерфейса'))
                ->value($currentUser->lang), // ✅ Устанавливаем текущий язык
        ];
    }
}
