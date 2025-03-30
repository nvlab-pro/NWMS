<?php

namespace App\Imports;

use App\Models\rwDatamatrix;
use App\Models\rwImportLog;
use App\Models\rwOffer;
use App\Orchid\Services\ChestnyZnakParser;
use App\Services\CustomTranslator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DatamatrixImport implements ToModel
{
    protected int $shopId, $importId, $successCount, $errorCount;

    public function __construct(int $shopId, int $importId)
    {
        $this->shopId = $shopId;
        $this->importId = $importId;
        $this->successCount = 0;
        $this->errorCount = 0;
    }

    public function model(array $row)
    {
        $currentUser = Auth::user();
        $shopId = $this->shopId;

        $datamatrixCode = $row[0];

        if ($datamatrixCode) {

            $parser = new ChestnyZnakParser($datamatrixCode);

            if ($parser->isValid()) {

                // Проверка на наличие такого кода в БД
                $exists = rwDatamatrix::where('dmt_datamatrix', $datamatrixCode)->exists();

                if (!$exists) {

                    $this->successCount++;

                    // Создание кода
                    rwDatamatrix::create([
                        'dmt_barcode' => $parser->getEAN13(),
                        'dmt_short_code' => $parser->getShortCode(),
                        'dmt_crypto_tail' => $parser->getCryptoTail(),
                        'dmt_datamatrix' => $datamatrixCode,
                        'dmt_shop_id' => $shopId,
                        'dmt_status' => 0,
                        'dmt_created_date' => date('Y-m-d'),
                    ]);

                    // Логирование
                    rwImportLog::create([
                        'il_import_id' => $this->importId,
                        'il_date' => date('Y-m-d H:i:s'),
                        'il_operation' => 1,
                        'il_name' => CustomTranslator::get('Добавление кода') . ': ' . $parser->getShortCode(),
                        'il_fields' => json_encode([
                            'dmt_barcode' => $parser->getEAN13(),
                            'dmt_short_code' => $parser->getShortCode(),
                            'dmt_crypto_tail' => $parser->getCryptoTail(),
                            'dmt_datamatrix' => $datamatrixCode,
                            'dmt_shop_id' => $shopId,
                            'dmt_status' => 0,
                            'dmt_created_date' => date('Y-m-d'),
                        ]),
                    ]);
                } else {

                    $this->errorCount++;

                    // Можно добавить лог о пропущенном дубликате
                    rwImportLog::create([
                        'il_import_id' => $this->importId,
                        'il_date' => date('Y-m-d H:i:s'),
                        'il_operation' => 3,
                        'il_name' => CustomTranslator::get('Код уже существует') . ': ' . $parser->getShortCode(),
                        'il_fields' => json_encode([
                            'dmt_datamatrix' => $datamatrixCode,
                            'dmt_shop_id' => $shopId,
                        ]),
                    ]);
                }
            }
        }
    }

    public function getSuccessCount()
    {

        return $this->successCount;

    }

    public function getErrorCount()
    {

        return $this->errorCount;

    }

}
