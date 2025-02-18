<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

class rwPlace extends Model
{
    protected $primaryKey = 'pl_id';

    use AsSource, Filterable, Attachable, HasFactory, SoftDeletes;

    protected $fillable = [
        'pl_domain_id',
        'pl_wh_id',
        'pl_type',
        'pl_room',
        'pl_floor',
        'pl_section',
        'pl_row',
        'pl_rack',
        'pl_shelf',
    ];

    protected $allowedFilters = [
        'pl_wh_id'          => Where::class,
        'pl_type'           => Where::class,
        'pl_room'           => Like::class,
        'pl_floor'          => Like::class,
        'pl_section'        => Like::class,
    ];

    protected $allowedSorts = [
        'pl_wh_id',
        'pl_type',
        'pl_room',
        'pl_floor',
        'pl_section',
        'pl_row',
        'pl_rack',
        'pl_shelf',
    ];

    public function getWh() {
        return $this->hasOne(rwWarehouse::class, 'wh_id', 'pl_wh_id');
    }

    public function getType() {
        return $this->hasOne(rwPlaceTypes::class, 'pt_id', 'pl_type');
    }

}
