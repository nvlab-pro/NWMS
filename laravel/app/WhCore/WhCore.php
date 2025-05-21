<?php

namespace App\WhCore;

use App\Models\rwAcceptanceOffer;
use App\Models\rwLibTypeDoc;
use App\Models\rwOrder;
use App\Models\rwOrderAssembly;
use App\Models\rwOrderOffer;
use App\Models\rwStatsRest;
use App\Models\rwUserAction;
use App\Models\WhcRest;
use App\Models\whcWhItem;
use App\Orchid\Services\WarehouseUserActionService;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Models\WhcWarehouse;

class WhCore
{
    private $warehouseId, $itemTableName;
    private $version = 2;

    /*
     *  whci_status:
     *  1 - ожидаем (новый)
     *  2 - в наличии
     *  3 - размещен
     *  4 - зарезервирован
     *  6 - отгружен
     *
     * whci_id - ID элемента
     * whci_offer_id -  ID оффера
     * whci_doc_offer_id -  ID оффера в документе
     *
     */

    public function __construct($warehouseId)
    {

        if ($warehouseId > 0) {

            $this->warehouseId = $warehouseId;

//            $dbWarehouse = WhcWarehouse::find($warehouseId);

            // Если таблица складов не создана, создаем её
            if (!Schema::hasTable('whc_warehouses')) {
                Schema::create('whc_warehouses', function (Blueprint $table) {
                    $table->id('whc_id');
                    $table->integer('whc_ver');
                    $table->timestamps();
                    $table->softDeletes();
                });
            }

            $this->itemTableName = 'whc_wh' . $warehouseId . '_items';

            // Если таблица с элементами не создана, создаем её
            if (!Schema::hasTable($this->itemTableName)) {

                // Создание нового склада

                Schema::create($this->itemTableName, function (Blueprint $table) {
                    $table->id('whci_id');
                    $table->dateTime('whci_date')->index();
                    $table->tinyInteger('whci_status')->unsigned()->index()->comment('0 - не учитывать, 1 - учитывать в расчете остатков'); // 0 - не учитывать, 1 - учитывать в расчете остатков
                    $table->bigInteger('whci_offer_id')->unsigned()->index();
                    $table->bigInteger('whci_place_id')->unsigned()->nullable()->index();
                    $table->bigInteger('whci_doc_id')->unsigned()->index();
                    $table->bigInteger('whci_doc_offer_id')->unsigned()->index();
                    $table->tinyInteger('whci_doc_type')->unsigned()->index();
                    $table->double('whci_count')->unsigned();
                    $table->tinyInteger('whci_sign');
                    $table->date('whci_production_date')->nullable()->index();
                    $table->date('whci_expiration_date')->nullable()->index();
                    $table->string('whci_batch', 15)->nullable()->index();
                    $table->string('whci_barcode', 30)->nullable()->index();
                    $table->double('whci_price')->nullable();
                    $table->integer('whci_cash')->nullable()->unsigned()->default(0)->index();
                    $table->timestamps();
                    $table->softDeletes();
                });

                WhcWarehouse::create([
                    'whc_id' => $warehouseId,
                    'whc_ver' => $this->version,
                ]);

            } else {

                // Таблица существует. Проверяем ее версию

                $whcWarehouse = WhcWarehouse::find($this->warehouseId);
                $version = $whcWarehouse->whc_ver;

                // Если версия 1, нужно добавить новое поле whci_production_date
                if ($version == 1) {

                    $whcWarehouse->whc_ver = 2;
                    $whcWarehouse->save();

                    Schema::table($this->itemTableName, function (Blueprint $table) {
                        $table->date('whci_production_date')->nullable()->index()->after('whci_sign');
                    });

                }

            }

            return true;

        } else {

            return false;

        }


    }

    // *******************************************************
    // *** Работа с остатками
    // *******************************************************

