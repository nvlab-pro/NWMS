<?php

namespace App\Orchid\Screens\Offers;

namespace App\Exports;

use App\Models\rwOffer;
use App\Services\CustomTranslator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\Exportable;

class OffersExport implements FromCollection, WithHeadings, WithColumnWidths
{
    use Exportable;

    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        $currentUser = Auth::user();

        $query = rwOffer::query();

        // 🔐 Фильтрация по ролям
        if ($currentUser->hasRole('admin')) {
            // admin видит всё
        } elseif ($currentUser->hasRole('warehouse_manager')) {
            $query->where('of_domain_id', $currentUser->domain_id);
        } else {
            $query->where('of_domain_id', $currentUser->domain_id)
                ->whereHas('getShop', function ($q) use ($currentUser) {
                    $q->whereIn('sh_user_id', [$currentUser->id, $currentUser->parent_id]);
                });
        }

        // 🔍 Применяем фильтры
        $filters = $this->filters;

        if (!empty($filters['of_id'])) {
            $query->where('of_id', $filters['of_id']);
        }

        if (!empty($filters['of_article'])) {
            $query->where('of_article', 'like', '%' . $filters['of_article'] . '%');
        }

        if (!empty($filters['of_name'])) {
            $query->where('of_name', 'like', '%' . $filters['of_name'] . '%');
        }

        if (!empty($filters['of_sku'])) {
            $query->where('of_sku', 'like', '%' . $filters['of_sku'] . '%');
        }

        if (!empty($filters['of_status'])) {
            $statuses = is_array($filters['of_status']) ? $filters['of_status'] : [$filters['of_status']];
            $query->whereIn('of_status', $statuses);
        }

        if (!empty($filters['of_shop_id'])) {
            $shops = is_array($filters['of_shop_id']) ? $filters['of_shop_id'] : [$filters['of_shop_id']];
            $query->whereIn('of_shop_id', $shops);
        }

        // 🔄 Сортировка
        if (!empty($this->filters['sort'])) {
            $sortField = $this->filters['sort'];
            $query->orderBy($sortField);
        }

        return $query->get()->map(function ($offer) {
            return [
                $offer->of_id,
                $offer->of_name,
                $offer->of_article,
                $offer->of_sku,
                $offer->of_price,
                $offer->of_estimated_price,
                $offer->of_weight,
                $offer->of_dimension_x,
                $offer->of_dimension_y,
                $offer->of_dimension_z,
                $offer->of_status,
                $offer->of_comment,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            CustomTranslator::get('Название'),
            CustomTranslator::get('Артикул'),
            CustomTranslator::get('SKU'),
            CustomTranslator::get('Цена'),
            CustomTranslator::get('Оценочная цена'),
            CustomTranslator::get('Вес'),
            CustomTranslator::get('Длина'),
            CustomTranslator::get('Ширина'),
            CustomTranslator::get('Высота'),
            CustomTranslator::get('Статус'),
            CustomTranslator::get('Комментарий'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8, 'B' => 45, 'C' => 15, 'D' => 15, 'E' => 12,
            'F' => 16, 'G' => 10, 'H' => 10, 'I' => 10, 'J' => 10,
            'K' => 10, 'L' => 40,
        ];
    }
}
