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

class rwOrderAssembly extends Model implements AuditableContract
{
    protected $primaryKey = 'oa_id';

    use AsSource, Filterable, Attachable, HasFactory, Auditable;

    // Другие разрешённые для массового присвоения атрибуты
    protected $fillable = [
        'oa_order_id',
        'oa_offer_id',
        'oa_user_id',
        'oa_place_id',
        'oa_data',
        'oa_qty',
        'oa_cash',
    ];
}
