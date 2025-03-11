<?php

namespace App\Orchid\Services;

class BarcodeService
{

    public static function convertBarcode($barcode)
    {
        if (preg_match('/^\d{3};/', $barcode)) {

            return str_replace(';', '*', $barcode);

        } else {

            return $barcode;

        }


    }

}