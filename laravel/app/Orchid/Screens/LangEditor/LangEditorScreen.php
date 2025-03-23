<?php

namespace App\Orchid\Screens\LangEditor;

use App\Services\CustomTranslator;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class LangEditorScreen extends Screen
{
    public $locale;
    /**
     * Название экрана.
     */
    public function name(): string
    {
        return CustomTranslator::get('Редактор локализации');
    }

    /**
     * Доступные запросы.
     */
    public function query(Request $request): array
    {
        $locale = $request->get('locale', config('app.locale'));
        $this->locale = $locale;

        $translations = $this->getTranslationsForLocale($locale);

        return [
            'translations' => $translations,
            'current_locale' => $locale,
            'available_locales' => $this->getLocales(),
        ];
    }

    /**
     * Кнопки действий.
     */
    public function commandBar(): array
    {
        return [
            Button::make(CustomTranslator::get('Сохранить'))
                ->icon('save')
                ->method('saveTranslations')
            ->parameters([
                'current_locale' => $this->locale,
            ]),
        ];
    }

    /**
     * Макеты отображения.
     */
    public function layout(): array
    {
        return [
            Layout::view('Screens.Lang.lang-switcher'),

            Layout::rows([
                // 🔹 Линки для смены языка

                Matrix::make('translations')
                    ->columns([
                        'Attribute',
                        'Value',
                    ])
                    ->title(CustomTranslator::get('Переводы'))
                    ->help(CustomTranslator::get('Редактируйте переводы в формате "Ключ - Значение"')),
            ]),
        ];
    }

    /**
     * Получает переводы для указанной локали.
     */
    public function getTranslationsForLocale(string $locale): array
    {
        $filePath = base_path("lang/{$locale}.json");

        if (!File::exists($filePath)) {
            return [];
        }

        $translations = json_decode(File::get($filePath), true) ?? [];

        return collect($translations)->map(fn ($value, $key) => [
            'Attribute' => $key,
            'Value' => $value,
        ])->toArray();
    }

    /**
     * Обработчик сохранения переводов.
     */
    public function saveTranslations(Request $request)
    {
        $locale = $request->get('current_locale');
        $translationsArray = $request->get('translations');

        $newTranslations = [];

        foreach ($translationsArray as $translation) {
            if (!empty($translation['Attribute']) && isset($translation['Value'])) {
                $newTranslations[$translation['Attribute']] = $translation['Value'];
            }
        }

        $filePath = base_path("lang/{$locale}.json");
        File::put($filePath, json_encode($newTranslations, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        Toast::info(CustomTranslator::get('Файл локализации обновлен'));
    }

    /**
     * Получает список доступных языков.
     */
    protected function getLocales(): array
    {
        $files = File::files(base_path('lang'));
        $locales = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'json') {
                $locale = $file->getFilenameWithoutExtension();
                $locales[$locale] = strtoupper($locale);
            }
        }

        return $locales;
    }
}
