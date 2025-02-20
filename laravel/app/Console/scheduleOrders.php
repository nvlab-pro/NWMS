<?php

namespace App\Console;

use App\Models\rwOrder;
use App\Models\rwOrderOffer;
use App\WhCore\WhCore;

class scheduleOrders
{
    public static function reserveOrders($orderId = 0)
    {
        $start = microtime(true);

        $arOffers = [];
        $arOrders = [];
        $arWhCore = [];

        if ($orderId == 0)
            $dbOrdersList = rwOrder::whereIn('o_status_id', [20, 30])->get();
        else
            $dbOrdersList = rwOrder::whereIn('o_status_id', [20, 30])->where('o_id', $orderId)->get();

        foreach ($dbOrdersList as $dbOrder) {
            $orderId = $dbOrder->o_id;

            if (!isset($arWhCore[$dbOrder->o_wh_id])) $arWhCore[$dbOrder->o_wh_id] = new WhCore($dbOrder->o_wh_id);

            $dbOrderOffers = rwOrderOffer::where('oo_order_id', $orderId)->get();

            foreach ($dbOrderOffers as $dbOrderOffer) {

                if (!isset($arOffers[$dbOrderOffer->oo_offer_id])) {

                    $arOffers[$dbOrderOffer->oo_offer_id] = 1;

                    $reservOrders = $arWhCore[$dbOrder->o_wh_id]->reservOffers($dbOrderOffer->oo_offer_id);

                    $arOrders += $reservOrders;

                }

            }

            // Если статус "В резерв" то добавляем на проверку все-равно
            if ($dbOrder->o_status_id == 20) {
                $reservOrders[$dbOrder->o_id] = 1;
                $arOrders += $reservOrders;
            }

        }

        // Проверяем измененные заказы
        foreach ($arOrders as $orderId => $value) {

            $dbCurrentOrder = rwOrder::where('o_id', $orderId)->first();

            if (isset($dbCurrentOrder->o_id) && ($dbCurrentOrder->o_status_id == 20 || $dbCurrentOrder->o_status_id == 30)) {

                if ($arWhCore[$dbCurrentOrder->o_wh_id]->readyToReserve($dbCurrentOrder->o_id)) {

                    rwOrder::where('o_id', $orderId)->update([
                        'o_status_id' => 40,
                    ]);

                } else {

                    rwOrder::where('o_id', $orderId)->update([
                        'o_status_id' => 30,
                    ]);

                }

            }

        }

        $end = microtime(true);
        $executionTime = $end - $start;

        echo "Execution time: {$executionTime} seconds\n\n";
    }

}