<?php

namespace App\WhCore;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Models\WhcWarehouse;

class WhCore
{
    private $warehouseId, $itemTableName;
    private $version = 1;

    /*
     *  whci_status:
     *  1 - ожидаем (новый)
     *  2 - в наличии
     *  3 - размещен
     *  4 - зарезервирован
     *  6 - отгружен
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

            $this->itemTableName = 'whc_wh'.$warehouseId.'_items';

            // Если таблица с элементами не создана, создаем её
            if (!Schema::hasTable($this->itemTableName)) {

                // Создание нового склада

                Schema::create($this->itemTableName, function (Blueprint $table) {
                    $table->id('whci_id');
                    $table->tinyInteger('whci_status')->unsigned()->index(); // 0 - не учитывать, 1 - учитывать в расчете остатков
                    $table->bigInteger('whci_offer_id')->unsigned()->index();
                    $table->bigInteger('whci_place_id')->unsigned()->nullable()->index();
                    $table->bigInteger('whci_doc_id')->unsigned()->index();
                    $table->bigInteger('whci_doc_offer_id')->unsigned()->index();
                    $table->tinyInteger('whci_doc_type')->unsigned()->index();
                    $table->double('whci_count')->unsigned();
                    $table->tinyInteger('whci_sign');
                    $table->date('whci_expiration_date')->nullable()->index();
                    $table->string('whci_batch', 15)->nullable()->index();
                    $table->string('whci_barcode', 30)->nullable()->index();
                    $table->double('whci_price')->nullable()->unsigned();
                    $table->integer('whci_cash')->nullable()->unsigned()->default(0);
                    $table->timestamps();
                    $table->softDeletes();
                });

                WhcWarehouse::create([
                    'whc_id' => $warehouseId,
                    'whc_ver' => $this->version,
                ]);

            }

            return true;

        } else {

            return false;

        }


    }

    public function getDocumentOffers($docId, $docType)
    {
        return DB::table('whc_wh'.$this->warehouseId.'_items')
            ->where('whci_doc_id', $docId)
            ->where('whci_doc_type', $docType);
    }

    public function getDocumentOffer($docId, $whOfferId, $docType)
    {
        return DB::table('whc_wh'.$this->warehouseId.'_items')
            ->where('whci_doc_id', $docId)
            ->where('whci_id', $whOfferId)
            ->where('whci_doc_type', $docType);
    }


    public function getWhOfferId($docId, $offerId, $docType)
    {
        $offerId = DB::table('whc_wh'.$this->warehouseId.'_items')
            ->where('whci_doc_id', $docId)
            ->where('whci_offer_id', $offerId)
            ->where('whci_doc_type', $docType)
            ->first('whci_id');

        if (isset($offerId->whci_id))
            return $offerId->whci_id;
                else
                    return 0;
    }

    /**
     * @return string
     */
    // Удаляем элемент из склада
    public function deleteItem($offerId, $docType): string
    {
        return DB::table('whc_wh'.$this->warehouseId.'_items')
            ->where('whci_id', $offerId)
            ->where('whci_doc_type', $docType)
            ->delete();

    }

    public function addItemCount($offerId, $count, $currentTime = 0)
    {
        $itemId = DB::table('whc_wh'.$this->warehouseId.'_items')
            ->where('whci_id', $offerId)
            ->first();

        if (isset($itemId->whci_id)) {
            $lastCount = $itemId->whci_count + $count;

            $time = $itemId->whci_cash;

            if ($time != $currentTime) {
                DB::table('whc_wh'.$this->warehouseId.'_items')
                    ->where('whci_id', $offerId)
                    ->update([
                        'whci_count' => $lastCount,
                        'whci_cash' => $currentTime,
                    ]);
            }
        }

        return true;
    }

    public function getCountOfPlacedFromDocument($docId, $docType)
    {
        return DB::table('whc_wh'.$this->warehouseId.'_items')
            ->where('whci_doc_id', $docId)
            ->where('whci_doc_type', $docType)
            ->where('whci_place_id', '>', 0)
            ->sum('whci_count');

    }
    public function saveOffers($docId, $docType, $docOfferId, $offerId, $status, $count, $barcode, $price, $expDate = NULL, $batch = NULL)
    {

        $validator = Validator::make([
            'docId' => $docId,
            'docType' => $docType,
            'docOfferId' => $docOfferId,
            'offerId' => $offerId,
            'status' => $status,
            'count' => $count,
            'barcode' => $barcode,
            'price' => $price,
            'expDate' => $expDate,
            'batch' => $batch,
        ], [
            'docId' => 'required|integer',
            'docType' => 'required|integer',
            'docOfferId' => 'required|integer',
            'offerId' => 'required|integer',
            'status' => 'required|integer',
            'count' => 'required|numeric',
            'barcode' => 'nullable|string',
            'price' => 'required|numeric',
            'expDate' => 'nullable|date_format:d.m.Y,Y-m-d', // Допускаем оба формата
            'batch' => 'nullable|string',
        ]);

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

        $dbCurrentOffer = DB::table('whc_wh'.$this->warehouseId.'_items')->where('whci_doc_offer_id', $docOfferId)->first();

        if (isset($dbCurrentOffer->whci_id)) {

            DB::table('whc_wh'.$this->warehouseId.'_items')->where('whci_id', $dbCurrentOffer->whci_id)->update([
                'whci_count' => $count,
                'whci_expiration_date' => $expDate,
                'whci_batch' => $batch,
                'whci_barcode' => $barcode,
                'whci_price' => $price,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        } else {

            $sign = 1;

            DB::table('whc_wh'.$this->warehouseId.'_items')->insert([
                'whci_status' => $status,
                'whci_offer_id' => $offerId,
                'whci_doc_id' => $docId,
                'whci_doc_offer_id' => $docOfferId,
                'whci_doc_type' => $docType,
                'whci_count' => $count,
                'whci_sign' => $sign,
                'whci_expiration_date' => $expDate,
                'whci_batch' => $batch,
                'whci_barcode' => $barcode,
                'whci_price' => $price,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

        }

    }

    // Создаем новые элементы
    function addItems($offerId, $count, $docInId, $barcode = null, $expirationDate = null, $batch = null, $priceId = null) {

        DB::table('whc_wh'.$warehouseId.'_items')->insert([
            'whci_status' => $offerId,
            'whci_offer_id' => $offerId,
            'whci_place_id' => $offerId,
            'whci_doc_id' => $offerId,
            'whci_doc_status' => $offerId,
            'whci_doc_type' => $offerId,
            'whci_count' => $count,
            'whci_sign' => $offerId,
            'whci_expiration_date' => $offerId,
            'whci_batch' => $offerId,
            'whci_barcode' => $offerId,
            'whci_price' => $offerId,
        ]);

    }

    // Привязываем элементы к месту хранения
    function setPlace($offerId, $count, $docInId, $placeId) {

        DB::table('whc_wh'.$warehouseId.'_items')
            ->where('whci_offer_id', $offerId)
            ->where('whci_in_doc_id', $docInId)
            ->where('whci_status', 1)
            ->select('whci_id')
            ->get();

//        DB::table('whc_wh'.$warehouseId.'_items')->where('whci_offer_id', $offerId)->update([]);

    }



}