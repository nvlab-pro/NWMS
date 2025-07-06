<?php

namespace App\Jobs;

use App\Models\rwAcceptance;
use App\Models\rwBillingTransactions;
use App\Models\rwInvoce;
use App\Models\rwOrder;
use App\Models\rwWarehouse;
use App\Models\WhcWarehouse;
use App\WhCore\WhCore;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CalculateBillingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $startDate;

    public function tags()
    {
        return ['billing'];
    }

    public function handle(): void
    {
        Log::info('[CalculateBilling] started at ' . now());

        // Глубина просчета (дней)
        $days = 90;
        $this->startDate = date('Y-m-d', strtotime("-{$days} days"));

        // Получаем список складов

        $resWarehouses = rwWarehouse::where('wh_billing_id', '>', 0)
            ->where('wh_status', 1)
            ->with(['getBilling', 'getParent.getCompany'])
            ->get();

        foreach ($resWarehouses as $warehouse) {
            $currentBillingId = $warehouse->wh_billing_id;

            $typeDate = $warehouse->getBilling->bs_date_type; // Какую берем дату при тарификации (текущую или дату документа)
            $jsonRates = $warehouse->getBilling->bs_rates; // Сами тарифы
            $vat = optional(optional($warehouse->getParent)->getCompany)->co_vat_proc ?? 0; // Получаем ставку НДС

            $customerCompanyId = $warehouse->wh_company_id;
            $executorCompanyId = $warehouse->getParent->wh_company_id;

            $arRates = json_decode($jsonRates, true);

            foreach ($arRates as $service => $rate) {
                if ($service == 'accepting') $this->calcAccepting($warehouse->wh_id, $rate, $typeDate, $currentBillingId, $vat, $customerCompanyId, $executorCompanyId);
                if ($service == 'picking') $this->calcPicking($warehouse->wh_id, $rate, $typeDate, $currentBillingId, $vat, $customerCompanyId, $executorCompanyId);
                if ($service == 'packing') $this->calcPacking($warehouse->wh_id, $rate, $typeDate, $currentBillingId, $vat, $customerCompanyId, $executorCompanyId);
//                if ($service == 'storage_items') $this->calcAccepting($rate);
            }

            $sumCost = 0;
            $sumReceived = 0;

            // Считаем сумму транзакций по складу
            $sumCost = rwBillingTransactions::where('bt_wh_id', $warehouse->wh_id)->sum('bt_total_sum');
            $sumReceived = rwInvoce::where('in_wh_id', $warehouse->wh_id)->where('in_status', 1)->sum('in_total_sum');

            $warehouse->wh_billing_cost = $sumCost;
            $warehouse->wh_billing_received = $sumReceived;
            $warehouse->wh_billing_sum = $sumReceived - $sumCost;
            $warehouse->save();

        }

        Log::info('[CalculateBilling] ended at ' . now());
    }

    // ********************************************************
    // *** Расчет тарифов для приходных накладных
    // ********************************************************
    public function calcAccepting($whId, $rate, $typeDate, $billingId, $vat, $customerCompanyId, $executorCompanyId): void
    {

        $resAcceptance = rwAcceptance::where('acc_wh_id', $whId)
            ->where('acc_status', 4)
            ->where('acc_date', '>=', $this->startDate)
            ->with('offers')
            ->get();

        $transType = 1; // accepting
        $code = $rate['code'];
        $templateStr = $rate['template'];
        $currentRate = $rate['rates'];

        foreach ($resAcceptance as $acceptance) {

            $isTrans = rwBillingTransactions::where('bt_service_id', $transType)
                ->where('bt_doc_id', $acceptance->acc_id)
                ->first();

            if (!$isTrans) {

                // Получаем дату транзакции
                $transDate = $acceptance->acc_date;
                if ($typeDate == 1) $transDate = date('Y-m-d H:i:s');

                $sumDoc = 0;
                $countOffers = 0;

                $core = new WhCore($whId);
                $offersList = $core->getDocumentOffers($acceptance->acc_id, 1)->get();

                foreach ($offersList as $whOffer) {

                    $tmpValue = 0;
                    $tmpWeight = 0;
                    $tmpSum = 0;

                    $offer = $whOffer->getOffer;

                    if ($offer) {

                        // Считаем объемный вес
                        if (isset($offer->of_dimension_x) && isset($offer->of_dimension_y) && isset($offer->of_dimension_z)
                            && $offer->of_dimension_x > 0 && $offer->of_dimension_y > 0 && $offer->of_dimension_z > 0) {
                            $tmpValue = $offer->of_dimension_x * $offer->of_dimension_y * $offer->of_dimension_z;
                        }
                        // Считаем вес
                        if (isset($offer->of_weight) && $offer->of_weight > 0) {
                            $tmpWeight = $offer->of_weight;
                        }

                        $tmpPrice = 0;
                        $num = 0;
                        $rate = 0;

                        // Получаем текущий тариф
                        foreach ($currentRate as $rateName => $rateTariff) {

                            if ($num == 0) {
                                $tmpPrice = $rateTariff['price'];
                                $rate = $rateName;
                            }

                            if ($tmpValue > $rateTariff['volume_to'] && $tmpPrice < $rateTariff['price']) {
                                $tmpPrice = $rateTariff['price'];
                                $rate = $rateName;
                            }
                            if ($tmpWeight > $rateTariff['weight_to'] && $tmpPrice < $rateTariff['price']) {
                                $tmpPrice = $rateTariff['price'];
                                $rate = $rateName;
                            }

                            $num++;

                        }

                        // Получить количество принятого товара
                        $sumDoc += $tmpPrice * $whOffer->whci_count;
                        $countOffers += $whOffer->whci_count;

                    }
                }

                // Создаем транзакцию
                $newTrans = new rwBillingTransactions();
                $newTrans->bt_date = $transDate;
                $newTrans->bt_service_id = $transType;
                $newTrans->bt_shop_id = $acceptance->acc_shop_id;
                $newTrans->bt_wh_id = $acceptance->acc_wh_id;
                $newTrans->bt_billing_id = $billingId;
                $newTrans->bt_customer_company_id = $customerCompanyId;
                $newTrans->bt_executor_company_id = $executorCompanyId;

                $newTrans->bt_doc_id = $acceptance->acc_id;
                $newTrans->bt_entity_count = $countOffers;

                $newTrans->bt_sum = $sumDoc;

                // считаем налоги
                if ($vat > 0) {
                    $newTrans->bt_tax = round($sumDoc * ($vat / 100), 2);
                    $newTrans->bt_total_sum = $sumDoc + round($sumDoc * ($vat / 100), 2);
                } else {
                    $newTrans->bt_tax = 0;
                    $newTrans->bt_total_sum = $sumDoc;
                }

                $newTrans->bt_act_id = 0;

                // Готовим строку
                $tmpStr = str_replace('{doc_id}', $acceptance->acc_id, $templateStr);
                $tmpStr = str_replace('{count_offers}', $countOffers, $tmpStr);
                $tmpStr = str_replace('{rate}', $rate, $tmpStr);
                $tmpStr = str_replace('{sum}', $newTrans->bt_total_sum, $tmpStr);

                $newTrans->bt_desc = $tmpStr;
                $newTrans->save();

            }
        }
    }

    // ********************************************************
    // *** Расчет тарифов для побора товара для заказа
    // ********************************************************
    public function calcPicking($whId, $rate, $typeDate, $billingId, $vat, $customerCompanyId, $executorCompanyId): void
    {

        $resOrder = rwOrder::where('o_wh_id', $whId)
            ->whereIn('o_status_id', [60, 70, 80, 90, 100, 110, 120, 130, 140, 150, 160, 170, 180])
            ->where('o_date', '>=', $this->startDate)
            ->with('offers')
            ->get();

        $transType = 3; // picking
        $code = $rate['code'];
        $templateStr = $rate['template'];
        $currentRate = $rate['rates'];

        foreach ($resOrder as $order) {

            $isTrans = rwBillingTransactions::where('bt_service_id', $transType)
                ->where('bt_doc_id', $order->o_id)
                ->first();

            if (!$isTrans) {

                // Получаем дату транзакции
                $transDate = $order->o_date;
                if ($typeDate == 1) $transDate = date('Y-m-d H:i:s');

                $sumDoc = 0;
                $countOffers = 0;

                $core = new WhCore($whId);
                $offersList = $core->getDocumentOffers($order->o_id, 2)->get();

                foreach ($offersList as $whOffer) {

                    $tmpValue = 0;
                    $tmpWeight = 0;
                    $tmpSum = 0;

                    $offer = $whOffer->getOffer;

                    if ($offer) {

                        // Считаем объемный вес
                        if (isset($offer->of_dimension_x) && isset($offer->of_dimension_y) && isset($offer->of_dimension_z)
                            && $offer->of_dimension_x > 0 && $offer->of_dimension_y > 0 && $offer->of_dimension_z > 0) {
                            $tmpValue = $offer->of_dimension_x * $offer->of_dimension_y * $offer->of_dimension_z;
                        }
                        // Считаем вес
                        if (isset($offer->of_weight) && $offer->of_weight > 0) {
                            $tmpWeight = $offer->of_weight;
                        }

                        $tmpPrice = 0;
                        $num = 0;
                        $rate = 0;

                        // Получаем текущий тариф
                        foreach ($currentRate as $rateName => $rateTariff) {

                            if ($num == 0) {
                                $tmpPrice = $rateTariff['price'];
                                $rate = $rateName;
                            }

                            if ($tmpValue > $rateTariff['volume_to'] && $tmpPrice < $rateTariff['price']) {
                                $tmpPrice = $rateTariff['price'];
                                $rate = $rateName;
                            }
                            if ($tmpWeight > $rateTariff['weight_to'] && $tmpPrice < $rateTariff['price']) {
                                $tmpPrice = $rateTariff['price'];
                                $rate = $rateName;
                            }

                            $num++;

                        }

                        // Получить количество принятого товара
                        $sumDoc += $tmpPrice * $whOffer->whci_count;
                        $countOffers += $whOffer->whci_count;

                    }
                }

                // Создаем транзакцию
                $newTrans = new rwBillingTransactions();
                $newTrans->bt_date = $transDate;
                $newTrans->bt_service_id = $transType;
                $newTrans->bt_shop_id = $order->o_shop_id;
                $newTrans->bt_wh_id = $order->o_wh_id;
                $newTrans->bt_billing_id = $billingId;
                $newTrans->bt_customer_company_id = $customerCompanyId;
                $newTrans->bt_executor_company_id = $executorCompanyId;
                $newTrans->bt_doc_id = $order->o_id;
                $newTrans->bt_entity_count = $countOffers;
                $newTrans->bt_sum = $sumDoc;

                // считаем налоги
                if ($vat > 0) {
                    $newTrans->bt_tax = round($sumDoc * ($vat / 100), 2);
                    $newTrans->bt_total_sum = $sumDoc + round($sumDoc * ($vat / 100), 2);
                } else {
                    $newTrans->bt_tax = 0;
                    $newTrans->bt_total_sum = $sumDoc;
                }

                $newTrans->bt_act_id = 0;

                // Готовим строку
                $tmpStr = str_replace('{doc_id}', $order->o_id, $templateStr);
                $tmpStr = str_replace('{count_offers}', $countOffers, $tmpStr);
                $tmpStr = str_replace('{rate}', $rate, $tmpStr);
                $tmpStr = str_replace('{sum}', $newTrans->bt_total_sum, $tmpStr);

                $newTrans->bt_desc = $tmpStr;
                $newTrans->save();

            }
        }
    }


    // ********************************************************
    // *** Расчет тарифов для упаковки товара для заказа
    // ********************************************************
    public function calcPacking($whId, $rate, $typeDate, $billingId, $vat, $customerCompanyId, $executorCompanyId): void
    {

        $resOrder = rwOrder::where('o_wh_id', $whId)
            ->whereIn('o_status_id', [100, 110, 120, 130, 140, 150, 160, 170, 180])
            ->where('o_date', '>=', $this->startDate)
            ->with('offers')
            ->get();

        $transType = 4; // packing
        $code = $rate['code'];
        $templateStr = $rate['template'];
        $currentRate = $rate['rates'];

        foreach ($resOrder as $order) {

            $isTrans = rwBillingTransactions::where('bt_service_id', $transType)
                ->where('bt_doc_id', $order->o_id)
                ->first();

            if (!$isTrans) {

                // Получаем дату транзакции
                $transDate = $order->o_date;
                if ($typeDate == 1) $transDate = date('Y-m-d H:i:s');

                $sumDoc = 0;
                $countOffers = 0;
                $tmpOrderValue = $tmpOrderWeight = 0;

                $core = new WhCore($whId);
                $offersList = $core->getDocumentOffers($order->o_id, 2)->get();

                // Считаем объемный вес заказа
                if ($order->o_dimension_x > 0 && $order->o_dimension_y > 0 && $order->o_dimension_z > 0) {
                    $tmpOrderValue = $order->of_dimension_x * $order->of_dimension_y * $order->of_dimension_z;
                }
                // Считаем вес заказа
                if ($order->o_weight > 0) {
                    $tmpOrderWeight = $order->o_weight;
                }

                // Если у заказа нет габаритов, считаем суммарный объем товара заказа
                if ($tmpOrderValue == 0 && $tmpOrderWeight == 0) {

                    foreach ($offersList as $whOffer) {

                        $offer = $whOffer->getOffer;

                        if ($offer) {

                            // Считаем объемный вес
                            if (isset($offer->of_dimension_x) && isset($offer->of_dimension_y) && isset($offer->of_dimension_z)
                                && $offer->of_dimension_x > 0 && $offer->of_dimension_y > 0 && $offer->of_dimension_z > 0) {
                                $tmpOrderValue += $offer->of_dimension_x * $offer->of_dimension_y * $offer->of_dimension_z;
                            }
                            // Считаем вес
                            if (isset($offer->of_weight) && $offer->of_weight > 0) {
                                $tmpOrderWeight += $offer->of_weight;
                            }
                        }
                    }
                }

                $num = 0;
                $weightStr = '';
                $rate = 0;

                // Получаем текущий тариф
                foreach ($currentRate as $rateName => $rateTariff) {

                    if ($num == 0) {
                        $sumDoc = $rateTariff['price'];
                        $weightStr = $tmpOrderValue . ' kg.';
                        $rate = $rateName;
                    }

                    if ($tmpOrderValue > $rateTariff['volume_to'] && $sumDoc < $rateTariff['price']) {
                        $sumDoc = $rateTariff['price'];
                        $weightStr = $tmpOrderValue . ' sm3.';
                        $rate = $rateName;
                    }
                    if ($tmpOrderWeight > $rateTariff['weight_to'] && $sumDoc < $rateTariff['price']) {
                        $sumDoc = $rateTariff['price'];
                        $weightStr = $tmpOrderWeight . ' kg.';
                        $rate = $rateName;
                    }

                    $num++;

                }

                // Готовим строку
                $tmpStr = str_replace('{doc_id}', $order->o_id, $templateStr);
                $tmpStr = str_replace('{sum}', $sumDoc, $tmpStr);
                $tmpStr = str_replace('{doc_weight}', $weightStr, $tmpStr);
                $tmpStr = str_replace('{rate}', $rate, $tmpStr);

                // Создаем транзакцию
                $newTrans = new rwBillingTransactions();
                $newTrans->bt_date = $transDate;
                $newTrans->bt_service_id = $transType;
                $newTrans->bt_shop_id = $order->o_shop_id;
                $newTrans->bt_wh_id = $order->o_wh_id;
                $newTrans->bt_billing_id = $billingId;
                $newTrans->bt_customer_company_id = $customerCompanyId;
                $newTrans->bt_executor_company_id = $executorCompanyId;
                $newTrans->bt_doc_id = $order->o_id;
                $newTrans->bt_entity_count = $countOffers;
                $newTrans->bt_sum = $sumDoc;

                // считаем налоги
                if ($vat > 0) {
                    $newTrans->bt_tax = round($sumDoc * ($vat / 100), 2);
                    $newTrans->bt_total_sum = $sumDoc + round($sumDoc * ($vat / 100), 2);
                } else {
                    $newTrans->bt_tax = 0;
                    $newTrans->bt_total_sum = $sumDoc;
                }

                $newTrans->bt_act_id = 0;

                // Готовим строку
                $tmpStr = str_replace('{doc_id}', $order->o_id, $templateStr);
                $tmpStr = str_replace('{count_offers}', $countOffers, $tmpStr);
                $tmpStr = str_replace('{rate}', $rate, $tmpStr);
                $tmpStr = str_replace('{sum}', $newTrans->bt_total_sum, $tmpStr);

                $newTrans->bt_desc = $tmpStr;
                $newTrans->save();

            }
        }
    }
}