    // *******************************************************
    // *** Считаем остатки по конкретному товару с учетом мест хранения
    public function calcRestOffer($offerId, $saveStats = 0)
    {
        $rest = 0;

        $dbPlaces = DB::table('whc_wh' . $this->warehouseId . '_items')
            ->select(
                'whci_place_id',
                DB::raw('MAX(whci_production_date) as whci_production_date'),
                DB::raw('MAX(whci_expiration_date) as whci_expiration_date'),
                DB::raw('MAX(whci_batch) as whci_batch')
            )
            ->where('whci_offer_id', $offerId)
            ->groupBy('whci_place_id', 'whci_production_date', 'whci_expiration_date', 'whci_batch')
            ->get();

        WhcRest::where('whcr_offer_id', $offerId)->update([
            'whcr_active' => 0,
            'whcr_updated' => 0,
        ]);

        foreach ($dbPlaces as $place) {

            $dbRest = DB::table('whc_wh' . $this->warehouseId . '_items')
                ->selectRaw('sum(whci_count * whci_sign) as total')
                ->where('whci_offer_id', $offerId)
                ->where('whci_place_id', $place->whci_place_id)
                ->where('whci_production_date', $place->whci_production_date)
                ->where('whci_expiration_date', $place->whci_expiration_date)
                ->where('whci_batch', $place->whci_batch)
                ->first();

            if (isset($dbRest->total)) {

                $currantRest = $dbRest->total;

                $existing = WhcRest::query()
                    ->where('whcr_offer_id', $offerId)
                    ->where('whcr_place_id', $place->whci_place_id)
                    ->where(function ($query) use ($place) {
                        $place->whci_production_date !== null
                            ? $query->where('whcr_production_date', $place->whci_production_date)
                            : $query->whereNull('whcr_production_date');

                        $place->whci_expiration_date !== null
                            ? $query->where('whcr_expiration_date', $place->whci_expiration_date)
                            : $query->whereNull('whcr_expiration_date');

                        $place->whci_batch !== null
                            ? $query->where('whcr_batch', $place->whci_batch)
                            : $query->whereNull('whcr_batch');
                    })
                    ->first();

                if ($existing) {
                    $existing->update([
                        'whcr_count' => $currantRest,
                        'whcr_active' => 1,
                        'whcr_updated' => 1,
                    ]);
                } else {

                    WhcRest::create([
                        'whcr_offer_id' => $offerId,
                        'whcr_place_id' => $place->whci_place_id,
                        'whcr_production_date' => $place->whci_production_date,
                        'whcr_expiration_date' => $place->whci_expiration_date,
                        'whcr_batch' => $place->whci_batch,
                        'whcr_wh_id' => $this->warehouseId,
                        'whcr_count' => $currantRest,
                        'whcr_active' => 1,
                        'whcr_updated' => 1,
                    ]);
                }

                $rest += $dbRest->total;

            }
        }

        WhcRest::where('whcr_offer_id', $offerId)
            ->where('whcr_updated', 0)
            ->delete();

        return $rest;
    }

    // ******************************************************
    // *** Получаем список товаров находящийся на месте
//    public function getRestOfOfferId($placeId, $offerId)
//    {
//
//        $rest = 0;
//
//        $rest = $this->calcRestOffer($offerId);
//
//        return $rest;
//
//    }

    public function getRestOfOfferId($offerId)
    {

        $rest = 0;

        $rest = $this->calcRestOffer($offerId);

        return $rest;

    }

    public function readyToReserve($orderId)
    {
        $dbItem = DB::table('whc_wh' . $this->warehouseId . '_items')
            ->where('whci_doc_id', $orderId)
            ->where('whci_doc_type', 2)
            ->where('whci_status', 0)
            ->first();

        if (isset($dbItem->whci_id))
            return false;

        return true;
    }

    // Является ли товар зарезервированным
    public function getReservedOffer($orderId, $offerId)
    {
        $dbItem = DB::table('whc_wh' . $this->warehouseId . '_items')
            ->where('whci_doc_id', $orderId)
            ->where('whci_offer_id', $offerId)
            ->where('whci_doc_type', 2)
            ->where('whci_status', 1)
            ->first();

        if (isset($dbItem->whci_id))
            return true;

        return false;
    }

