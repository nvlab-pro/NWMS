<?php

namespace App\Console\Integrations;

use App\Integrations\YandexDostavka\YandexDeliveryService;
use App\Models\rwIntegration;

class scheduleYandexDelivery
{
    public static function getPickUpsList()
    {

        $integrationsList = rwIntegration::where('int_status', 1)
            ->where('int_ds_id', 5)
            ->get();

        foreach ($integrationsList as $integration) {

            $service = new YandexDeliveryService($integration->int_url, $integration->int_token, $integration->int_pickup_point);
            $pickUps = $service->getPickupPoints();

            foreach ($pickUps['points'] as $pickUp) {

                dump($pickUp);
                dump('-----------------------');

            }

        }

    }
}