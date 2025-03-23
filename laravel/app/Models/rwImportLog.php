<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class rwImportLog extends Model
{
    protected $primaryKey = 'il_id';

    use AsSource, Filterable, Attachable, HasFactory;

    // Другие разрешённые для массового присвоения атрибуты
    protected $fillable = [
        'il_import_id',
        'il_date',
        'il_operation',
        'il_name',
        'il_fields',
    ];

    protected $casts = [
        'il_fields' => 'array',
    ];
}