    public function reservOffers($offerId)
    {
        $currentRest = $acceptanceCountSum = $ordersCountSum = 0;
        $arOrders = [];

        // Считаем сумму всех приемок
        $dbRest = DB::table('whc_wh' . $this->warehouseId . '_items')
            ->selectRaw('sum(whci_count) as total')
            ->where('whci_offer_id', $offerId)
            ->where('whci_doc_type', 1)
            ->first();

        if (isset($dbRest->total)) $acceptanceCountSum = $dbRest->total;

        // Считаю сумму всех заказов
        $dbRest = DB::table('whc_wh' . $this->warehouseId . '_items')
            ->selectRaw('sum(whci_count) as total')
            ->where('whci_offer_id', $offerId)
            ->where('whci_doc_type', 2)
            ->where('whci_status', 1)
            ->first();
        if (isset($dbRest->total)) $ordersCountSum = $dbRest->total;

        // Получаем текущий остаток товара
        $currentRest = $acceptanceCountSum - $ordersCountSum;

        // Если товар еще есть, распределяем на оставшиеся заказы
        if ($currentRest > 0) {

            $dbItems = DB::table('whc_wh' . $this->warehouseId . '_items')
                ->where('whci_offer_id', $offerId)
                ->where('whci_doc_type', 2)
                ->where('whci_status', 0)
                ->get();

            foreach ($dbItems as $dbItem) {

                $currentRest -= $dbItem->whci_count;

                // Если остатка хватает то резервируем заказ
                if ($currentRest >= 0) {

                    $dbCurrentOrder = rwOrder::where('o_id', $dbItem->whci_doc_id)
                        ->first();

                    if (isset($dbCurrentOrder->o_id) && $dbCurrentOrder->o_status_id < 50) {

                        DB::table('whc_wh' . $this->warehouseId . '_items')
                            ->where('whci_id', $dbItem->whci_id)
                            ->update([
                                'whci_status' => 1,
                            ]);

                        $arOrders[$dbItem->whci_doc_id] = 1;

                    }

                } else {

                    $currentRest += $dbItem->whci_count;

                }

            }
        }

        return $arOrders;

    }

    // *******************************************************
    // *** Получаем список всех товаров из документа
    public function getDocumentOffers($docId, $docType)
    {
        return DB::table('whc_wh' . $this->warehouseId . '_items')
            ->where('whci_doc_id', $docId)
            ->where('whci_doc_type', $docType)
            ->orderBy('whci_doc_offer_id', 'DESC');
    }

    // *******************************************************
    // *** Получаем конкретный товар зная ID его элемента
    public function getDocumentOffer($docId, $whOfferId, $docType)
    {
        return DB::table('whc_wh' . $this->warehouseId . '_items')
            ->where('whci_doc_id', $docId)
            ->where('whci_id', $whOfferId)
            ->where('whci_doc_type', $docType);
    }

    // *******************************************************
    // *** Получаем список всех товаров из документа
    public function getDocumentOfferTurnover($offerId)
    {

        $items = whcWhItem::fromWarehouse($this->warehouseId)
            ->where('whci_offer_id', $offerId)
            ->orderBy('whci_date', 'ASC')
            ->with('getStatus')
            ->get();

        return $items;

//        return DB::table('whc_wh' . $this->warehouseId . '_items')
//            ->where('whci_offer_id', $offerId)
//            ->orderBy('whci_date', 'ASC')
//            ->get();
    }


    public function getWhOfferId($docId, $offerId, $docType)
    {
        $offerId = DB::table('whc_wh' . $this->warehouseId . '_items')
            ->where('whci_doc_id', $docId)
            ->where('whci_offer_id', $offerId)
            ->where('whci_doc_type', $docType)
            ->first('whci_id');

        if (isset($offerId->whci_id))
            return $offerId->whci_id;
        else
            return 0;
    }

