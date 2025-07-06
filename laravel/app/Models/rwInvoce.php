<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class rwInvoce extends Model implements AuditableContract
{
    use AsSource, Filterable, Attachable, HasFactory, Auditable;

    protected $primaryKey = 'in_id';

    protected $fillable = [
        'in_status',
        'in_date',
        'wh_user_id',
        'in_wh_id',
        'in_customer_company_id',
        'in_executor_company_id',
        'in_sum',
        'in_tax',
        'in_total_sum',
    ];

    public function getCustomerCompany() {
        return $this->hasOne(rwCompany::class, 'co_id', 'in_customer_company_id');
    }

    public function getExecutorCompany() {
        return $this->hasOne(rwCompany::class, 'co_id', 'in_executor_company_id');
    }

}
