<?php

namespace App\Orchid\Services;

use App\Models\rwAcceptance;
use App\Models\rwAcceptanceOffer;
use App\Models\rwPlace;
use App\WhActionLog\WhActionLog;
use App\WhCore\WhCore;
use Illuminate\Support\Facades\Auth;

class DocumentService
{
    protected $docId, $currentWarehouse, $shopId, $whId, $docStatus;

    public function __construct($docId)
    {
        $this->docId = $docId;

        $dbCurrentAcceptance = rwAcceptance::where('acc_id', $this->docId)->first();

        $this->shopId = $dbCurrentAcceptance->acc_shop_id;
        $this->whId = $dbCurrentAcceptance->acc_wh_id;
        $this->docStatus = $dbCurrentAcceptance->acc_status;

        $this->currentWarehouse = new WhCore($this->whId);
    }

    public function __invoke(): void
    {
        //
    }

    // ************************************************************
    // *** Пересчитываем остатки в приемки для вывода в списке
    public function updateRest($docType)
    {

        $sumExpected = rwAcceptanceOffer::where('ao_acceptance_id', $this->docId)
            ->sum('ao_expected');

        $sumAccepted = $this->currentWarehouse->getAcceptedCount($this->docId, $docType);
        $sumPlaced = $this->currentWarehouse->getPlacedCount($this->docId, $docType);

        rwAcceptance::where('acc_id', $this->docId)
            ->update([
                'acc_count_expected' => $sumExpected,
                'acc_count_accepted' => $sumAccepted,
                'acc_count_placed' => $sumPlaced,
            ]);

    }


    // ************************************************************
    // *** Кладем товар на полку (сохраняем его место хранения)
    public function saveOfferToPlace($whOfferId, $placeId, $scanCount, $currentTime)
    {
        /*
                1. Находим нужную запись на складе
                2. Проверяем равно ли количество привязанного товара этой записи
                3. Если равно, привязываем весь кусок
                4. Если не равно, то создаем копию записи с нужным количество товара (откусываем кусок).
                5. Прописываем у старого куска ID места хранения, новый остается непривязанным
        */
        $currentUser = Auth::user();

        $dbOffer = $this->currentWarehouse->getItem($whOfferId);

        if ($dbOffer->whci_count >= $scanCount && $dbOffer->whci_cash != $currentTime) {


            if ($dbOffer->whci_count == $scanCount) {
                // Если количество товара совпадают, привязываем все

                $this->currentWarehouse->setPlace($whOfferId, $placeId);

            } else {
                // Если товара меньше, откусываем кусок и его привязываем

                $newCount = $dbOffer->whci_count - $scanCount;

                // Обновляем количество у старого куска и привязываем его
                $this->currentWarehouse->setPlaceCoutItem($whOfferId, $placeId, $scanCount);

                // Создаем новый элемент на складе
                $dbCurrentAccept = rwAcceptanceOffer::create([
                    'ao_acceptance_id' => $dbOffer->whci_doc_id,
                    'ao_offer_id' => $dbOffer->whci_offer_id,
                    'ao_expected' => 0,
                ]);

                $this->currentWarehouse->addItems(
                    $dbOffer->whci_doc_id,
                    $dbOffer->whci_date,
                    $dbOffer->whci_offer_id,
                    $newCount,
                    $dbCurrentAccept->ao_id,
                    $dbOffer->whci_doc_type,
                    $dbOffer->whci_barcode,
                    $dbOffer->whci_expiration_date,
                    $dbOffer->whci_batch,
                    $dbOffer->whci_price,
                    $currentTime,
                );

            }

            // Сохраняем данные в лог
//            WhActionLog::saveActionLog($this->whId, 2, date('Y-m-d H:i:s', time()), $currentUser->id, $whOfferId, $scanCount);

            // Сохраняем данные в статистике
            WarehouseUserActionService::logAction([
                'ua_user_id'     => $currentUser->id, // ID текущего кладовщика
                'ua_lat_id'      => 2,            // ID типа действия (например, 2 — "привязка товара")
                'ua_domain_id'   => $currentUser->domain_id,    // ID компании / окружения
                'ua_wh_id'       => $this->whId, // ID склада
                'ua_shop_id'     => $this->shopId,      // ID магазина, если применимо
                'ua_place_id'    => $placeId,     // ID ячейки склада
                'ua_entity_type' => 'offer',      // Тип сущности (например, offer, order)
                'ua_entity_id'   => $dbOffer->whci_offer_id,     // ID выбранного товара
                'ua_quantity'    => $scanCount,          // Количество товара
            ]);

            $this->updateRest(1);

            // Пересчитываем остатки
            $this->currentWarehouse->calcRestOffer($dbOffer->whci_offer_id);

            return true;

        } else {
            return false;
        }

    }

