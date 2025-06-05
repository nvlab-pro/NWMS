<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class rwOrderPacking extends Model implements AuditableContract
{
    protected $primaryKey = 'op_id';

    use AsSource, Filterable, Attachable, HasFactory, Auditable;

    // Другие разрешённые для массового присвоения атрибуты
    protected $fillable = [
        'op_id',
        'op_order_id',
        'op_offer_id',
        'op_user_id',
        'op_barcode',
        'op_data',
        'op_qty',
        'op_pallet',
        'op_box',
        'op_cash',
    ];

    public function getUser()
    {
        return $this->hasOne(User::class, 'id', 'op_user_id');
    }
}
