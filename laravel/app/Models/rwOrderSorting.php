<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class rwOrderSorting extends Model implements AuditableContract
{
    protected $primaryKey = 'os_id';

    use AsSource, Filterable, Attachable, HasFactory, Auditable;

    // Другие разрешённые для массового присвоения атрибуты
    protected $fillable = [
        'os_id',
        'os_user_id',
        'os_order_id',
        'os_offer_id',
        'os_place_id',
        'os_qty',
        'os_barcode',
        'os_data',
        'os_cash',
    ];
    public function getUser()
    {
        return $this->hasOne(User::class, 'id', 'op_user_id');
    }
}
