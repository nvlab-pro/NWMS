<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rwLibAcceptStatus extends Model
{
    protected $primaryKey = 'las_id';
    protected $table = 'rw_lib_accept_status';

    use HasFactory;
}
