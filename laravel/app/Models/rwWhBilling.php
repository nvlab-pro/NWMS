<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class rwWhBilling extends Model
{
    use AsSource, Filterable, Attachable, HasFactory;

    protected $primaryKey = 'wb_id';

    protected $fillable = [
        'wb_date',
        'wb_wh_id',
        'wb_billing_id',
        'wb_comment',
    ];

    public function billingSetting()
    {
        return $this->belongsTo(rwBillingSetting::class, 'wb_billing_id', 'bs_id');
    }
}
