<?php

require __DIR__ . '/vendor/autoload.php';

use alhimik1986\PhpExcelTemplator\PhpExcelTemplator;

$templatePath = __DIR__ . '/storage/app/templates/products_template2.xlsx';
$outputPath = __DIR__ . '/storage/app/generated/test_output.xlsx';

$products = [
    ['{{name}}' => 'Яблоко', '{{price}}' => 120],
    ['{{name}}' => 'Банан',  '{{price}}' => 95],
    ['{{name}}' => 'Груша',  '{{price}}' => 150],
];

echo "Пытаемся сгенерировать...\n";

PhpExcelTemplator::saveToFile(
    $outputPath,    // <- правильно: куда сохранить результат
    $templatePath,  // <- правильно: откуда взять шаблон
    [
        '[[products]]' => $products,
    ]
);

echo file_exists($outputPath)
    ? "✅ Файл создан: $outputPath\n"
    : "❌ Файл не создан\n";