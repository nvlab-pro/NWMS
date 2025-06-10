<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class rwOrderDsStatus extends Model
{
    protected $primaryKey = 'odss_id';
//    protected $table = 'rw_lib_status';

    use AsSource, Filterable, Attachable, HasFactory;

    public static function perPage(): int
    {
        return 50;
    }
}
