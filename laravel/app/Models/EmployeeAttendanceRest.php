<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use OwenIt\Auditing\Auditable;

class EmployeeAttendanceRest extends Model
{
    use AsSource, Filterable, Attachable, HasFactory;

    protected $table = 'wh_employees_attendances_rests';
    protected $primaryKey = 'ear_id';
    public $timestamps = false;

    protected $fillable = [
        'ear_type',
        'ear_date_from',
        'ear_date_to',
        'ear_user_id',
        'ear_comment',
    ];

    protected $casts = [
        'ear_date_from' => 'date',
        'ear_date_to' => 'date',
    ];

    public function user() {
        return $this->hasOne(User::class, 'id', 'ear_user_id');
    }

}
