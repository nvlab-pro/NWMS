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

        $ydOrderId = 0;

        $baseUrl = $resOrder->getDs->getSource->int_url;
        $token = $resOrder->getDs->getSource->int_token;
        $pickUpPointFrom = $resOrder->getDs->getSource->int_pickup_point;
        $pickUpPointTo = $resOrder->getDs->ods_ds_pp_id;
        $this->dsId = $resOrder->getDs->ods_ds_id;

        // Если это Яндекс Доставка
        if($this->dsId == 5) {
            $yd = new YandexDeliveryService($baseUrl, $token, $pickUpPointTo);
            $ydOrderId = $yd->uploadOrderToDeliveryService($resOrder);

            $this->ydOrderId = $ydOrderId;
        }

        return $ydOrderId;

    }

    public function getOrderLable($resOrder, $dsOrderId)
    {

        // Если это Яндекс Доставка
        if($this->dsId == 5) {

            $baseUrl = $resOrder->getDs->getSource->int_url;
            $token = $resOrder->getDs->getSource->int_token;
            $pickUpPointTo = $resOrder->getDs->ods_ds_pp_id;

            $yd = new YandexDeliveryService($baseUrl, $token, $pickUpPointTo);
            $ydOrderId = $yd->getOrderLable($dsOrderId);

            dump($ydOrderId);

        }

        return $ydOrderId;

    }

}