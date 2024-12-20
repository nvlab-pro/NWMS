<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class rwLibStatus extends Model
{
    protected $primaryKey = 'ls_id';
    protected $table = 'rw_lib_status';

    use AsSource, Filterable, Attachable, HasFactory;

    public static function perPage(): int
    {
        return 50;
    }}
