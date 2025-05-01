<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class rwAcceptanceOffer extends Model implements AuditableContract
{
    protected $primaryKey = 'ao_id';

    use AsSource, Filterable, Attachable, HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'ao_id',
        'ao_acceptance_id',
        'ao_offer_id',
        'ao_batch',
        'ao_production_date',
        'ao_expiration_date',
        'ao_barcode',
        'ao_expected',
        'ao_accepted',
        'ao_placed',
        'ao_price',

        'ao_offer_id',
        'ao_img',
        'ao_name',
        'ao_article',
        'ao_dimension',
        'oa_status',
        'ao_wh_offer_id',
    ];

    protected $allowedFilters = [
        'ao_id'            => Where::class,
        'ao_offer_id'            => Where::class,
        'ao_expected'       => Where::class,
        'ao_accepted'        => Where::class,
        'ao_price'        => Where::class,
        'ao_barcode'        => Like::class,
        'ao_expiration_date'        => Like::class,
        'getOffers.of_name' => Like::class,  // Фильтр для поля связанной модели
        'getOffers.of_article' => Like::class, // Еще один пример для фильтрации
    ];

    /**
     * @var array
     */
    protected $allowedSorts = [
        'ao_id',
        'ao_offer_id',
        'getOffers.of_name',
        'getOffers.of_article',
        'ao_expected',
        'ao_accepted',
        'ao_price',
        'ao_barcode',
        'ao_expiration_date',
    ];

    public static function perPage(): int
    {
        return 50;
    }
    public function getOffers() {
        return $this->hasOne(rwOffer::class, 'of_id', 'ao_offer_id');
    }


    // Переопределите метод для сортировки по полям связанной модели
    public function scopeSort($query, $sort)
    {
        if ($sort === 'getOffers.of_name') {
            return $query->join('rw_offers', 'rw_acceptance_offers.ao_offer_id', '=', 'rw_offers.of_id')
                ->orderBy('rw_offers.of_name');
        }

        // Включите сортировку для других полей
        return parent::scopeSort($query, $sort);
    }

    // Переопределение метода фильтрации для поля связанной модели
    public function scopeFilter($query, $filters)
    {
        // Фильтрация по полю из связанной таблицы (например, `of_name`)
        if (isset($filters['getOffers.of_name'])) {
            $query->join('rw_offers', 'rw_acceptance_offers.ao_offer_id', '=', 'rw_offers.of_id')
                ->where('rw_offers.of_name', 'like', '%'.$filters['getOffers.of_name'].'%');
        }

        // Фильтрация по другим полям, если нужно
        if (isset($filters['getOffers.of_article'])) {
            $query->join('rw_offers', 'rw_acceptance_offers.ao_offer_id', '=', 'rw_offers.of_id')
                ->where('rw_offers.of_article', 'like', '%'.$filters['getOffers.of_article'].'%');
        }

        // Стандартная фильтрация по полям модели (например, `ao_id`)
        return parent::scopeFilter($query, $filters);
    }
}
