<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use App\Models\rwDomain;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;
use Illuminate\Support\Facades\Config; // ✅ Добавляем правильный импорт

class UserLangLayout extends Rows
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
            Select::make('user.lang')
                ->options($languages)
                ->title(CustomTranslator::get('Язык интерфейса'))
                ->value($currentUser->lang), // ✅ Устанавливаем текущий язык
        ];
    }
}
