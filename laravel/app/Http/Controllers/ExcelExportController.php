<?php

namespace App\Http\Controllers;

use alhimik1986\PhpExcelTemplator\params\ExcelParam;
use alhimik1986\PhpExcelTemplator\PhpExcelTemplator;
use alhimik1986\PhpExcelTemplator\setters\CellSetterArrayValue;
use alhimik1986\PhpExcelTemplator\setters\CellSetterArrayValueSpecial;
use App\Models\rwCompany;
use App\Models\rwOrder;
use App\Models\rwOrderContact;
use App\Models\rwOrderDs;
use App\Models\rwOrderOffer;
use App\Models\rwWarehouse;
use App\WhCore\WhCore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExcelExportController extends Controller
{
    protected $orderId, $tmpId;

    public function __construct(int $orderId, int $tmpId)
    {

        $this->tmpId = $tmpId;
        $this->orderId = $orderId;

    }

    public function generate(string $templatePath, string $outputPath): void
    {
        // … заполняете шаблон …
        PhpExcelTemplator::saveToFile($templatePath, $outputPath, $this->dummyData());
    }

    private function dummyData(): array
    {

        $resOrder = rwOrder::find($this->orderId);

        $clientsTags = $resMainWh = $customerTags = [];

        // *****************************************
        // *** Получаем данные продавца

        $resSeller = rwWarehouse::where('wh_id', $resOrder->o_wh_id)->first(); // Склад отправителя (селлера)

        if ($resSeller->wh_parent_id > 0)
            $resMainWh = rwWarehouse::where('wh_id', $resSeller->wh_parent_id)->first(); // Склад обрабатывающий продавца

        $sellerDocNum = $resSeller->wh_doc_num;
        $sellerDocDate = $resSeller->wh_doc_date;

        $resOrderSeller = rwCompany::where('co_id', $resSeller->wh_company_id)->with('getCity')->first();
        if ($resOrderSeller) {
            $customerTags = [
                '{doc_id}' => $sellerDocNum,
                '{dog_date}' => $sellerDocDate,
                '{customer_name}' => $resOrderSeller->co_legal_name,
                '{customer_inn}' => $resOrderSeller->co_vat_number,
                '{customer_phone}' => $resOrderSeller->co_phone,
                '{customer_email}' => $resOrderSeller->co_email,
                '{customer_address}' => $resOrderSeller->co_address,
                '{customer_bank_account}' => $resOrderSeller->co_bank_account,
                '{customer_bank_ks}' => $resOrderSeller->co_bank_ks,
                '{customer_bank_name}' => $resOrderSeller->co_bank_name,
                '{customer_bank_bic}' => $resOrderSeller->co_swift_bic,
                '{customer_contact_person}' => $resOrderSeller->co_contact_person,
            ];
        }

        // *****************************************
        // *** Получаем данные склада

        $senderTags = [];

        if ($resSeller->wh_parent_id > 0) {

            $resOrderSender = rwCompany::where('co_id', $resMainWh->wh_company_id)->with('getCity')->first();
            if ($resOrderSender) {
                $senderTags = [
                    '{executor_name}' => $resOrderSender->co_legal_name,
                    '{executor_inn}' => $resOrderSender->co_vat_number,
                    '{executor_phone}' => $resOrderSender->co_phone,
                    '{executor_email}' => $resOrderSender->co_email,
                    '{executor_address}' => $resOrderSender->co_address,
                    '{executor_bank_account}' => $resOrderSender->co_bank_account,
                    '{executor_bank_ks}' => $resOrderSender->co_bank_ks,
                    '{executor_bank_name}' => $resOrderSender->co_bank_name,
                    '{executor_bank_bic}' => $resOrderSender->co_swift_bic,
                    '{executor_contact_person}' => $resOrderSender->co_contact_person,
                ];
            }
        }

        // *****************************************
        // *** Получаем данные о службе доставки

        $dsTags = [];

        $resDS = rwOrderDs::where('ods_id', $this->orderId)->with('getDsName')->first();

        if ($resDS) {
            $dsTags = [
                '{ds_name}' => $resDS->getDsName->ds_name,
            ];
        }


        // *****************************************
        // *** Получаем данные о покупателе

        if ($resOrder->o_customer_type == 1) {

            // Получаем данные о покупателе (юр.лицо)
            $resOrderCompany = rwCompany::where('co_id', $resOrder->o_company_id)->with('getCity')->first();
            if ($resOrderCompany) {
                $clientsTags = ['{buyer_name}' => $resOrderCompany->co_legal_name,
                    '{buyer_inn}' => $resOrderCompany->co_vat_number,
                    '{buyer_phone}' => $resOrderCompany->co_phone,
                    '{buyer_email}' => $resOrderCompany->co_email,
                    '{buyer_city}' => $resOrderCompany->getCity->lcit_name,
                    '{buyer_address}' => $resOrderCompany->co_address,];
            }
        } else {

            // Получаем данные о покупателе (физ.лицо)
            $resOrderClient = rwOrderContact::where('oc_order_id', $this->orderId)->with('getCity')->first();
            if ($resOrderClient) {
                $clientsTags = [
                    '{buyer_name}' => $resOrderClient->oc_first_name . ' ' . $resOrderClient->oc_last_name,
                    '{buyer_inn}' => '',
                    '{buyer_phone}' => $resOrderClient->oc_phone ?? '',
                    '{buyer_email}' => $resOrderClient->oc_email ?? '',
                    '{buyer_city}' => $resOrderClient->getCity->lcit_name ?? '',
                    '{buyer_address}' => $resOrderClient->oc_full_address ?? '',
                ];
            }
        }

        // *****************************************
        // *** Получаем данные о товаре

        // Выводим данные о товаре без сроков годности
        $resOrderOffer = rwOrderOffer::where('oo_order_id', $this->orderId)
            ->with('getOffer')
            ->get();

        $tags = [
            '[offer_num]' => [],
            '[offer_id]' => [],
            '[offer_name]' => [],
            '[offer_article]' => [],
            '[offer_sku]' => [],
            '[offer_qty]' => [],
            '[offer_oc_price]' => [],
            '[offer_price]' => [],
            '[offer_dimension_x]' => [],
            '[offer_dimension_y]' => [],
            '[offer_dimension_z]' => [],
            '[offer_weight]' => [],
            '[offer_sum]' => [],
        ];

        $num = 0;
        $totalQty = 0;
        $totalSum = 0;

        foreach ($resOrderOffer as $orderOffer) {
            $num++;
            $offer = $orderOffer->getOffer;

            $tags['[offer_id]'][] = $offer->of_id;
            $tags['[offer_num]'][] = $num;
            $tags['[offer_name]'][] = $offer->of_name;
            $tags['[offer_article]'][] = $offer->of_article ?? '';
            $tags['[offer_qty]'][] = $orderOffer->oo_qty;
            $tags['[offer_sku]'][] = $offer->of_sku ?? '';
            $tags['[offer_oc_price]'][] = $orderOffer->oo_oc_price ?? 0;
            $tags['[offer_price]'][] = $orderOffer->oo_price;
            $tags['[offer_dimension_x]'][] = $offer->of_dimension_x ?? '';
            $tags['[offer_dimension_y]'][] = $offer->of_dimension_y ?? '';
            $tags['[offer_dimension_z]'][] = $offer->of_dimension_z ?? '';
            $tags['[offer_weight]'][] = $offer->of_weight ?? '';
            $tags['[offer_sum]'][] = round($orderOffer->oo_qty * $orderOffer->oo_oc_price, 2);

            $totalQty += $orderOffer->oo_qty;

            if ($orderOffer->oo_oc_price > 0)
                $totalSum += $orderOffer->oo_oc_price;

        }

        $num = 0;

        // Выводим данные о товаре со сроками годности
        $currentWh = new WhCore($resOrder->o_wh_id);
        $resWhOrderOffer = $currentWh->getDocumentOffers($this->orderId, 2)->get();
        foreach ($resWhOrderOffer as $whOrderOffer) {
            $num++;

            $tags['[offer_part_num]'][] = $num;
            $tags['[offer_part_id]'][] = $whOrderOffer->whci_id;
            $tags['[offer_part_name]'][] = $whOrderOffer->getOffer->of_name;
            $tags['[offer_part_article]'][] = $whOrderOffer->getOffer->of_article;
            $tags['[offer_part_sku]'][] = $whOrderOffer->getOffer->of_sku;
            $tags['[offer_part_qty]'][] = $whOrderOffer->whci_count;
            $tags['[offer_part_oc_price]'][] = $whOrderOffer->whci_price;
            $tags['[offer_part_price]'][] = $whOrderOffer->whci_price;
            $tags['[offer_part_production_date]'][] = $whOrderOffer->whci_production_date;
            $tags['[offer_part_expiration_date]'][] = $whOrderOffer->whci_expiration_date;
            $tags['[offer_part_batch]'][] = $whOrderOffer->whci_batch;
            $tags['[offer_part_dimension_x]'][] = $whOrderOffer->getOffer->of_dimension_x;
            $tags['[offer_part_dimension_y]'][] = $whOrderOffer->getOffer->of_dimension_y;
            $tags['[offer_part_dimension_z]'][] = $whOrderOffer->getOffer->of_dimension_z;
            $tags['[offer_part_weight]'][] = $whOrderOffer->getOffer->of_weight;
            $tags['[offer_part_sum]'][] = round($whOrderOffer->whci_count * $whOrderOffer->whci_price, 2);

        }

        return array_merge([
            '{order_id}' => $this->orderId,
            '{order_ext_id}' => $resOrder->o_ext_id,
            '{order_date}' => $resOrder->o_date,
            '{order_date_send}' => $resOrder->o_date_send,

            '{total_qty}' => $totalQty,
            '{total_sum}' => round($totalSum, 2),

            '[offer_id]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_id]']),
            '[offer_num]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_num]']),
            '[offer_name]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_name]']),
            '[offer_article]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_article]']),
            '[offer_qty]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_qty]']),
            '[offer_sku]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_sku]']),
            '[offer_oc_price]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_oc_price]']),
            '[offer_price]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_price]']),
            '[offer_dimension_x]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_dimension_x]']),
            '[offer_dimension_y]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_dimension_y]']),
            '[offer_dimension_z]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_dimension_z]']),
            '[offer_weight]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_weight]']),
            '[offer_sum]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_sum]']),

            '[offer_part_num]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_part_num]']),
            '[offer_part_id]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_part_id]']),
            '[offer_part_name]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_part_name]']),
            '[offer_part_article]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_part_article]']),
            '[offer_part_sku]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_part_sku]']),
            '[offer_part_qty]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_part_qty]']),
            '[offer_part_oc_price]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_part_oc_price]']),
            '[offer_part_price]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_part_price]']),
            '[offer_part_production_date]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_part_production_date]']),
            '[offer_part_expiration_date]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_part_expiration_date]']),
            '[offer_part_batch]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_part_batch]']),
            '[offer_part_dimension_x]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_part_dimension_x]']),
            '[offer_part_dimension_y]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_part_dimension_y]']),
            '[offer_part_dimension_z]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_part_dimension_z]']),
            '[offer_part_weight]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_part_weight]']),
            '[offer_part_sum]' => new ExcelParam(CellSetterArrayValueSpecial::class, $tags['[offer_part_sum]']),
        ],
            $customerTags,
            $senderTags,
            $dsTags,
            $clientsTags
        );
    }
}