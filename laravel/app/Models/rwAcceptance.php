<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class rwAcceptance extends Model
{
    protected $primaryKey = 'acc_id';

    use AsSource, Filterable, Attachable, HasFactory, SoftDeletes;

    public static function perPage(): int
    {
        return 50;
    }

    public function getAccStatus() {
        return $this->hasOne(rwLibAcceptStatus::class, 'las_id', 'acc_status');
    }
    public function getWarehouse() {
        return $this->hasOne(rwWarehouse::class, 'wh_id', 'acc_wh_id');
    }

}
