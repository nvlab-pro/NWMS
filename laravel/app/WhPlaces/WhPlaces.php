<?php

namespace App\WhPlaces;

use App\Models\rwPlace;

class WhPlaces
{
    private $barcode, $placeId, $prefix;

    public function __construct($barcode, $whId)
    {
        $this->barcode = $barcode;
        $this->placeId = 0;

        $arBarcode = explode("*", $barcode);

        if ($arBarcode[0] >= 102 && $arBarcode[0] <= 110) {

            $controlSum = $arBarcode[0] + $arBarcode[1];

            if ($controlSum == $arBarcode[2]) {

                $tmpPlaceId = $arBarcode[1];
                $this->prefix = $arBarcode[0];

                $dbPlace = rwPlace::where('pl_wh_id', $whId)
                    ->where('pl_id', $tmpPlaceId)
                    ->where('pl_type', $this->prefix)
                    ->first();

                if (isset($dbPlace) && $dbPlace->pl_id > 0) {
                    $this->placeId = $dbPlace->pl_id;
                }

            }

        }

//        return $this->placeId;
        return false;
    }

    public function getPlaceId()
    {
        return $this->placeId;
    }

    public function getType()
    {
        return $this->prefix;
    }

    public function getPlaceWeight()
    {
        $currentPlace = rwPlace::find($this->placeId);

        return $currentPlace->pl_place_weight;
    }

    static function calcPlaceWeight($placeId = null)
    {

        if ($placeId == NULL) $placeId = $this->placeId;
        $currentPlace = rwPlace::find($this->placeId);

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