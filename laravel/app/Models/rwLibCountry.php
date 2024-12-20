<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class rwLibCountry extends Model
{
    protected $primaryKey = 'lco_id';

    use AsSource, Filterable, Attachable, HasFactory, SoftDeletes;

    public static function perPage(): int
    {
        return 50;
    }

    public function getCurrency() {
        return $this->hasOne(rwLibCurrency::class, 'lcur_id', 'lco_currency_id');
    }
    public function getLanguage() {
        return $this->hasOne(rwLibLanguage::class, 'llang_id', 'lco_lang_id');
    }
    public function getWeight() {
        return $this->hasOne(rwLibWeight::class, 'lw_id', 'lco_weight_id');
    }
    public function getLength() {
        return $this->hasOne(rwLibLength::class, 'llen_id', 'lco_length_id');
    }
}
