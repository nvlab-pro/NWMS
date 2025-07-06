<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereBetween;
use Orchid\Screen\AsSource;

class rwBillingTransactions extends Model
{
    protected $primaryKey = 'bt_id';

    use AsSource, Filterable, Attachable, HasFactory;

    protected $fillable = [
        'bt_date',
        'bt_service_id',
        'bt_shop_id',
        'bt_billing_id',
        'bt_customer_company_id',
        'bt_executor_company_id',
        'bt_doc_id',
        'bt_entity_count',
        'bt_sum',
        'bt_act_id',
        'bt_desc',
    ];


    protected $allowedFilters = [
        'bt_date'       => Where::class,
        'bt_service_id'       => Where::class,
        'bt_shop_id'       => Where::class,
        'bt_billing_id'         => Where::class,
        'bt_customer_company_id'       => Where::class,
        'bt_executor_company_id'   => Where::class,
        'bt_entity_count'          => Where::class,
        'bt_act_id'         => Where::class,
    ];

    protected $allowedSorts = [
        'bt_sum',
        'bt_tax',
        'bt_total_sum',
    ];

    public function actionType() {
        return $this->hasOne(rwLibActionType::class, 'lat_id', 'bt_service_id');
    }

    public function customerCompany() {
        return $this->hasOne(rwCompany::class, 'co_id', 'bt_customer_company_id');
    }

    public function executorCompany() {
        return $this->hasOne(rwCompany::class, 'co_id', 'bt_executor_company_id');
    }

    public function getWarehouse() {
        return $this->hasOne(rwWarehouse::class, 'wh_id', 'bt_wh_id');
    }

}