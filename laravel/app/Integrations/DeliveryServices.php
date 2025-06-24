<?php

namespace App\Integrations;

use App\Integrations\YandexDostavka\YandexDeliveryService;
use App\Models\rwOrder;

class DeliveryServices
{
    private $dsId, $ydOrderId;

    public function __construct($dsId)
    {
        $this->dsId = $dsId;
    }

    public function uploadOrderToDeliveryService($resOrder)
    {
//        $currentLabel = 0;

//        $resOrder = rwOrder::where('o_domain_id', $currentUser->domain_id)
//            ->where('o_id', $orderId)
//            ->with(['offers', 'getMeasurements', 'getDs.getSource', 'getDs.getDsName'])
//            ->first();

        $baseUrl = $resOrder->getDs->getSource->int_url;
        $token = $resOrder->getDs->getSource->int_token;
        $pickUpPointFrom = $resOrder->getDs->getSource->int_pickup_point;
        $pickUpPointTo = $resOrder->getDs->ods_ds_pp_id;

        // Если это Яндекс Доставка
        if($this->dsId == 5) {
            $yd = new YandexDeliveryService($baseUrl, $token, $pickUpPointTo);
            $ydOrderId = $yd->uploadOrderToDeliveryService($resOrder);
        }

        $this->ydOrderId = $ydOrderId;

        return $ydOrderId;

    }

}