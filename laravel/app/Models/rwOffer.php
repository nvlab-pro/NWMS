<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDate;
use Orchid\Filters\Types\WhereMaxMin;
use Orchid\Filters\Types\WhereDateStartEnd;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class rwOffer extends Model implements AuditableContract
{
    protected $primaryKey = 'of_id';

    use AsSource, Filterable, Attachable, HasFactory, SoftDeletes, Auditable;

    // Другие разрешённые для массового присвоения атрибуты
    protected $fillable = [
        'of_dimension_x',
        'of_dimension_y',
        'of_dimension_z',
        'of_weight',
    ];

    protected $allowedFilters = [
        'of_id'            => Where::class,
        'of_shop_id'       => Where::class,
        'of_ext_id'        => Where::class,
        'of_status'        => Where::class,
        'of_article'        => Like::class,
        'of_sku'            => Like::class,
        'of_name'           => Like::class,
        'of_price'          => Where::class,
        'of_estimated_price'    => Where::class,
    ];

    /**
     * @var array
     */
    protected $allowedSorts = [
        'id',
    ];

    public static function perPage(): int
    {
        return 50;
    }
    public function getStatus() {
        return $this->hasOne(rwLibStatus::class, 'ls_id', 'of_status');
    }

    public function getShop() {
        return $this->hasOne(rwShop::class, 'sh_id', 'of_shop_id');
    }

}
