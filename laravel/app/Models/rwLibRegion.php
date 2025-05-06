<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class rwLibRegion extends Model
{
    protected $primaryKey = 'lr_id';

    use AsSource, Filterable, Attachable, HasFactory;

    protected $fillable = [
        'lr_name',
    ];
}
