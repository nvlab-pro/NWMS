<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class rwStatsRest extends Model
{
    protected $primaryKey = 'sr_id';
    public $timestamps = false;

    use AsSource, Filterable, Attachable, HasFactory;

    protected $fillable = [
        'sr_date',
        'sr_offer_id',
        'sr_wh_id',
        'sr_count',
        'sr_reserved_count',
    ];
}