    public function getPlacedCount($docId, $docType)
    {
        return DB::table('whc_wh' . $this->warehouseId . '_items')
            ->where('whci_doc_id', $docId)
            ->where('whci_doc_type', $docType)
            ->where('whci_place_id', '>', 0)
            ->sum('whci_count');
    }

    public function setPlaceCoutItem($itemId, $placeId, $count = 0)
    {
        if ($count == 0) {

            $offerId = DB::table('whc_wh' . $this->warehouseId . '_items')
                ->where('whci_id', $itemId)
                ->update([
                    'whci_place_id' => $placeId,
                ]);

        } else {

            $offerId = DB::table('whc_wh' . $this->warehouseId . '_items')
                ->where('whci_id', $itemId)
                ->update([
                    'whci_place_id' => $placeId,
                    'whci_count' => $count,
                ]);

        }

    }

    public function getAcceptedCount($docId, $docType)
    {
        return DB::table('whc_wh' . $this->warehouseId . '_items')
            ->where('whci_doc_id', $docId)
            ->where('whci_doc_type', $docType)
            ->sum('whci_count');
    }

    public function getItem($itemId)
    {
        $offerId = DB::table('whc_wh' . $this->warehouseId . '_items')
            ->where('whci_id', $itemId)
            ->first();

        if (isset($offerId->whci_id))
            return $offerId;
        else
            return 0;

    }

    // Удаляем элемент из склада
    public function deleteItem($whOfferId, $docType): string
    {
        return DB::table('whc_wh' . $this->warehouseId . '_items')
            ->where('whci_id', $whOfferId)
            ->where('whci_doc_type', $docType)
            ->delete();

    }

    // Удаляем элемент со склада, зная его документ
    public function deleteItemFromDocument($docOfferId, $docId, $docType, $placeId = NULL): string
    {
        return DB::table('whc_wh' . $this->warehouseId . '_items')
            ->where('whci_doc_offer_id', $docOfferId)
            ->where('whci_doc_id', $docId)
            ->where('whci_doc_type', $docType)
            ->where('whci_place_id', $placeId)
            ->delete();

    }

    // Сохраняем весь заказ из таблицы комплектации
    public function reservAssembledOrder($orderId)
    {
        $docType = 2;

        $dbOrder = rwOrder::where('o_id', $orderId)->first();

        DB::table('whc_wh' . $this->warehouseId . '_items')
            ->where('whci_doc_id', $orderId)
            ->where('whci_doc_type', $docType)
            ->update([
                'whci_cash' => 0,
            ]);

        $dbAssembledOffers = rwOrderAssembly::where('oa_order_id', $orderId)->get();

        foreach ($dbAssembledOffers as $dbAssembledOffer) {

            $price = 0;

            $dbOrderOffer = rwOrderOffer::where('oo_order_id', $orderId)
                ->where('oo_offer_id', $dbAssembledOffer->oa_offer_id)
                ->first();

            if (isset($dbOrderOffer->oo_oc_price) && $dbOrderOffer->oo_oc_price > 0) $price = $dbOrderOffer->oo_oc_price;
            if (isset($dbOrderOffer->oo_price) && $dbOrderOffer->oo_price > 0) $price = $dbOrderOffer->oo_price;

            $this->saveOffers(
                $orderId,
                $dbAssembledOffer->oa_data,
                $docType,
                $dbAssembledOffer->oa_id,
                $dbAssembledOffer->oa_offer_id,
                1,
                $dbAssembledOffer->oa_qty,
                $dbAssembledOffer->oa_barcode,
                $price,
                $expDate = NULL,
                $batch = NULL,
                $timeCash = 1,
                $placeId = $dbAssembledOffer->oa_place_id
            );

        }

        DB::table('whc_wh' . $this->warehouseId . '_items')
            ->where('whci_doc_id', $orderId)
            ->where('whci_doc_type', $docType)
            ->where('whci_cash', 0)
            ->delete();


    }

