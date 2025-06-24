<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class rwOrderMeasurement extends Model
{
    protected $primaryKey = 'om_id';
    public $timestamps = false;

    use AsSource, Filterable, Attachable, HasFactory;

    protected $fillable = [
        'om_id',
        'om_x',
        'om_y',
        'om_z',
        'om_weight',
    ];

}
