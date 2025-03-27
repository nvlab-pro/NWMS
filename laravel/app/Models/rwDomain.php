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
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class rwDomain extends Model implements AuditableContract
{
    protected $primaryKey = 'dm_id';
    protected $table = 'rw_domains';

    use AsSource, Filterable, Attachable, HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'dm_name',
        'dm_country_id',
    ];

    protected $allowedFilters = [
        'dm_name' => Where::class,
        'dm_country_id' => Where::class,
    ];

    protected $allowedSorts = [
        'dm_name',
        'dm_country_id',
    ];

    public function getCountry() {
        return $this->hasOne(rwLibCountry::class, 'lco_id', 'dm_country_id');
    }

}