    // ************************************************************
    // *** Получаем коллекцию всех товаров в накладной
    public function getAcceptanceList()
    {
        $acceptId = $this->docId;

        $dbAcceptOffersList = rwAcceptanceOffer::where('ao_acceptance_id', $acceptId)->filters()->orderBy('ao_id', 'DESC');
        $dbAcceptOffersList = $dbAcceptOffersList->with('getOffers');

        // Получаю список сохраненных в накладной товаров
        $acceptOffersList = $this->currentWarehouse->getDocumentOffers($acceptId, 1);

        $arDocOfferList = [];

        foreach ($acceptOffersList->get() as $currentItem) {

            $acceptCount = $currentItem->whci_count;
            $acceptPrice = $currentItem->whci_price;
            $batch = $currentItem->whci_batch;
            $expirationDate = $currentItem->whci_expiration_date;
            $productionDate = $currentItem->whci_production_date;
            $barcode = $currentItem->whci_barcode;
            $placed = $currentItem->whci_place_id;
            $status = $this->docStatus;

            $docOffer = $dbAcceptOffersList->clone()
                ->where('ao_id', $currentItem->whci_doc_offer_id)
                ->first();

            // 2. Преобразование формата даты expDate
            if ($expirationDate) {
                // Проверяем, в каком формате пришла дата
                if (strpos($expirationDate, '-') !== false) {
                    // Если дата в формате DD.MM.YYYY, преобразуем в YYYY-MM-DD
                    $expirationDate = \DateTime::createFromFormat('Y-m-d', $expirationDate)->format('d.m.Y');
                }
            }

            // 2. Преобразование формата даты expDate
            if ($productionDate) {
                // Проверяем, в каком формате пришла дата
                if (strpos($productionDate, '-') !== false) {
                    // Если дата в формате DD.MM.YYYY, преобразуем в YYYY-MM-DD
                    $productionDate = \DateTime::createFromFormat('Y-m-d', $productionDate)->format('d.m.Y');
                }
            }

            $dbPlace = rwPlace::where('pl_id', $placed)->first();

            $placeStr = 0;
            if (isset($dbPlace->pl_id)) {
                if ($dbPlace->pl_room) $placeStr = $dbPlace->pl_room;
                if ($dbPlace->pl_floor) $placeStr .= '-' . $dbPlace->pl_floor;
                if ($dbPlace->pl_section) $placeStr .= '-' . $dbPlace->pl_section;
                if ($dbPlace->pl_row) $placeStr .= '-' . $dbPlace->pl_row;
                if ($dbPlace->pl_rack) $placeStr .= '-' . $dbPlace->pl_rack;
                if ($dbPlace->pl_shelf) $placeStr .= '-' . $dbPlace->pl_shelf;
            }

            $arDocOfferList[] = [
                'ao_id' => $docOffer->ao_id,
                'ao_offer_id' => $docOffer->getOffers->of_id,
                'ao_wh_offer_id' => $currentItem->whci_id,
                'oa_status' => $status,
                'ao_img' => $docOffer->getOffers->of_img,
                'ao_name' => $docOffer->getOffers->of_name,
                'ao_article' => $docOffer->getOffers->of_article,
                'ao_dimension' => $docOffer->getOffers->of_dimension_x . 'x' .
                    $docOffer->getOffers->of_dimension_y . 'x' .
                    $docOffer->getOffers->of_dimension_z . ' / ' .
                    $docOffer->getOffers->of_weight . 'гр.',
                'ao_batch' => $batch,
                'ao_expiration_date' => $expirationDate,
                'ao_production_date' => $productionDate,
                'ao_barcode' => $barcode,
                'ao_expected' => $docOffer->ao_expected,
                'ao_accepted' => $acceptCount,
                'ao_placed' => $placeStr,
                'ao_price' => $acceptPrice,

            ];

        }

        $collection = collect($arDocOfferList)->map(function ($item) {
            return new rwAcceptanceOffer($item);
        });

        return $collection;

    }

