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

class rwSettingsSoa extends Model implements AuditableContract
{
    protected $primaryKey = 'ssoa_id';

    use AsSource, Filterable, Attachable, HasFactory, Auditable;

    // Другие разрешённые для массового присвоения атрибуты
    protected $fillable = [
        'ssoa_status_id',
        'ssoa_priority',
        'ssoa_domain_id',
        'ssoa_wh_id',
        'ssoa_user_id',
        'ssoa_date_from',
        'ssoa_date_to',
        'ssoa_offers_count_from',
        'ssoa_offers_count_to',
        'ssoa_order_from',
        'ssoa_order_to',
        'ssoa_ds_id',
        'ssoa_name',
        'ssoa_lable_id',
        'ssoa_doc_id',
        'ssoa_count_ready',
        'ssoa_count_process',
        'ssoa_count_send',
        'ssoa_finish_place_type',
        'ssoa_all_offers',
    ];

    public function getWarehouse() {
        return $this->hasOne(rwWarehouse::class, 'wh_id', 'ssoa_wh_id');
    }

    public function getUser() {
        return $this->hasOne(User::class, 'id', 'ssoa_user_id');
    }

    public function getStatus() {
        return $this->hasOne(rwLibStatus::class, 'ls_id', 'ssoa_status_id');
    }

    public function getDS() {
        return $this->hasOne(rwDeliveryService::class, 'ds_id', 'ssoa_ds_id');
    }

    public function getFinishPlace() {
        return $this->hasOne(rwPlaceTypes::class, 'pt_id', 'ssoa_finish_place_type');
    }

}
