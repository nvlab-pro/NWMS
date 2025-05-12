<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class rwSettingsMarking extends Model
{
    protected $primaryKey = 'sm_id';

    use AsSource, Filterable, Attachable, HasFactory;

    protected $fillable = [
        'sm_status_id',
        'sm_priority',
        'sm_domain_id',
        'sm_user_id',
        'sm_name',
        'sm_ds_id',
        'sm_date_from',
        'sm_date_to',
    ];

    public function getStatus() {
        return $this->hasOne(rwLibStatus::class, 'ls_id', 'sm_status_id');
    }

    public function getDS() {
        return $this->hasOne(rwDeliveryService::class, 'ds_id', 'sm_ds_id');
    }

    public function getUser() {
        return $this->hasOne(User::class, 'id', 'sm_user_id');
    }

}
