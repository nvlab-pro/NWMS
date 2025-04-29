<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class rwIntegration extends Model
{
    use HasFactory, AsSource;

    protected $primaryKey = 'int_id';
    protected $table = 'rw_integrations';

    protected $fillable = [
        'int_domain_id',
        'int_user_id',
        'int_ds_id',
        'int_type',
        'int_name',
        'int_url',
        'int_token',
    ];

    public function getDS() {
        return $this->hasOne(rwDeliveryService::class, 'ds_id', 'int_ds_id');
    }

}