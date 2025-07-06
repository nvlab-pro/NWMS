<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class rwBillingSetting extends Model implements AuditableContract
{
    protected $primaryKey = 'bs_id';

    use AsSource, Filterable, Attachable, HasFactory, Auditable;

    protected $fillable = [
        'bs_domain_id',
        'bs_data',
        'bs_status',
        'bs_name',
        'bs_rates',
        'bs_fields',
        'bs_date_type',
    ];

}
