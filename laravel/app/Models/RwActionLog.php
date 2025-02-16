<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RwActionLog extends Model
{
    protected $primaryKey = 'al_id';
    public $timestamps = false;

    use HasFactory;

    protected $fillable = [
        'al_id',
        'al_wh_id',
        'al_action_id',
        'al_date',
        'al_user_id',
        'al_entity_id',
        'al_qty',
        'al_transaction_id',
    ];
}
