<?php

namespace App\Imports;

use App\Models\rwAcceptance;
use App\Models\rwAcceptanceOffer;
use App\Models\rwBarcode;
use App\Models\rwImportLog;
use App\Models\rwOffer;
use App\Models\rwShop;
use App\Models\rwWarehouse;
use App\Services\CustomTranslator;
use App\WhCore\WhCore;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;

class AcceptancesImport implements ToModel, WithHeadingRow
{
    protected int $importId, $acceptId, $whId;

    public function __construct($request, int $importId, $acceptId)
    {
        $this->importId = $importId;
        $this->acceptId = $acceptId;

        if ($acceptId == 0) {

            $currentUser = Auth::user();

            // Создаем приходную накладную
            $request->validate([
                'acc_ext_id' => 'nullable|integer',
                'acc_wh_id' => 'required|integer',
                'acc_shop_id' => 'required|integer',
                'acc_date' => 'required|string|max:10',
                'acc_type' => 'required|integer',
                'acc_comment' => 'nullable|string|max:255',
            ]);

            $userId = rwWarehouse::query()->where('wh_id', $request->acc_wh_id)->first()->wh_user_id;

            $this->acceptId = rwAcceptance::create([
                'acc_status' => 1,
                'acc_ext_id' => $request->acc_ext_id,
                'acc_user_id' => $userId,
                'acc_wh_id' => $request->acc_wh_id,
                'acc_shop_id' => $request->acc_shop_id,
                'acc_date' => $request->acc_date . ' 00:00:00', // если нужно привести к datetime
                'acc_type' => $request->acc_type,
                'acc_domain_id' => $currentUser->domain_id,
                'acc_comment' => $request->acc_comment,
            ])->acc_id;

            $this->whId = $request->acc_wh_id;

        } else {

            $this->whId = rwAcceptance::where('acc_id', $acceptId)->first()->acc_wh_id;

        }
    }

    public function model(array $row)
    {
        $currentUser = Auth::user();

        $shopId = rwAcceptance::where('acc_id', $this->acceptId)->first()->acc_shop_id;

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
                'ao_acceptance_id' => $this->acceptId ?? null,
                'ao_offer_id' => $offer->of_id ?? null,
                'ao_expected' => $row['ao_expected'] ?? null,
            ];

            // если все поля пустые — пропускаем строку
            if (collect($payloadFields)->filter(fn($v) => !is_null($v) && $v !== '')->isEmpty()) {
                return null;
            }

            // Ищем товар в накладной
            $dbCurrentAcceptance = rwAcceptanceOffer::where('ao_acceptance_id', $this->acceptId)
                ->where('ao_offer_id', $offer->of_id)
                ->first();

            if (!$dbCurrentAcceptance) {
                // Если товара в накладной еще нет

                // Создание лога
                rwImportLog::create([
                    'il_import_id' => $this->importId,
                    'il_date' => date('Y-m-d H:i:s'),
                    'il_operation' => 1,
                    'il_name' => CustomTranslator::get('Добавление нового товара в документ') . ': ' . $this->acceptId,
                    'il_fields' => json_encode([
                        'ao_acceptance_id'  => $this->acceptId ?? null,
                        'ao_offer_id'       => $offer->of_id ?? null,
                        'ao_expected'       => $row['ao_expected'] ?? null,
                        'ao_barcode'        => $row['ao_barcode'] ?? null,
                        'oa_price'          => $row['oa_price'] ?? null,
                        'oa_batch'          => $row['oa_batch'] ?? null,
                        'oa_expiration_date' => $row['oa_expiration_date'] ?? null,

                    ]),
                ]);

                // Создаю запись
                $currentAcceptance = rwAcceptanceOffer::create($payloadFields);
                $status = 0;

                // Добавляю запись на склад
                $currentWarehouse = new WhCore($this->whId);

                $currentWarehouse->saveOffers(
                    $this->acceptId,
                    date('Y-m-d H:i:s', time()),
                    1,                                  // Приемка (таблица rw_lib_type_doc)
                    $currentAcceptance->ao_id,                                        // ID офера в документе
                    $offer->of_id,          // оригинальный ID товара
                    $status,
                    0,
                    $row['ao_barcode'] ?? null,
                    $row['oa_price'] ?? 0,
                    $row['oa_expiration_date'] ?? null,
                    $row['oa_batch'] ?? null,
                    $row['oa_production_date'] ?? null,
                );
            }
        }
    }

    public function getAcceptId(): int
    {
        return $this->acceptId;
    }

    public function getWhId(): int
    {
        return $this->whId;
    }

}