    // ************************************************************
    // *** Получаем массив данных конкретного товара
    public function getAcceptanceOffer($offerId)
    {

        $acceptId = $this->docId;

        // Получаю список сохраненных в накладной товаров
        $acceptOffersList = $this->currentWarehouse->getDocumentOffer($acceptId, $offerId, 1);

        $arDocOfferList = [];

        $currentItem = $acceptOffersList->first();

        if (isset($currentItem->whci_id)) {

            $acceptCount = $currentItem->whci_count;
            $acceptPrice = $currentItem->whci_price;
            $batch = $currentItem->whci_batch;
            $expirationDate = $currentItem->whci_expiration_date;
            $barcode = $currentItem->whci_barcode;
            $placed = $currentItem->whci_place_id;
            $status = $this->docStatus;

            $docOffer = rwAcceptanceOffer::where('ao_acceptance_id', $acceptId)->with('getOffers')->where('ao_id', $currentItem->whci_doc_offer_id)
                ->first();

            // 2. Преобразование формата даты expDate
            if ($expirationDate) {
                // Проверяем, в каком формате пришла дата
                if (strpos($expirationDate, '-') !== false) {
                    // Если дата в формате DD.MM.YYYY, преобразуем в YYYY-MM-DD
                    $expirationDate = \DateTime::createFromFormat('Y-m-d', $expirationDate)->format('d.m.Y');
                }
            }

            $arDocOfferList = [
                'ao_id' => $docOffer->ao_id,
                'ao_offer_id' => $docOffer->getOffers->of_id,
                'ao_wh_offer_id' => $currentItem->whci_id,
                'oa_status' => $status,
                'ao_img' => $docOffer->getOffers->of_img,
                'ao_name' => $docOffer->getOffers->of_name,
                'ao_article' => $docOffer->getOffers->of_article,
                'ao_dimension' => $docOffer->getOffers->of_dimension_x . 'x' .
                    $docOffer->getOffers->of_dimension_y . 'x' .
                    $docOffer->getOffers->of_dimension_z . ' / ' .
                    $docOffer->getOffers->of_weight . 'гр.',
                'ao_batch' => $batch,
                'ao_expiration_date' => $expirationDate,
                'ao_barcode' => $barcode,
                'ao_expected' => $docOffer->ao_expected,
                'ao_accepted' => $acceptCount,
                'ao_placed' => $placed,
                'ao_price' => $acceptPrice,

            ];

            return $arDocOfferList;

        } else {

            return [];

        }

    }

    // ************************************************************
    // *** Создаем новый элемент на складе
    public function addItemCount($offerId, $docDate, $count, $currentTime = 0, $exeptDate = NULL, $batch = NULL)
    {
        if ($this->currentWarehouse->addItemCount($offerId, $count, $currentTime, $exeptDate, $batch)) {

            return true;

        } else {

            if (strlen($exeptDate) == 6) {
                $exeptDate = '20' . substr($exeptDate, 4, 2) . '-' . substr($exeptDate, 2, 2) . '-' . substr($exeptDate, 0, 2);
            }

            $dbItem = $this->currentWarehouse->getItem($offerId);

            $dbCurrentAccept = rwAcceptanceOffer::create([
                'ao_acceptance_id' => $dbItem->whci_doc_id,
                'ao_offer_id' => $dbItem->whci_offer_id,
                'ao_expected' => 0,
            ]);

            $this->currentWarehouse->saveOffers(
                $dbItem->whci_doc_id,
                $docDate,
                $dbItem->whci_doc_type,
                $dbCurrentAccept->ao_id,
                $dbItem->whci_offer_id,
                $dbItem->whci_status,
                $count,
                $dbItem->whci_barcode,
                $dbItem->whci_price,
                $exeptDate,
                $batch
            );

            return true;

        }

    }

    // ********************************************************************
    // *** Получаем ID элмента на складе по номеру накладной и ID оффера
    public function getWhOfferId($offerId, $docType)
    {
        return $this->currentWarehouse->getWhOfferId($this->docId, $offerId, $docType);

    }

}
