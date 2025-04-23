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
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class rwAcceptance extends Model implements AuditableContract
{
    protected $primaryKey = 'acc_id';

    use AsSource, Filterable, Attachable, HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'acc_domain_id',
        'acc_status',
        'acc_ext_id',
        'acc_user_id',
        'acc_wh_id',
        'acc_date',
        'acc_type',
        'acc_domain_id',
        'acc_shop_id',
        'acc_comment',
        'acc_count_expected',
        'acc_count_accepted',
        'acc_count_placed',
    ];

    protected $allowedFilters = [
        'acc_id' => Where::class,
        'acc_status' => Where::class,
        'acc_type' => Where::class,
        'acc_ext_id' => Where::class,
        'acc_date' => Like::class,
        'acc_wh_id' => Where::class,
        'acc_comment' => Like::class,
    ];

    protected $allowedSorts = [
        'acc_status',
        'acc_ext_id',
        'acc_wh_id',
        'acc_date',
        'acc_type',
    ];
    public static function perPage(): int
    {
        return 50;
    }

    public function getAccStatus() {
        return $this->hasOne(rwLibAcceptStatus::class, 'las_id', 'acc_status');
    }
    public function getAccType() {
        return $this->hasOne(rwLibAcceptType::class, 'lat_id', 'acc_type');
    }
    public function getWarehouse() {
        return $this->hasOne(rwWarehouse::class, 'wh_id', 'acc_wh_id');
    }

    public function getUser() {
        return $this->hasOne(User::class, 'id', 'acc_user_id');
    }
    public function offers()
    {
        return $this->hasMany(rwAcceptanceOffer::class, 'ao_acceptance_id', 'acc_id');
    }
}
