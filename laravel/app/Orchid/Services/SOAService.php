<?php

namespace App\Orchid\Services;

use App\Models\rwOrder;
use App\Models\rwSettingsSoa;
use App\Models\rwWarehouse;

class SOAService
{

    public static function calcRedyOrders($soaId, $orderType = 1)
    {

        $dbSOASetting = rwSettingsSoa::where('ssoa_id', $soaId)->first();

        if (isset($dbSOASetting->ssoa_id)) {

            $dbOrders = rwOrder::where('o_domain_id', $dbSOASetting->ssoa_domain_id)
                ->where('o_type_id', $orderType)
                ->where('o_wh_id', $dbSOASetting->ssoa_wh_id);

            if (isset($dbSOASetting->ssoa_date_from) && isset($dbSOASetting->ssoa_date_to)) {
                $dbOrders = $dbOrders->where('o_date_send', '>=', $dbSOASetting->ssoa_date_from)
                    ->where('o_date_send', '<=', $dbSOASetting->ssoa_date_to);
            }
//            foreach ($dbOrders->get() as $order) {
//                dump($order->o_id . ' - ' . $order->o_status_id);
//            }

            if ($dbSOASetting->ssoa_offers_count_from > 0 && $dbSOASetting->ssoa_offers_count_to > 0) {
                $dbOrders = $dbOrders->where('o_count', '>=', $dbSOASetting->ssoa_offers_count_from)
                    ->where('o_count', '<=', $dbSOASetting->ssoa_offers_count_to);
            }
            if ($dbSOASetting->ssoa_order_from > 0 && $dbSOASetting->ssoa_order_to > 0) {
                $dbOrders = $dbOrders->where('o_id', '>=', $dbSOASetting->ssoa_order_from)
                    ->where('o_id', '<=', $dbSOASetting->ssoa_order_to);
            }

            $readyOrdersCount = $dbOrders->clone()->whereIn('o_status_id', [40, 50])->count();
            $processOrdersCount = $dbOrders->clone()->whereIn('o_status_id', [50, 60])->count();
            $sendOrdersCount = $dbOrders->clone()->where('o_status_id',  '>',60)->count();

            rwSettingsSoa::where('ssoa_id', $soaId)->update([
                'ssoa_count_ready'      => $readyOrdersCount,
                'ssoa_count_process'    => $processOrdersCount,
                'ssoa_count_send'       => $sendOrdersCount,
            ]);

        }

    }

    public static function calcAllSettings($domainId, $parentWhId)
    {
        $arWhList = rwWarehouse::where('wh_parent_id', $parentWhId)
            ->pluck('wh_id')
            ->toArray();

        $dbSettingsList = rwSettingsSoa::where('ssoa_domain_id', $domainId)
            ->where('ssoa_status_id', 1)
            ->whereIn('ssoa_wh_id', $arWhList)
            ->get();

        foreach ($dbSettingsList as $dbSetting) {

            self::calcRedyOrders($dbSetting->ssoa_id);

        }

    }

    public static function getFirstOrder($soaId, $userId, $orderType = 1)
    {
        $dbSOASetting = rwSettingsSoa::where('ssoa_id', $soaId)->first();

        if (isset($dbSOASetting->ssoa_id)) {

            $dbOrders = rwOrder::where('o_domain_id', $dbSOASetting->ssoa_domain_id)
                ->where('o_type_id', $orderType)
                ->where('o_wh_id', $dbSOASetting->ssoa_wh_id);

            if (isset($dbSOASetting->ssoa_date_from) && isset($dbSOASetting->ssoa_date_to)) {
                $dbOrders = $dbOrders->where('o_date_send', '>=', $dbSOASetting->ssoa_date_from)
                    ->where('o_date_send', '<=', $dbSOASetting->ssoa_date_to);
            }
            if ($dbSOASetting->ssoa_offers_count_from > 0 && $dbSOASetting->ssoa_offers_count_to > 0) {
                $dbOrders = $dbOrders->where('o_count', '>=', $dbSOASetting->ssoa_offers_count_from)
                    ->where('o_count', '<=', $dbSOASetting->ssoa_offers_count_to);
            }
            if ($dbSOASetting->ssoa_order_from > 0 && $dbSOASetting->ssoa_order_to > 0) {
                $dbOrders = $dbOrders->where('o_id', '>=', $dbSOASetting->ssoa_order_from)
                    ->where('o_id', '<=', $dbSOASetting->ssoa_order_to);
            }

            $currentOrder = $dbOrders->clone()->where('o_status_id', 50)
                ->where('o_operation_user_id', $userId)
                ->first();

            if ($currentOrder) {
                return $currentOrder;
            } else {
                $currentOrder = $dbOrders->where('o_status_id', 40)->first();
                return $currentOrder;
            }

        }

        return false;

    }
}