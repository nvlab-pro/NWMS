<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class rwLibTypeDoc extends Model
{
    protected $primaryKey = 'td_id';
    public $timestamps = false;

    use AsSource, Filterable, Attachable, HasFactory;
}
