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
                $translations[$match] = "";
            }
        }
    }

    return $translations;
}

function updateTranslationFile($translations, $outputFile) {
    $existingTranslations = [];

    if (file_exists($outputFile)) {
        $existingTranslations = json_decode(file_get_contents($outputFile), true) ?? [];
    }

    $mergedTranslations = array_merge($existingTranslations, $translations);
    file_put_contents($outputFile, json_encode($mergedTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Укажите путь к каталогу с файлами
$directory = __DIR__ . '../laravel';
// Укажите путь к выходному JSON-файлу
$outputFile = __DIR__ . '../img/translations.json';

//$files = [];
//scanDirectory($directory, $files);
//$translations = extractTranslations($files);
//updateTranslationFile($translations, $outputFile);

echo "Translations have been updated in $outputFile";