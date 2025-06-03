<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class rwCompany extends Model implements AuditableContract
{
    protected $primaryKey = 'co_id';

    use AsSource, Filterable, Attachable, HasFactory, Auditable;

    protected $fillable = [
        'co_domain_id',
        'co_name',
        'co_legal_name',
        'co_vat_number',
        'co_registration_number',
        'co_country_id',
        'co_city_id',
        'co_postcode',
        'co_address',
        'co_phone',
        'co_email',
        'co_website',
        'co_bank_account',
        'co_bank_ks',
        'co_bank_name',
        'co_swift_bic',
        'co_contact_person',
    ];

}
