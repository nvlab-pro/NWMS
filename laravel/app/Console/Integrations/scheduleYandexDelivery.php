<?php

namespace App\Console\Integrations;

use App\Integrations\YandexDostavka\YandexDeliveryService;
use App\Models\rwIntegration;
use App\Models\rwLibCity;
use App\Models\rwLibRegion;
use App\Models\rwPickupPoint;

class scheduleYandexDelivery
{
    public static function getPickUpsList()
    {

        $integrationsList = rwIntegration::where('int_status', 1)
            ->where('int_ds_id', 5)
            ->get();

        foreach ($integrationsList as $integration) {


            $long = 25;
            $iteration = 0;
            $service = new YandexDeliveryService(
                $integration->int_url,
                $integration->int_token,
                $integration->int_pickup_point
            );

            while($iteration < 300) {

                $pickUps = $service->getPickupPoints([
                    'latitude' => ['from' => 30.00000, 'to' => 71.00000],
                    'longitude' =>['from' => $long, 'to' => $long+1],
                ]);

                dump($long . ' ' . '('.$iteration.')');

                $tmp = 0;

                foreach ($pickUps['points'] as $pickUp) {
                    $address = $pickUp['address'] ?? [];
                    $contact = $pickUp['contact'] ?? [];
                    $schedule = $pickUp['schedule'] ?? [];

                    $regionName = trim($address['region'] ?? '');
                    $cityName = trim($address['locality'] ?? '');

                    // 1. Найти или создать регион
                    $region = null;
                    if ($regionName !== '') {
                        $region = rwLibRegion::firstOrCreate(
                            ['lr_name' => $regionName],
                            [] // можно добавить created_by и пр. при необходимости
                        );
                    }

                    // 2. Найти или создать город
                    $city = null;
                    if ($cityName !== '') {
                        $city = rwLibCity::firstOrCreate(
                            ['lcit_name' => $cityName],
                            [
                                'lcit_country_id' => 1,
                                'lcit_coord_latitude' => $pickUp['position']['latitude'] ?? 0,
                                'lcit_coord_longitude' => $pickUp['position']['longitude'] ?? 0,
                            ]
                        );
                    }

                    rwPickupPoint::updateOrCreate(
                        ['pp_ext_id' => $pickUp['id']],
                        [
                            'pp_status' => 1,
                            'pp_update' => 1,
                            'pp_station_id' => $pickUp['operator_station_id'] ?? null,
                            'pp_name' => $pickUp['name'] ?? null,
                            'pp_type' => $pickUp['type'] ?? null,
                            'pp_position_latitude' => $pickUp['position']['latitude'] ?? null,
                            'pp_position_longitude' => $pickUp['position']['longitude'] ?? null,
                            'pp_geoId' => $address['geoId'] ?? null,
                            'pp_country_id' => 1, // или логика аналогичная региону
                            'pp_region_id' => $region?->lr_id,
                            'pp_city_id' => $city?->lcit_id,
                            'pp_street' => $address['street'] ?? null,
                            'pp_house' => $address['house'] ?? null,
                            'pp_apartment' => $address['apartment'] ?? null,
                            'pp_building' => $address['building'] ?? null,
                            'pp_postal_code' => $address['postal_code'] ?? null,
                            'pp_full_address' => $address['full_address'] ?? null,
                            'pp_payed' => in_array('already_paid', $pickUp['payment_methods'] ?? []) ? 1 : 0,
                            'pp_phone' => $contact['phone'] ?? null,
                            'pp_schedule' => json_encode($schedule['restrictions'] ?? []),
                            'pp_comment' => $address['comment'] ?? null,
                            'pp_instruction' => $pickUp['instruction'] ?? null,
                        ]
                    );

                    $tmp++;
                }
                if ($tmp == 0) $iteration++;

                $long = $long + 0.1;
            }

        }

    }

    public static function uploadOrdersToYandexDelivery()
    {

    }
}