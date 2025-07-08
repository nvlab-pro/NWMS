<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use OwenIt\Auditing\Auditable;

class rwBillingAct extends Model
{
    use AsSource, Filterable, Attachable, HasFactory, Auditable;

    protected $primaryKey = 'ba_id';

    protected $fillable = [
        'ba_wh_id',
        'ba_status',
        'ba_date_start',
        'ba_date_end',
        'ba_customer_company_id',
        'ba_executor_company_id',
        'ba_sum',
        'ba_tax_sum',
        'ba_sum_total',
    ];

    public function getCustomerCompany() {
        return $this->hasOne(rwCompany::class, 'co_id', 'ba_customer_company_id');
    }

    public function getExecutorCompany() {
        return $this->hasOne(rwCompany::class, 'co_id', 'ba_executor_company_id');
    }

}