    public function addItemCount($offerId, $count, $currentTime = 0, $exeptDate = NULL, $batch = NULL, $prodDate = NULL)
    {
        if (strlen($prodDate) == 6) {
            $prodDate = '20' . substr($prodDate, 4, 2) . '-' . substr($prodDate, 2, 2) . '-' . substr($prodDate, 0, 2);
        } else {
            $prodDate = NULL;
        }
        if (strlen($exeptDate) == 6) {
            $exeptDate = '20' . substr($exeptDate, 4, 2) . '-' . substr($exeptDate, 2, 2) . '-' . substr($exeptDate, 0, 2);
        } else {
            $exeptDate = NULL;
        }
        if ($batch == '') $batch = NULL;

        $itemId = DB::table('whc_wh' . $this->warehouseId . '_items')
            ->where('whci_id', $offerId)
            ->where('whci_production_date', $prodDate)
            ->where('whci_expiration_date', $exeptDate)
            ->where('whci_batch', $batch)
            ->first();

        if (!isset($itemId->whci_id)) {
            $itemIdTmp = DB::table('whc_wh' . $this->warehouseId . '_items')
                ->where('whci_id', $offerId)
                ->first();

            if (isset($itemIdTmp->whci_id)) {
                $itemId = DB::table('whc_wh' . $this->warehouseId . '_items')
                    ->where('whci_offer_id', $itemIdTmp->whci_offer_id)
                    ->where('whci_production_date', $prodDate)
                    ->where('whci_expiration_date', $exeptDate)
                    ->where('whci_batch', $batch)
                    ->first();
            }
        }

        if (isset($itemId->whci_id)) {

            // Товар с таким  ID и датой есть

            $lastCount = $itemId->whci_count + $count;

            $time = $itemId->whci_cash;

            if ($time != $currentTime) {
                DB::table('whc_wh' . $this->warehouseId . '_items')
                    ->where('whci_id', $itemId->whci_id)
                    ->update([
                        'whci_count' => $lastCount,
                        'whci_cash' => $currentTime,
                    ]);
            } else {
                return 100; //  Если это перезагрузка страницы, возвращаем 100
            }

            return true;

        } else {

            return false;

        }

    }

