<?php

namespace App\Orchid\Services;

use App\Models\rwAcceptance;
use App\Models\rwAcceptanceOffer;
use App\WhCore\WhCore;

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

    public function getAcceptanceList($acceptId)
    {
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
                'ao_barcode' => $barcode,
                'ao_expected' => $docOffer->ao_expected,
                'ao_accepted' => $acceptCount,
                'ao_placed' => $placed,
                'ao_price' => $acceptPrice,

            ];

        }

        $collection = collect($arDocOfferList)->map(function ($item) {
            return new rwAcceptanceOffer($item);
        });

        return $collection;

    }

    public function getAcceptanceOffer($acceptId, $offerId)
    {

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

    public function addItemCount($offerId, $count, $currentTime = 0)
    {
        $this->currentWarehouse->addItemCount($offerId, $count, $currentTime);

        return true;
    }

    public function getWhOfferId($docId, $offerId, $docType)
    {
        return $this->currentWarehouse->getWhOfferId($docId, $offerId, $docType);

    }
}
