<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class WhcRest extends Model
{
    protected $primaryKey = 'whcr_id';
    public $timestamps = false;

    use AsSource, Filterable, Attachable, HasFactory;

    protected $fillable = [
        'whcr_offer_id',
        'whcr_wh_id',
        'whcr_count',
        'whcr_reserved_count',
        'whcr_date',
        'whcr_place_id',
        'whcr_production_date',
        'whcr_expiration_date',
        'whcr_batch',
        'whcr_updated',
    ];

    public function getPlace() {
        return $this->hasOne(rwPlace::class, 'pl_id', 'whcr_place_id');
    }

}
