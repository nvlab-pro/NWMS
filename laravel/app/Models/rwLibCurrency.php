<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class rwLibCurrency extends Model
{
    protected $primaryKey = 'lcur_id';

    use AsSource, Filterable, Attachable, HasFactory, SoftDeletes;

    public static function perPage(): int
    {
        return 50;
    }
}
