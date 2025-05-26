<?php

namespace App\Http\Controllers;

use alhimik1986\PhpExcelTemplator\PhpExcelTemplator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExcelExportController extends Controller
{
    public function download()
    {
        $templatePath = storage_path('app/templates/products_template.xlsx');
        $outputDir = storage_path('app/generated');

        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0775, true);
        }

        $outputPath = $outputDir . '/products_' . now()->format('Ymd_His') . '.xlsx';

        $products = [
            '[name]' => [
                'Яблоко',
                'Банан',
                'Груша',
            ],
            '[price]' => [
                100,
                200,
                300,
            ],
//            ['name' => 'Яблоко', 'price' => 120],
//            ['name' => 'Банан',  'price' => 95],
//            ['name' => 'Груша',  'price' => 150],
        ];

        PhpExcelTemplator::saveToFile(
            $templatePath,
            $outputPath,
            $products,
        );

        if (!file_exists($outputPath)) {
            abort(500, 'Файл не был создан.');
        }

        return response()->download($outputPath)->deleteFileAfterSend();
    }
}