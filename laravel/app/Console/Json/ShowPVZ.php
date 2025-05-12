<?php

namespace App\Console\Json;

use App\Models\rwLibCity;
use App\Models\rwPickupPoint;
use Illuminate\Http\Request;

class ShowPVZ
{
    function __construct() {

    }

    function showYDPvz(Request $request) {

        if ($request->has('city')) {

            $city = $request->get('city');

            $cityId = rwLibCity::where('lcit_name', $city)->first();

            if (isset($cityId->lcit_id)) {

                $dbPPList = rwPickupPoint::where('pp_city_id', $cityId->lcit_id)
                    ->get();

                $arPPList = [];

                foreach ($dbPPList as $item) {

                    $arPPList[] = [
                        "id" => $item->pp_id,
                        "ext_id" => $item->pp_ext_id,
                        "rw_ds_id" => 1449,
                        "oa_ds_id" => 66,
                        "name" => $item->pp_name,
                        "company" => "Яндекс Доставки",
                        "about_company" => "",
                        "sum" => 300,
                        "time_work" => "",
                        "adress" => $item->pp_full_address,
                        "img_map" => "",
                        "geo_x" => $item->pp_position_latitude,
                        "geo_y" => $item->pp_position_longitude,
                        "delivery_name" => $item->pp_name,
                        "delivery_desc" => "",
                        "delivery_type" => 2,
                        "delivery_period" => 2,
                        "prepaid_only" => 0,
                        "version" => 2
                    ];

                }

                return response(
                    implode(",\n", array_map(fn($i) => json_encode($i, JSON_UNESCAPED_UNICODE), $arPPList)),
                    200,
                    ['Content-Type' => 'application/json']
                );

            } else {
//                return response()->json(['error' => 'City not found'], 404);
            }

        } else {
//            return response()->json(['error' => 'Missing city parameter'], 400);
        }

    }

}