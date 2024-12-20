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

class rwAcceptanceOffer extends Model
{
    protected $primaryKey = 'ao_id';

    use AsSource, Filterable, Attachable, HasFactory, SoftDeletes;

    protected $allowedFilters = [
        'ao_offer_id'            => Where::class,
        'ao_expected'       => Where::class,
        'ao_accepted'        => Where::class,
        'ao_price'        => Where::class,
        'ao_expiration_date'        => Like::class,
    ];

    /**
     * @var array
     */
    protected $allowedSorts = [
        'ao_offer_id',
        'ao_expected',
        'ao_accepted',
        'ao_price',
        'ao_expiration_date',
    ];

    public static function perPage(): int
    {
        return 50;
    }
    public function getOffers() {
        return $this->hasOne(rwOffer::class, 'of_id', 'ao_offer_id');
    }
}
