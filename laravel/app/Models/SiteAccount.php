<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class SiteAccount extends Model
{
    protected $primaryKey = 'sa_id';

    use AsSource, Filterable, Attachable, HasFactory;

    protected $fillable = [
        'sa_lang',
        'sa_first_name',
        'sa_last_name',
        'sa_email',
        'sa_password',
        'sa_timecash',
        'sa_comment',
    ];
}
