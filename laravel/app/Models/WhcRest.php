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
    ];

    public function getPlace() {
        return $this->hasOne(rwPlaces::class, 'pl_id', 'whcr_place_id');
    }

}
