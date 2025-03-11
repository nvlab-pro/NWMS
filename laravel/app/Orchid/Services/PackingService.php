<?php

namespace App\Orchid\Services;

use App\Models\rwOrder;
use App\Models\rwSettingsProcPacking;
use App\Models\rwSettingsSoa;
use App\Models\rwWarehouse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PackingService
{

    // Проверяем не упаковывается ли уже заказ на данном месте
    public static function getCurrentOrderIdFromTable($tableId)
    {
        $currentUser = Auth::user();

        // Находим заказы удовлетворяющие параметрам очереди
        $dbCurrantOrder = rwOrder::where('o_domain_id', $currentUser->domain_id)
            ->where('o_status_id', 90)
            ->where('o_order_place', $tableId)
            ->first();

        if ($dbCurrantOrder) {
            return $dbCurrantOrder->o_id;
        }

        return false;

    }

    // Получаем список заказов, которые готовы для упаковки
    public static function checkPackingTable($tableId)
    {
        $currentUser = Auth::user();

        // Находим заказы удовлетворяющие параметрам очереди
        $currentOrder = rwOrder::where('o_domain_id', $currentUser->domain_id)
            ->where('o_status_id', 90)
            ->where('o_order_place', $tableId)
            ->with('getPlace')
            ->first();

        if ($currentOrder) {
            // Если на столе уже идет упаковка

            $orderUser = User::where('id', $currentOrder->o_operation_user_id)->first();

            $arOrders = [
                'id' => $orderUser->id,
                'name' => $orderUser->name,
            ];

            return $arOrders;

        }

        return false;
    }

    // Получаем список заказов, которые готовы для упаковки
    public static function getOrdersRedyToPacking($queueId, $tableId = 0)
    {
        $currentUser = Auth::user();
        $arOrders = [];

        // Получаем настройки очереди
        $currentQueue = rwSettingsProcPacking::where('spp_id', $queueId)
            ->first();

        if ($currentQueue) {

            // Получаем данные очереди
            $queueWhId = $currentQueue->spp_wh_id;
            $queueStartPlaceType = $currentQueue->spp_start_place_type;
            $queueRackFrom = $currentQueue->spp_place_rack_from;
            $queueRackTo = $currentQueue->spp_place_rack_to;
            $queuePackType = $currentQueue->spp_packing_type;

            // Находим заказы удовлетворяющие параметрам очереди
            $dbOrdersList = rwOrder::where('o_domain_id', $currentUser->domain_id)
                ->whereIn('o_status_id', [60, 80])
                ->where('o_wh_id', $queueWhId)
                ->with('getPlace')
                ->get();

            foreach ($dbOrdersList as $currentOrder) {
                // Если способ подбора совпадает с типом очереди
                if ($currentOrder->getPlace->pl_type == $queueStartPlaceType) {

                    if ($currentOrder->getPlace->pl_type == 105) {
                        if ($currentOrder->o_order_place == $tableId) {
                            $arOrders[$currentOrder->o_id] = 0;
                        }
                    } else {

                        $arOrders[$currentOrder->o_id] = 0;

                    }
                }
                // Если выбран способ подбора прямо с полок
                if ($queueStartPlaceType == 102) {

                    $arOrders[$currentOrder->o_id] = 0;

                }

                // Если на столе уже идет упаковка
                if ($currentOrder->o_status_id == 90 && $tableId == $currentOrder->o_order_place) {

                    $orderUser = User::where('id', $currentOrder->o_operation_user_id)->first();

                    $arOrders[$currentOrder->o_id] = [
                        'id' => $orderUser->id,
                        'name' => $orderUser->name,
                    ];

                }
            }

        }

        return $arOrders;

    }

}