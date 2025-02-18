<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class rwWarehouse extends Model implements AuditableContract
{
    use AsSource, Filterable, Attachable, HasFactory, SoftDeletes, Auditable;

    protected $primaryKey = 'wh_id';

    // Другие разрешённые для массового присвоения атрибуты
    protected $fillable = [
        'wh_id',
        'wh_user_id',
        'wh_type',
        'wh_parent_id',
        'wh_name',
        'wh_domain_id',
    ];

    public static function perPage(): int
    {
        return 50;
    }
    public function getOwner() {
        return $this->hasOne(User::class, 'id', 'wh_user_id');
    }

    public function getWhType() {
        return $this->hasOne(rwLibWhType::class, 'lwt_id', 'wh_type');
    }

    public function getDomain() {
        return $this->hasOne(rwDomain::class, 'dm_id', 'wh_domain_id');
    }

    public function getParent() {
        return $this->hasOne(rwWarehouse::class, 'wh_id', 'wh_parent_id');
    }


}
