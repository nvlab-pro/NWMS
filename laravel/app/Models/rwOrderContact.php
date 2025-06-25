<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class rwOrderContact extends Model implements AuditableContract
{
    use AsSource, Filterable, Attachable, HasFactory, Auditable;
    protected $primaryKey = 'oc_id';

    protected $fillable = [
        'oc_order_id',
        'oc_first_name',
        'oc_middle_name',
        'oc_last_name',
        'oc_phone',
        'oc_email',
        'oc_country_id',
        'oc_city_id',
        'oc_postcode',
        'oc_coord_latitude',
        'oc_coord_longitude',
        'oc_full_address',
    ];

    public function getCity()
    {
        return $this->belongsTo(rwLibCity::class, 'oc_city_id', 'lcit_id');
    }

}
