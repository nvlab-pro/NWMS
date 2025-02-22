<?php

namespace App\Orchid\Services;

use App\Models\rwAcceptance;
use App\Models\rwOrder;
use App\Models\rwOrderAssembly;
use App\Models\rwOrderOffer;
use App\WhCore\WhCore;

class OrderService
{
    private $docId, $dbOrder, $currentWarehouse;

    public function __construct($docId)
    {

        $this->docId = $docId;

        $this->dbOrder = rwOrder::where('o_id', $this->docId)->first();
        $whId = $this->dbOrder->o_wh_id;

        $this->currentWarehouse = new WhCore($whId);

    }

    public function recalcOrderRest()
    {
        $dbDocOffers = rwOrderOffer::where('oo_order_id', $this->docId)->get();

        $count = $sum = 0;

        if (isset($dbDocOffers)) {

            foreach ($dbDocOffers as $dbDocOffer) {

                $count += $dbDocOffer->oo_qty;
                $sum += $dbDocOffer->oo_oc_price;

            }
        }

        $dbOrder = rwOrder::find($this->docId);
        $dbOrder->o_count = $count;
        $dbOrder->o_sum = $sum;
        $dbOrder->save();

    }

    public function resaveOrderList()
    {

        $docStatus = $this->dbOrder->o_status_id;
        $docDate = $this->dbOrder->o_date;

        $dbDocOffers = rwOrderOffer::where('oo_order_id', $this->docId)->get();

        foreach ($dbDocOffers as $currentDocOffer) {

            $status = 0;
            $count = $currentDocOffer->oo_qty;
            if ($docStatus <= 10) $count = 0; // Если статус документа "новый", товар не резервируем

            // Сохраняем заказ в базу если статус позволяет
            if ($docStatus < 40) {

                $this->currentWarehouse->saveOffers(
                    $this->docId,
                    $docDate,
                    2,                                  // Приемка (таблица rw_lib_type_doc)
                    $currentDocOffer->oo_id,                    // ID офера в документе
                    $currentDocOffer->oo_offer_id,              // оригинальный ID товара
                    $status,
                    $count,
                    NULL,
                    $currentDocOffer->oo_price,
                    $currentDocOffer->oo_expiration_date,
                    $currentDocOffer->oo_batch,
                    time()
                );

            }

        }

        $this->recalcOrderRest();

    }

    // Резервируем товары конкретного заказа в процессе сборки
    public function resaveOrderRests()
    {
        $docStatus = $this->dbOrder->o_status_id;
        $docDate = $this->dbOrder->o_date;
        $status = 1;

        $dbDocOffers = rwOrderOffer::where('oo_order_id', $this->docId)->get();

        foreach ($dbDocOffers as $currentDocOffer) {

            $currentQty = $currentDocOffer->oo_qty;

            $dbOrderAssembly = rwOrderAssembly::where('oa_order_id', $this->docId)
                ->where('oa_offer_id', $currentDocOffer->oo_offer_id)
                ->where('oa_qty', '>', 0)
                ->get();

            foreach ($dbOrderAssembly as $currentOffer) {

                $this->currentWarehouse->saveOffers(
                    $this->docId,
                    $currentOffer->oa_data,
                    2,                                  // Приемка (таблица rw_lib_type_doc)
                    $currentDocOffer->oo_id,                    // ID офера в документе
                    $currentDocOffer->oo_offer_id,              // оригинальный ID товара
                    $status,
                    $currentOffer->oa_qty,
                    $currentOffer->oa_barcode,
                    $currentDocOffer->oo_price,
                    $currentDocOffer->oo_expiration_date,
                    $currentDocOffer->oo_batch,
                    time(),
                    $currentOffer->oa_place_id
                );

                $currentQty -= $currentOffer->oa_qty;

            }

            if ($currentQty > 0) {

                $this->currentWarehouse->saveOffers(
                    $this->docId,
                    $docDate,
                    2,                                  // Приемка (таблица rw_lib_type_doc)
                    $currentDocOffer->oo_id,                    // ID офера в документе
                    $currentDocOffer->oo_offer_id,              // оригинальный ID товара
                    0,
                    $currentQty,
                    NULL,
                    $currentDocOffer->oo_price,
                    $currentDocOffer->oo_expiration_date,
                    $currentDocOffer->oo_batch,
                    time()
                );

            } else {

                $this->currentWarehouse->deleteItemFromDocument(
                    $currentDocOffer->oo_id,
                    $this->docId,
                    2,
                    NULL
                );

            }
        }
    }
}