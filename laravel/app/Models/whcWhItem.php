<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class whcWhItem extends Model
{
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected static $warehouseId;

    public static function fromWarehouse($warehouseId)
    {
        $instance = new self();
        $instance->setTable('whc_wh' . $warehouseId . '_items');
        return $instance->newQuery();
    }

    public function getStatus()
    {
        return $this->belongsTo(rwOrderStatus::class, 'whci_status', 'os_id');
    }

    public function getOffer()
    {
        return $this->belongsTo(rwOffer::class, 'whci_offer_id', 'of_id');
    }
}
