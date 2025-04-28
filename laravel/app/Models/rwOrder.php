<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereBetween;
use Orchid\Filters\Types\WhereIn;
use Orchid\Screen\AsSource;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class rwOrder extends Model implements AuditableContract
{
    protected $primaryKey = 'o_id';

    use AsSource, Filterable, Attachable, HasFactory, SoftDeletes, Auditable;

    // Другие разрешённые для массового присвоения атрибуты
    protected $fillable = [
        'o_status_id',
        'o_domain_id',
        'o_parcel_id',
        'o_type_id',
        'o_client_id',
        'o_ext_id',
        'o_shop_id',
        'o_user_id',
        'o_wh_id',
        'o_date',
        'o_date_send',
        'o_source_id',
        'o_count',
        'o_sum',
        'o_operation_user_id',
    ];

    protected $allowedFilters = [
        'o_id'       => Where::class,
        'o_status_id'       => Where::class,
        'o_parcel_id'       => Where::class,
        'o_type_id'         => Where::class,
        'o_client_id'       => Where::class,
        'o_ext_id'          => Where::class,
        'o_shop_id'         => Where::class,
        'o_wh_id'           => Where::class,
        'o_date'            => WhereBetween::class,
        'o_date_send'       => WhereBetween::class,
    ];

    /**
     * @var array
     */
    protected $allowedSorts = [
        'o_status_id',
    ];

    public function getShop() {
        return $this->hasOne(rwShop::class, 'sh_id', 'o_shop_id');
    }

    public function getStatus() {
        return $this->hasOne(rwOrderStatus::class, 'os_id', 'o_status_id');
    }

    public function getType() {
        return $this->hasOne(rwOrderType::class, 'ot_id', 'o_type_id');
    }

    public function getWarehouse() {
        return $this->hasOne(rwWarehouse::class, 'wh_id', 'o_wh_id');
    }

    public function getPlace() {
        return $this->hasOne(rwPlace::class, 'pl_id', 'o_order_place');
    }
    public function offers()
    {
        return $this->hasMany(rwOrderOffer::class, 'oo_order_id', 'o_id');
    }
}
