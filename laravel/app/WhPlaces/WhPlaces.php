<?php

namespace App\WhPlaces;

use App\Models\rwPlaces;

class WhPlaces
{
    private $barcode, $placeId;

    public function __construct($barcode)
    {
        $this->barcode = $barcode;
        $this->placeId = 0;

        $arBarcode = explode("*", $barcode);

        if ($arBarcode[0] == "102") {

            $controlSum = $arBarcode[0] + $arBarcode[1];

            if ($controlSum == $arBarcode[2]) {

                $this->placeId = $arBarcode[1];

            }

        }

        return $this->placeId;
    }

    function getPlaceId()
    {
        return $this->placeId;
    }

    function getPlaceWeight()
    {
        $currentPlace = rwPlaces::find($this->placeId);

        return $currentPlace->pl_place_weight;
    }

    static function calcPlaceWeight($placeId = null)
    {

        if ($placeId == NULL) $placeId = $this->placeId;
        $currentPlace = rwPlaces::find($this->placeId);

        if ($currentPlace) {

            $sumWeight = 0;

            if ($currentPlace->pl_shelf > 0)
                $sumWeight += $currentPlace->pl_shelf;

            if ($currentPlace->pl_rack > 0)
                $sumWeight += $currentPlace->pl_rack * 10;

            if ($currentPlace->pl_row > 0)
                $sumWeight += $currentPlace->pl_row * 1000;

            if ($currentPlace->pl_section > 0)
                $sumWeight += $currentPlace->pl_section * 10000;

        }

    }
}