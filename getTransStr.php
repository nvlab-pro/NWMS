<?php

function scanDirectory($dir, &$files = []) {
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = "$dir/$item";
        if (is_dir($path)) {
            scanDirectory($path, $files);
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            $files[] = $path;
        }
    }
}

function extractTranslations($files) {
    $translations = [];
    $pattern = "/__\('([^']+)'\)/";

    foreach ($files as $file) {
        $content = file_get_contents($file);
        if (preg_match_all($pattern, $content, $matches)) {
            foreach ($matches[1] as $match) {
                if (!isset($translations[$match])) {
                    $translations[$match] = [
                        "translation" => "",
                        "source" => $file
                    ];
                }
            }
        }
    }

    return $translations;
}

function loadExistingTranslations($filePath) {
    if (file_exists($filePath)) {
        $data = json_decode(file_get_contents($filePath), true) ?? [];
        return array_map('trim', array_keys($data)); // Получаем только ключи без пробелов
    }
    return [];
}

function updateTranslationFiles($translations, $jsonFile, $txtFile, $langFile) {
    $existingTranslations = loadExistingTranslations($jsonFile);
    $existingLangStrings = loadExistingTranslations($langFile);
    $txtContent = "";

    foreach ($translations as $key => $data) {
        $trimmedKey = trim($key); // Убираем лишние пробелы

        // Проверяем, есть ли ключ в lang/en.json или в translations.json
        if (in_array($trimmedKey, $existingLangStrings, true) || in_array($trimmedKey, $existingTranslations, true)) {
            continue;
        }

        $existingTranslations[$trimmedKey] = "";
        $txtContent .= "$trimmedKey - {$data['source']}\n";
    }

    // Обновляем JSON-файл
    file_put_contents($jsonFile, json_encode($existingTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // Обновляем TXT-файл
    file_put_contents($txtFile, $txtContent);
}

// Укажите путь к каталогу с PHP-файлами
$directory = __DIR__ . '/laravel';
// Пути к выходным файлам
$jsonFile = __DIR__ . '/img/translations.json';
$txtFile = __DIR__ . '/img/translations.txt';
$langFile = __DIR__ . '/lang/en.json';

$files = [];
scanDirectory($directory, $files);
$translations = extractTranslations($files);
updateTranslationFiles($translations, $jsonFile, $txtFile, $langFile);

echo "Translations have been updated in $jsonFile and $txtFile";