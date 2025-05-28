<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class rwPrintTemplate extends Model
{
    protected $primaryKey = 'pt_id';

    use AsSource, Filterable, Attachable, HasFactory;

    protected $fillable = [
        'pt_domain_id',
        'pt_user_id',
        'pt_name',
        'pt_modul',
        'pt_type',
        'pt_attachment_id',
    ];
}
