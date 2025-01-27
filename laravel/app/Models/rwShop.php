<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class rwShop extends Model
{
    protected $primaryKey = 'sh_id';

    use AsSource, Filterable, Attachable, HasFactory, SoftDeletes;

    protected $fillable = [
        'sh_id',
        'sh_domain_id',
        'sh_user_id',
        'sh_name',
    ];

    public static function perPage(): int
    {
        return 50;
    }
    public function getOwner() {
        return $this->hasOne(User::class, 'id', 'sh_user_id');
    }

}
