<?php

namespace App\Http\Controllers;

use App\Models\rwPrintTemplate;
use App\Services\CustomTranslator;
use Orchid\Support\Facades\Alert;
use Orchid\Attachment\Models\Attachment;

class OrderPrintController extends Controller
{
    public function download(int $orderId, int $tmpId)
    {
        // 1. Берём путь к XSLX-шаблону по $tmp
        $resCurrentTemplate = rwPrintTemplate::find($tmpId);

        if ($resCurrentTemplate) {

            $attachment = Attachment::find($resCurrentTemplate->pt_attachment_id);

            if (!$attachment || !$attachment->path) {
                echo CustomTranslator::get('Файл не найден.');

            }

            $templatePath = base_path().'/storage/app/'.$attachment->disk.'/'.$attachment->physicalPath();

            if (!file_exists($templatePath)) {
                echo CustomTranslator::get('Файл с шаблоном не найден: ') . $templatePath;
            } else {

                // 2. Генерируем файл
                $outputDir  = storage_path('app/generated');
                $outputPath = "{$outputDir}/order_{$orderId}_{$tmpId}.xlsx";

                (new ExcelExportController($orderId, $tmpId))
                    ->generate($templatePath, $outputPath);

                // 3. Отдаём пользователю
                return response()->download($outputPath)->deleteFileAfterSend();

            }

        }
    }
}