<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class rwLibCity extends Model
{
    protected $primaryKey = 'lcit_id';

    use AsSource, Filterable, Attachable, HasFactory, SoftDeletes;

    public static function perPage(): int
    {
        return 50;
    }
    public function getCountry() {
        return $this->hasOne(rwLibCountry::class, 'lco_id', 'lcit_country_id');
    }
}
