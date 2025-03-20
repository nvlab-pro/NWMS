<?php

namespace App\Imports;

use App\Models\rwOffer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OffersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Определяем, по какому полю будем искать запись
        $offerQuery = rwOffer::query();

        if (!empty($row['of_id'])) {
            $offerQuery->where('of_id', $row['of_id']);
        } elseif (!empty($row['of_sku'])) {
            $offerQuery->where('of_sku', $row['of_sku']);
        } elseif (!empty($row['of_article'])) {
            $offerQuery->where('of_article', $row['of_article']);
        }

        // Ищем существующую запись
        $offer = $offerQuery->first();

        // Подготовка данных для обновления (только заполненные поля)
        $updateData = array_filter([
            'of_domain_id' => $row['of_domain_id'] ?? null,
            'of_shop_id' => $row['of_shop_id'] ?? null,
            'of_status' => $row['of_status'] ?? null,
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
        ], fn($value) => !is_null($value) && $value !== ''); // Очищаем пустые значения

        // Если запись найдена, обновляем её
        if ($offer) {
            if (!empty($updateData)) {
                $offer->update($updateData);
            }
        } else {
            // Если не найдено, создаем новую запись
            return new rwOffer($updateData);
        }
    }
}
