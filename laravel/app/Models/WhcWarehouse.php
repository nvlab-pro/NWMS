<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class WhcWarehouse extends Model
{
    protected $primaryKey = 'whc_id';

    use AsSource, Filterable, Attachable, HasFactory, SoftDeletes;

    protected $fillable = [
        'whc_id',
        'whc_ver',
    ];

}
