<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class rwSettingsProcPacking extends Model implements AuditableContract
{
    protected $primaryKey = 'spp_id';

    use AsSource, Filterable, Attachable, HasFactory, Auditable;

    protected $fillable = [
        'spp_status_id',
        'spp_priority',
        'spp_domain_id',
        'spp_wh_id',
        'spp_user_id',
        'spp_name',
        'spp_start_place_type',
        'spp_place_rack_from',
        'spp_place_rack_to',
        'spp_packing_type',
    ];

    public function getWarehouse() {
        return $this->hasOne(rwWarehouse::class, 'wh_id', 'spp_wh_id');
    }

    public function getUser() {
        return $this->hasOne(User::class, 'id', 'spp_user_id');
    }

    public function getStatus() {
        return $this->hasOne(rwLibStatus::class, 'ls_id', 'spp_status_id');
    }

    public function getStartPlace() {
        return $this->hasOne(rwPlaceTypes::class, 'pt_id', 'spp_start_place_type');
    }

}
