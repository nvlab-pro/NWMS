<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use OwenIt\Auditing\Auditable;

class rwPickupPoint extends Model
{
    protected $primaryKey = 'pp_id';

    use AsSource, Filterable, Attachable, HasFactory;

    protected $fillable = [
        'pp_status',
        'pp_update',
        'pp_ext_id',
        'pp_station_id',
        'pp_name',
        'pp_type',
        'pp_position_latitude',
        'pp_position_longitude',
        'pp_geoId',
        'pp_country_id',
        'pp_region_id',
        'pp_city_id',
        'pp_street',
        'pp_house',
        'pp_apartment',
        'pp_building',
        'pp_postal_code',
        'pp_full_address',
        'pp_payed',
        'pp_phone',
        'pp_schedule',
        'pp_comment',
        'pp_instruction',
    ];

}
