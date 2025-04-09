<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class rwOrderOffer extends Model implements AuditableContract
{
    protected $primaryKey = 'oo_id';

    use AsSource, Filterable, Attachable, HasFactory, Auditable;

    // Другие разрешённые для массового присвоения атрибуты
    protected $fillable = [
        'oo_order_id',
        'oo_offer_id',
        'oo_qty',
        'oo_oc_price',
        'oo_price',
        'oo_expiration_date',
        'oo_batch',
        'oo_operation_user_id',
        'oo_cash',
    ];

    public function getOffer() {
        return $this->hasOne(rwOffer::class, 'of_id', 'oo_offer_id');
    }

    public function getPackingOffer() {
        return $this->hasOne(rwOrderPacking::class, 'op_id', 'oo_id');
    }

    public function getPlaces(): HasMany
    {
        return $this->hasMany(whcRest::class, 'whcr_offer_id', 'oo_offer_id');
    }

}
