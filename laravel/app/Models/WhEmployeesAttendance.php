<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use OwenIt\Auditing\Auditable;

class WhEmployeesAttendance extends Model
{
    use AsSource, Filterable, Attachable, HasFactory;

    protected $primaryKey = 'ea_id';

    protected $fillable = [
        'ea_user_id',
        'ea_date',
        'ea_type',
    ];

    public function user() {
        return $this->hasOne(User::class, 'id', 'ea_user_id');
    }

}
