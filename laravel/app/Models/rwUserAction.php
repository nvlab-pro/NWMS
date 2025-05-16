<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class rwUserAction extends Model
{
    protected $primaryKey = 'ua_id';

    use AsSource, Filterable, Attachable, HasFactory;

    protected $fillable = [
        'ua_user_id',
        'ua_lat_id',
        'ua_domain_id',
        'ua_wh_id',
        'ua_shop_id',
        'ua_place_id',
        'ua_entity_type',
        'ua_entity_id',
        'ua_quantity',
        'ua_time_start',
        'ua_time_end',
        'ua_transaction_id',
    ];

    protected $casts = [
        'ua_time_start' => 'datetime',
        'ua_time_end'   => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'ua_user_id');
    }

    public function actionType() {
        return $this->belongsTo(rwLibActionType::class, 'ua_lat_id');
    }

}
