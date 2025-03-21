<?php

namespace App\Imports;

use App\Models\rwOffer;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Orchid\Support\Facades\Alert;

class OffersImport implements ToModel, WithHeadingRow
{
    protected int $shopId;

    public function __construct(int $shopId)
    {
        $this->shopId = $shopId;
    }

    public function model(array $row)
    {
        $currentUser = Auth::user();
        $shopId = $this->shopId;

// Сначала ищем товар только в рамках текущего магазина
        $offer = rwOffer::query()
            ->where('of_shop_id', $shopId)
            ->when(!empty($row['of_id']), fn($q) => $q->where('of_id', $row['of_id']))
            ->when(empty($row['of_id']) && !empty($row['of_sku']), fn($q) => $q->where('of_sku', $row['of_sku']))
            ->when(empty($row['of_id']) && empty($row['of_sku']) && !empty($row['of_article']), fn($q) => $q->where('of_article', $row['of_article']))
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

                $offer->update($updateData);

            } else {
                // Если не найдено, создаем новую запись
                return new rwOffer($updateData);

            }
        }
    }
}
