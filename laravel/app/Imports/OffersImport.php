<?php

namespace App\Imports;

use App\Models\rwBarcode;
use App\Models\rwImportLog;
use App\Models\rwOffer;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Orchid\Support\Facades\Alert;

class OffersImport implements ToModel, WithHeadingRow
{
    protected int $shopId, $importId;

    public function __construct(int $shopId, int $importId)
    {
        $this->shopId = $shopId;
        $this->importId = $importId;
    }

    public function model(array $row)
    {
        $currentUser = Auth::user();
        $shopId = $this->shopId;

        // Сначала ищем товар только в рамках текущего магазина
        $offer = rwOffer::query()
            ->where('of_shop_id', $shopId)
            ->when(!empty($row['of_id']), fn($q) => $q->where('of_id', $row['of_id']))
            ->when(empty($row['of_id']) && !empty($row['of_ext_id']), fn($q) => $q->where('of_ext_id', $row['of_ext_id']))
            ->when(empty($row['of_id']) && empty($row['of_ext_id']) && !empty($row['of_sku']), fn($q) => $q->where('of_sku', $row['of_sku']))
            ->when(empty($row['of_id']) && empty($row['of_ext_id']) && empty($row['of_sku']) && !empty($row['of_article']), fn($q) => $q->where('of_article', $row['of_article']))
            ->first();

        $payloadFields = [
            'of_name' => $row['of_name'] ?? null,
            'of_article' => $row['of_article'] ?? null,
            'of_sku' => $row['of_sku'] ?? null,
            'of_price' => $row['of_price'] ?? null,
            'of_estimated_price' => $row['of_estimated_price'] ?? null,
            'of_datamarix' => $row['of_datamarix'] ?? null,
            'of_img' => $row['of_img'] ?? null,
            'of_dimension_x' => $row['of_dimension_x'] ?? null,
            'of_dimension_y' => $row['of_dimension_y'] ?? null,
            'of_dimension_z' => $row['of_dimension_z'] ?? null,
            'of_weight' => $row['of_weight'] ?? null,
            'of_comment' => $row['of_comment'] ?? null,
        ];

        // если все поля пустые — пропускаем строку
        if (collect($payloadFields)->filter(fn($v) => !is_null($v) && $v !== '')->isEmpty()) {
            return null;
        }

        $updateData = array_filter([
            'of_domain_id' => $currentUser->domain_id,
            'of_shop_id' => $this->shopId,
            'of_status' => $row['of_status'] ?? 1,
            ...$payloadFields
        ], fn($value) => !is_null($value) && $value !== '');

        // Если запись найдена, обновляем её
        if (!empty($payloadFields)) {
            if ($offer) {

                // Обновление
                rwImportLog::create([
                    'il_import_id' => $this->importId,
                    'il_date' => now(),
                    'il_operation' => 2,
                    'il_name' => CustomTranslator::get('Обновление товара') . ': ' . $row['of_name'],
                    'il_fields' => json_encode($updateData),
                ]);

                $offerId = $offer->of_id;

                $offer->update($updateData);

            } else {

                // Создание лога
                rwImportLog::create([
                    'il_import_id' => $this->importId,
                    'il_date' => date('Y-m-d H:i:s'),
                    'il_operation' => 1,
                    'il_name' => CustomTranslator::get('Создание нового товара') . ': ' . $row['of_name'],
                    'il_fields' => json_encode($updateData),
                ]);

                // Если не найдено, создаем новую запись
                $offer = rwOffer::create($updateData);

                $offerId = $offer->of_id;

            }

            // Обновляем баркода, если они есть
            if ($row['of_barcode'] != '') {

                $arBarcode = explode(',', preg_replace('/[\s]+/', '', $row['of_barcode']));

                foreach ($arBarcode as $barcode) {

                    rwBarcode::updateOrCreate(
                        [
                            'br_offer_id' => $offerId, // или любое другое поле, которое нужно обновить
                            'br_barcode' => $barcode,
                            'br_shop_id' => $shopId,
                        ],
                        [
                            'br_barcode' => $barcode,
                        ]
                    );

                }

            }

        }
    }
}