    public function saveOffers($docId, $docDate, $docType, $docOfferId, $offerId, $status, $count, $barcode, $price, $expDate = NULL, $batch = NULL, $prodDate = NULL, $timeCash = 0, $placeId = NULL)
    {

        $validator = Validator::make([
            'docId' => $docId,
            'docDate' => $docDate,
            'docType' => $docType,
            'docOfferId' => $docOfferId,
            'offerId' => $offerId,
            'status' => $status,
            'count' => $count,
            'barcode' => $barcode,
            'price' => $price,
            'expDate' => $expDate,
            'prodDate' => $prodDate,
            'batch' => $batch,
            'timeCash' => $timeCash,
            'placeId' => $placeId,
        ], [
            'docId' => 'required|integer',
            'docDate' => 'nullable|date_format:Y-m-d H:i:s', // Допускаем оба формата
            'docType' => 'required|integer',
            'docOfferId' => 'required|integer',
            'offerId' => 'required|integer',
            'status' => 'required|integer',
            'count' => 'required|numeric',
            'barcode' => 'nullable|string',
            'price' => 'required|numeric',
            'expDate' => 'nullable|date_format:d.m.Y,Y-m-d', // Допускаем оба формата
            'prodDate' => 'nullable|date_format:d.m.Y,Y-m-d', // Допускаем оба формата
            'batch' => 'nullable|string',
            'timeCash' => 'required|numeric',
            'placeId' => 'nullable|numeric',
        ]);

        $currentUser = Auth::user();

        if ($validator->fails()) {
            // Обработка ошибок валидации, например, выброс исключения
            throw new \Exception('Validation Error: ' . $validator->errors()->first());
        }

        // 2. Преобразование формата даты expDate
        if ($expDate) {
            // Проверяем, в каком формате пришла дата
            if (strpos($expDate, '.') !== false) {
                // Если дата в формате DD.MM.YYYY, преобразуем в YYYY-MM-DD
                $expDate = \DateTime::createFromFormat('d.m.Y', $expDate)->format('Y-m-d');
            }
        }

        // 2. Преобразование формата даты expDate
        if ($prodDate) {
            // Проверяем, в каком формате пришла дата
            if (strpos($prodDate, '.') !== false) {
                // Если дата в формате DD.MM.YYYY, преобразуем в YYYY-MM-DD
                $prodDate = \DateTime::createFromFormat('d.m.Y', $prodDate)->format('Y-m-d');
            }
            if (is_numeric($prodDate)) {
                $prodDate = Carbon::createFromTimestamp($prodDate)->toDateString();
            }
        }

        $dbCurrentOffer = DB::table('whc_wh' . $this->warehouseId . '_items')
            ->where('whci_doc_id', $docId)
            ->where('whci_doc_type', $docType)
            ->where('whci_doc_offer_id', $docOfferId)
            ->where('whci_place_id', $placeId)
            ->first();

        if (isset($dbCurrentOffer->whci_id)) {

            // Сохраняем данные в базе
            DB::table('whc_wh' . $this->warehouseId . '_items')->where('whci_id', $dbCurrentOffer->whci_id)->update([
                'whci_date' => $docDate,
                'whci_count' => $count,
                'whci_production_date' => $prodDate,
                'whci_expiration_date' => $expDate,
                'whci_batch' => $batch,
                'whci_barcode' => $barcode,
                'whci_price' => $price,
                'whci_cash' => $timeCash,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        } else {

            $sign = rwLibTypeDoc::where('td_id', $docType)->first()->td_sign;

            // Сохраняем данные в базе
            DB::table('whc_wh' . $this->warehouseId . '_items')->insert([
                'whci_date' => $docDate,
                'whci_status' => $status,
                'whci_offer_id' => $offerId,
                'whci_place_id' => $placeId,
                'whci_doc_id' => $docId,
                'whci_doc_offer_id' => $docOfferId,
                'whci_doc_type' => $docType,
                'whci_count' => $count,
                'whci_sign' => $sign,
                'whci_production_date' => $prodDate,
                'whci_expiration_date' => $expDate,
                'whci_batch' => $batch,
                'whci_barcode' => $barcode,
                'whci_price' => $price,
                'whci_cash' => $timeCash,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

        }

        $this->calcRestOffer($offerId);

    }

    // Создаем новые элементы
    function addItems($docId, $docDate, $offerId, $count, $offerDocId, $docType, $barcode = null, $expirationDate = null, $batch = null, $price = null, $timeCash = 0)
    {

        $sign = 1;

        DB::table('whc_wh' . $this->warehouseId . '_items')->insert([
            'whci_status' => 0,
            'whci_date' => $docDate,
            'whci_doc_id' => $docId,
            'whci_offer_id' => $offerId,
            'whci_doc_offer_id' => $offerDocId,
            'whci_doc_type' => $docType,
            'whci_count' => $count,
            'whci_sign' => $sign,
            'whci_expiration_date' => $expirationDate,
            'whci_batch' => $batch,
            'whci_barcode' => $barcode,
            'whci_price' => $price,
            'whci_cash' => $timeCash,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

    }

    // Привязываем элементы к месту хранения
//    function setPlace($offerId, $count, $docOfferId, $placeId)
//    {
//
//        DB::table('whc_wh' . $warehouseId . '_items')
//            ->where('whci_offer_id', $offerId)
//            ->where('whci_in_doc_id', $docOfferId)
//            ->where('whci_status', 1)
//            ->select('whci_id')
//            ->get();
//
////        DB::table('whc_wh'.$warehouseId.'_items')->where('whci_offer_id', $offerId)->update([]);
//
//    }

    function setPlace($whOfferId, $placeId)
    {

        DB::table('whc_wh' . $this->warehouseId . '_items')
            ->where('whci_id', $whOfferId)
            ->update([
                'whci_place_id' => $placeId,
            ]);

//        DB::table('whc_wh'.$warehouseId.'_items')->where('whci_offer_id', $offerId)->update([]);

    }


}