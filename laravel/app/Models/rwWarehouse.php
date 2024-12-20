<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class rwWarehouse extends Model
{
    protected $primaryKey = 'wh_id';

    use AsSource, Filterable, Attachable, HasFactory, SoftDeletes;

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


}
