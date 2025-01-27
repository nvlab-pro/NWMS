<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use OwenIt\Auditing\Auditable;

class rwBarcode extends Model
{
    protected $primaryKey = 'br_id';
    public $timestamps = false;

    protected $fillable = [
        'br_id',
        'br_offer_id',
        'br_barcode',
    ];

}
