<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class rwOrderDs extends Model implements AuditableContract
{
    use AsSource, Filterable, Attachable, HasFactory, Auditable;
    protected $primaryKey = 'ods_id';

    protected $fillable = [
        'ods_id',
        'ods_ds_id',
        'ods_status',
        'ods_ds_pp_id',
        'ods_track_number',
    ];

    public function getDsName()
    {
        return $this->belongsTo(rwDeliveryService::class, 'ds_id', 'ods_ds_id');
    }

}
