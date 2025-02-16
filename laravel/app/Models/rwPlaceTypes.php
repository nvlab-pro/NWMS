<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class rwPlaceTypes extends Model
{
    protected $primaryKey = 'pt_id';
    public $timestamps = false;

    use AsSource, Filterable, Attachable, HasFactory;

}
