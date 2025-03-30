<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use OwenIt\Auditing\Auditable;

class rwDatamatrix extends Model
{
    protected $table = 'rw_datamatrix';
    protected $primaryKey = 'dmt_id';
    public $timestamps = false;

    use AsSource, Filterable, Attachable, HasFactory;

    protected $fillable = [
        'dmt_status',
        'dmt_shop_id',
        'dmt_order_id',
        'dmt_barcode',
        'dmt_short_code',
        'dmt_crypto_tail',
        'dmt_datamatrix',
        'dmt_created_date',
        'dmt_used_date',
    ];
}
