<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CustomSiteTranslator
{
    protected static array $translations = [];
    protected static string $locale = 'en';
    protected static bool $loaded = false; // Флаг загрузки переводов
    protected static string $openAiApiKey = ''; // Заменить на свой API-ключ

    protected static function loadTranslations(): void
    {
        if (self::$loaded) {
            return; // Если переводы уже загружены, пропускаем
        }

        self::$openAiApiKey = config('app.open_ai_api_key');
        $path = base_path("lang/" . self::$locale . ".site.json");

        if (File::exists($path)) {
            self::$translations = json_decode(File::get($path), true) ?? [];
        } else {
            self::$translations = [];
            Log::warning("Файл перевода не найден: {$path}");
        }

        self::$loaded = true; // Устанавливаем флаг загрузки
    }

    public static function cleanText($text) {
        // Удаляем все HTML-теги
//        $text = strip_tags($text);

        // Удаляем лишние пробелы, переносы строк и заменяем их одним пробелом
        $text = preg_replace('/\s+/', ' ', $text);

        // Обрезаем пробелы в начале и конце строки
        return trim($text);
    }

    public static function get(string $key, $locale, array $replace = []): string
    {

        self::$locale = $locale;

        if (
            self::$locale == 'en' || self::$locale == '' ||
            !preg_match('/\p{L}/u', $key) // если НЕТ букв
        ) {
            return $key;
        }

        $key = self::cleanText($key);

        self::loadTranslations(); // Загружаем переводы только при первом вызове

        if (!isset(self::$translations[$key])) {
            Log::warning("Перевод отсутствует: {$key}");

            // Получаем перевод через OpenAI
            $translation = self::translateViaOpenAI($key);

            if ($translation) {
                self::$translations[$key] = $translation;
                self::saveTranslations(); // Сохраняем новый перевод в JSON-файл
            } else {
                $translation = $key; // Оставляем ключ, если не удалось получить перевод
            }
        } else {
            $translation = self::$translations[$key];

        }

        // Подставляем переменные в строку (если есть)
        foreach ($replace as $search => $value) {
            $translation = str_replace(":$search", $value, $translation);
        }


        return $translation;
    }

    protected static function translateViaOpenAI(string $text): ?string
    {

        if (!self::$openAiApiKey) {
            Log::error("API-ключ OpenAI не установлен.");
            return null;
        }

        try {
            $language = config('languages.' . self::$locale, null);

            if (!$language) return $text;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . self::$openAiApiKey,
                'Content-Type'  => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
//                'model' => 'gpt-4', // Можно использовать gpt-3.5-turbo для экономии
                'model' => 'gpt-4-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'Translate a text for site about warehouse automation software. Consider this nuance and keeping the html formatting. Translate text to '.$language.'.'],
                    ['role' => 'user', 'content' => $text]
                ],
                'temperature' => 0.7,
                'max_tokens' => 3000,
            ]);

            $result = $response->json();
            return $result['choices'][0]['message']['content'] ?? null;

        } catch (\Exception $e) {
            Log::error("Ошибка при переводе через OpenAI: " . $e->getMessage());
            return null;
        }
    }

    protected static function saveTranslations(): void
    {
        $path = base_path("lang/" . self::$locale . ".site.json");

        // Проверяем, существует ли папка "lang", если нет — создаем
        if (!File::exists(base_path("lang"))) {
            File::makeDirectory(base_path("lang"), 0755, true);
        }

        // Проверяем, существует ли файл, если нет — создаем пустой JSON
        if (!File::exists($path)) {
            File::put($path, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        // Записываем переводы в файл
        File::put($path, json_encode(self::$translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public static function setLocale(string $locale): void
    {
        if (self::$locale !== $locale) {
            self::$locale = $locale;
            self::$loaded = false; // Сбрасываем флаг загрузки
            self::loadTranslations(); // Загружаем переводы для нового языка
        }
    }
}
