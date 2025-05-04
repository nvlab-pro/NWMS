<?php

namespace App\Imports;

use App\Models\rwAcceptance;
use App\Models\rwAcceptanceOffer;
use App\Models\rwImportLog;
use App\Models\rwOffer;
use App\Models\rwOrder;
use App\Models\rwOrderOffer;
use App\Models\rwWarehouse;
use App\Services\CustomTranslator;
use App\WhCore\WhCore;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OrdersImport implements ToModel, WithHeadingRow
{
    protected int $importId, $orderId, $whId;

    public function __construct($request, int $importId, $orderId)
    {
        $this->importId = $importId;
        $this->orderId = $orderId;

        if ($orderId == 0) {

            $currentUser = Auth::user();

            // Создаем приходную накладную
            $request->validate([
                'o_type_id' => 'nullable|integer',
                'o_wh_id' => 'required|integer',
                'o_domain_id' => 'required|integer',
                'o_user_id' => 'required|integer',
                'o_ext_id' => 'nullable|string|max:30',
                'o_shop_id' => 'required|integer',
                'o_date' => 'required|string|max:10',
                'o_date_send' => 'nullable|string|max:10',
            ]);

            $userId = rwWarehouse::query()->where('wh_id', $request->o_wh_id)->first()->wh_user_id;

            if ($request->o_date_send == '') $request->o_date_send = date('Y-m-d');

            $this->orderId = rwOrder::create([
                'o_status_id' => 10,
                'o_type_id' => $request->o_type_id,
                'o_user_id' => $userId,
                'o_ext_id' => $request->o_ext_id,
                'o_shop_id' => $request->o_shop_id,
                'o_wh_id' => $request->o_wh_id,
                'o_date' => $request->o_date . ' 00:00:00', // если нужно привести к datetime
                'o_date_send' => $request->o_date_send . ' 00:00:00', // если нужно привести к datetime
                'o_domain_id' => $currentUser->domain_id,
            ])->o_id;

            $this->whId = $request->o_wh_id;

        } else {

            $this->whId = rwOrder::where('o_id', $orderId)->first()->o_wh_id;

        }
    }

    public function model(array $row)
    {
        $currentUser = Auth::user();

        $shopId = rwOrder::where('o_id', $this->orderId)->first()->o_shop_id;

        $offer = rwOffer::query()
            ->where('of_shop_id', $shopId)
            ->when(!empty($row['of_id']), fn($q) => $q->where('of_id', (int) $row['of_id']))
            ->when(empty($row['of_id']) && !empty($row['of_ext_id']), fn($q) => $q->where('of_ext_id', $row['of_ext_id']))
            ->when(empty($row['of_id']) && empty($row['of_ext_id']) && !empty($row['of_sku']), fn($q) => $q->where('of_sku', $row['of_sku']))
            ->when(empty($row['of_id']) && empty($row['of_ext_id']) && empty($row['of_sku']) && !empty($row['of_article']), fn($q) => $q->where('of_article', $row['of_article']))
            ->first();

        if ($offer) {
            // Сначала ищем товар только в рамках текущего магазина

            $payloadFields = [
                'oo_order_id' => $this->orderId ?? null,
                'oo_offer_id' => $offer->of_id ?? null,
                'oo_qty' => $row['oo_qty'] ?? null,
                'oo_oc_price' => $row['oo_oc_price'] ?? null,
                'oo_price' => $row['oo_price'] ?? null,
            ];

            // если все поля пустые — пропускаем строку
            if (collect($payloadFields)->filter(fn($v) => !is_null($v) && $v !== '')->isEmpty()) {
                return null;
            }

            // Ищем товар в накладной
            $dbCurrentOrder = rwOrderOffer::where('oo_order_id', $this->orderId)
                ->where('oo_offer_id', $offer->of_id)
                ->first();

            if (!$dbCurrentOrder) {
                // Если товара в накладной еще нет

                // Создание лога
                rwImportLog::create([
                    'il_import_id' => $this->importId,
                    'il_date' => date('Y-m-d H:i:s'),
                    'il_operation' => 1,
                    'il_name' => CustomTranslator::get('Добавление нового товара в заказ') . ': ' . $this->orderId,
                    'il_fields' => json_encode([
                        'oo_order_id'       => $this->orderId ?? null,
                        'oo_offer_id'       => $offer->of_id ?? null,
                        'oo_qty'            => $row['oo_qty'] ?? null,
                        'oo_oc_price'        => $row['oo_oc_price'] ?? null,
                        'oo_price'          => $row['oo_price'] ?? null,
                    ]),
                ]);

                // Создаю запись
                $currentOrder = rwOrderOffer::create($payloadFields);
                $status = 0;

                // Добавляю запись на склад
                $currentWarehouse = new WhCore($this->whId);

                $currentWarehouse->saveOffers(
                    $this->orderId,
                    date('Y-m-d H:i:s', time()),
                    2,                                  // Приемка (таблица rw_lib_type_doc)
                    $currentOrder->oo_id,                                        // ID офера в документе
                    $offer->of_id,          // оригинальный ID товара
                    $status,
                    $row['oo_qty'],
                    null,
                    $row['oo_oc_price'] ?? 0,
                    null,
                    null
                );
            }
        }
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getWhId(): int
    {
        return $this->whId;
    }

}
