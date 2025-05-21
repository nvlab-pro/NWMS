<?php

namespace App\WhPlaces;

use App\Models\rwPlace;
use App\Models\rwWarehouse;
use App\Models\WhcRest;
use Illuminate\Support\Facades\Auth;

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

        $currentUser = Auth::user();

        // Конвретируем место хранения в формате C120432 в стандартный формат
        if ($currentUser && preg_match('/^(\p{L})(\d{2})(\d{2})(\d)(\d)$/u', $barcode, $matches)) {

            $place = rwPlace::where('pl_domain_id', $currentUser->domain_id)
                ->where('pl_wh_id', $whId)
                ->where('pl_type', 102)
                ->where('pl_room', 'ПЭК')
                ->where('pl_floor', strtoupper($matches[1]))
                ->where('pl_row', (int)$matches[2])
                ->where('pl_section', (int)$matches[3])
                ->where('pl_cell', (int)$matches[4])
                ->where('pl_shelf', (int)$matches[5])
                ->first();

            if ($place) {
                $this->placeId = $place->pl_id;
            } else {
                $place = rwPlace::create([
                    'pl_domain_id'      => $currentUser->domain_id,
                    'pl_wh_id'          => $whId,
                    'pl_type'           => 102,
                    'pl_room'           => 'ПЭК',
                    'pl_floor'          => strtoupper($matches[1]),
                    'pl_row'            => (int)$matches[2],
                    'pl_section'        => (int)$matches[3],
                    'pl_cell'           => (int)$matches[4],
                    'pl_shelf'          => (int)$matches[5],
                    'pl_place_weight'   => 0,
                ]);

                $place_weight = self::calcPlaceWeight($place->pl_id);
                $place->update(['pl_place_weight' => $place_weight]);
                $this->placeId = $place->pl_id;
            }
        }

        // Конвертируем место хранения в формате 120432 в стандартный формат
        if ($currentUser && preg_match('/^(\d{2})(\d{2})(\d{2})$/', $barcode, $matches)) {

            $row     = (int) $matches[1];
            $section = (int) $matches[2];
            $shelf   = (int) $matches[3];

            $place = rwPlace::where('pl_domain_id', $currentUser->domain_id)
                ->where('pl_wh_id', $whId)
                ->where('pl_type', 102)
                ->where('pl_room', 'ПЭК')
                ->where('pl_row', $row)
                ->where('pl_section', $section)
                ->where('pl_shelf', $shelf)
                ->first();

            if ($place) {
                $this->placeId = $place->pl_id;
            } else {
                $place = rwPlace::create([
                    'pl_domain_id'      => $currentUser->domain_id,
                    'pl_wh_id'          => $whId,
                    'pl_type'           => 102,
                    'pl_room'           => 'ПЭК',
                    'pl_row'            => $row,
                    'pl_section'        => $section,
                    'pl_shelf'          => $shelf,
                    'pl_place_weight'   => 0,
                ]);

                $place_weight = self::calcPlaceWeight($place->pl_id);
                $place->update(['pl_place_weight' => $place_weight]);
                $this->placeId = $place->pl_id;
            }
        }

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

        $sumWeight = 0;

        if ($placeId == NULL) $placeId = $this->placeId;
        $currentPlace = rwPlace::find($placeId);

        if ($currentPlace) {

            if ($currentPlace->pl_shelf > 0)
                $sumWeight += $currentPlace->pl_shelf;

            if ($currentPlace->pl_rack > 0)
                $sumWeight += $currentPlace->pl_rack * 10;

            if ($currentPlace->pl_row > 0)
                $sumWeight += $currentPlace->pl_row * 1000;

            if ($currentPlace->pl_section > 0)
                $sumWeight += $currentPlace->pl_section * 10000;

       }

        return $sumWeight;

    }

    static function getPlacesList($offerId, $whId = 0) {

        $arRests = [];
        $arWhRests = [];

        if ($whId == 0) {
            $dbRests = WhcRest::where('whcr_offer_id', $offerId)
                ->with('getPlace')
                ->orderBy('whcr_wh_id', 'ASC')
                ->orderBy('whcr_count', 'DESC')
                ->get();
        } else {
            $dbRests = WhcRest::where('whcr_offer_id', $offerId)
                ->where('whcr_wh_id', $whId)
                ->with('getPlace')
                ->orderBy('whcr_count', 'DESC')
                ->get();
        }

        foreach ($dbRests as $rest) {

            $dbWh = rwWarehouse::where('wh_id', $rest->whcr_wh_id)->first();

            $placeStr = '-';
            if (isset($rest->getPlace->pl_id)) {
                if ($rest->getPlace->pl_room) $placeStr = $rest->getPlace->pl_room;
                if ($rest->getPlace->pl_floor) $placeStr .= '-' . $rest->getPlace->pl_floor;
                if ($rest->getPlace->pl_section) $placeStr .= '-' . $rest->getPlace->pl_section;
                if ($rest->getPlace->pl_row) $placeStr .= '-' . $rest->getPlace->pl_row;
                if ($rest->getPlace->pl_rack) $placeStr .= '-' . $rest->getPlace->pl_rack;
                if ($rest->getPlace->pl_shelf) $placeStr .= '-' . $rest->getPlace->pl_shelf;
            }

            $arRests[] = [
                'whName' => $dbWh->wh_name,
                'whId' => $rest->whcr_wh_id,
                'placeId' => $rest->whcr_place_id,
                'placeName' => $placeStr,
                'count' => $rest->whcr_count,
                'production_date' => $rest->whcr_production_date,
                'expiration_date' => $rest->whcr_expiration_date,
                'batch' => $rest->whcr_batch,
            ];

            if (isset($arWhRests[$rest->whcr_wh_id])) {
                $arWhRests[$rest->whcr_wh_id] = [
                    'whName' => $dbWh->wh_name,
                    'whId' => $rest->whcr_wh_id,
                    'count' => $arWhRests[$rest->whcr_wh_id]['count'] + $rest->whcr_count,
                ];
            } else {
                $arWhRests[$rest->whcr_wh_id] = [
                    'whName' => $dbWh->wh_name,
                    'whId' => $rest->whcr_wh_id,
                    'count' => $rest->whcr_count,
                ];
            }
        }

        return [
            'arRests'       => $arRests,
            'arWhRests'     => $arWhRests,
        ];

    }
}