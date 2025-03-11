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

function updateTranslationFiles($translations, $jsonFile, $txtFile) {
    $existingTranslations = [];

    // Читаем существующий JSON-файл, если он есть
    if (file_exists($jsonFile)) {
        $existingTranslations = json_decode(file_get_contents($jsonFile), true) ?? [];
    }

    $txtContent = "";

    foreach ($translations as $key => $data) {
        if (!isset($existingTranslations[$key])) {
            $existingTranslations[$key] = "";
            $txtContent .= "$key - {$data['source']}\n";
        }
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

$files = [];
scanDirectory($directory, $files);
$translations = extractTranslations($files);
updateTranslationFiles($translations, $jsonFile, $txtFile);

echo "Translations have been updated in $jsonFile and $txtFile";

