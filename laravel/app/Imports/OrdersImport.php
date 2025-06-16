<?php
/**
 * Мульти-импорт заказов из Excel.
 * ─ при каждом новом order_ext_id создаётся (или берётся) отдельный заказ
 * ─ ID склада (wh) и магазина (shop) берутся из формы, одинаковы для всех заказов файла
 */

namespace App\Imports;

use App\Models\{
    rwOrder, rwOrderOffer, rwOrderContact, rwCompany, rwWarehouse, rwShop, rwOffer, rwImportLog
};
use App\Services\CustomTranslator;
use App\WhCore\WhCore;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\{
    ToCollection, WithHeadingRow, WithChunkReading,
    SkipsOnFailure, SkipsFailures
};

class OrdersImport implements ToCollection, WithHeadingRow, WithChunkReading, SkipsOnFailure
{
    use SkipsFailures;

    /** @var int[]  все созданные/обновлённые заказы (для последующего вывода) */
    private array $orderIds = [];

    /** @var rwOrder[] кеш заказов по order_ext_id */
    private array $orders = [];

    public function __construct(
        private readonly Request $request,
        private readonly int     $importId
    ) {
        // shop/wh фиксированы для всего файла
        $this->shopId = (int)$request->o_shop_id;
        $this->whId   = (int)$request->o_wh_id;
    }

    /* ---------- Excel / Maatwebsite настройки ---------- */

    public function chunkSize(): int  { return 1_000; }

    /* ---------- Основная обработка ---------- */

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            // «ключ» заказа
            $extId = trim((string)($row['order_ext_id'] ?? ''));

            if ($extId === '') {
                // если не указан – генерируем surrogate-key (чтобы каждый заказ был уникален)
                $extId = 'AUTO-' . md5(json_encode($row));
            }

            /** @var rwOrder $order */
            $order = $this->orders[$extId] ??= $this->createOrUpdateOrder($row, $extId);

            // сохраняем ID первого созданного заказа (для redirect-а)
            $this->orderIds[ $order->o_id ] = $order->o_id;

            // товары
            $this->attachOffer($order, $row);
        }
    }

    /* ---------- HELPERS ---------- */

    /** Создание/обновление заказа и базовых сущностей */
    private function createOrUpdateOrder(array $row, string $extId): rwOrder
    {
        $currentUser = Auth::user();

        $order = rwOrder::firstOrCreate(
            ['o_ext_id' => $extId],
            [
                'o_status_id'     => 10,
                'o_domain_id'     => $currentUser->domain_id,
                'o_user_id'       => $currentUser->id,
                'o_shop_id'       => $this->shopId,
                'o_wh_id'         => $this->whId,
                'o_type_id'       => $row['o_type_id']               ?? 1,
                'o_date'          => ($row['order_date']      ?? now())->startOfDay(),
                'o_date_send'     => ($row['order_date_send'] ?? now())->startOfDay(),
                'o_customer_type' => $row['order_customer_type']     ?? 0,
                'o_company_id'    => $row['order_company_id']        ?? null,
            ]
        );

        /* ---- Контакт клиента (создаём один раз) ---- */
        if ($order->getContact()->doesntExist() && !empty($row['oc_phone'])) {
            rwOrderContact::create([
                'oc_order_id'     => $order->o_id,
                'oc_first_name'   => $row['oc_first_name']   ?? '',
                'oc_middle_name'  => $row['oc_middle_name']  ?? '',
                'oc_last_name'    => $row['oc_last_name']    ?? '',
                'oc_phone'        => $row['oc_phone']        ?? '',
                'oc_email'        => $row['oc_email']        ?? '',
                'oc_city_id'      => $row['oc_city_id']      ?? null,
                'oc_postcode'     => $row['oc_postcode']     ?? '',
                'oc_full_address' => $row['oc_full_address'] ?? '',
            ]);
        }

        /* ---- Служба доставки / ПВЗ ----
           При необходимости – запишите данные в rwOrderDs или другую таблицу,
           используя те же row['ds_id'], row['ds_pp_id'].
        */

        return $order;
    }

    /** Добавление / обновление товарной строки заказа + склад */
    private function attachOffer(rwOrder $order, array $row): void
    {
        // ① Поиск оффера
        /** @var rwOffer|null $offer */
        $offer = rwOffer::query()
            ->when(!empty($row['of_id']),  fn($q) => $q->where('of_id',       $row['of_id']))
            ->when(empty($row['of_id']) && !empty($row['of_ext_id']),
                fn($q) => $q->where('of_ext_id',  $row['of_ext_id']))
            ->when(empty($row['of_id']) && empty($row['of_ext_id']) && !empty($row['of_sku']),
                fn($q) => $q->where('of_sku',     $row['of_sku']))
            ->when(empty($row['of_id']) && empty($row['of_ext_id']) && empty($row['of_sku']) && !empty($row['of_article']),
                fn($q) => $q->where('of_article', $row['of_article']))
            ->first();

        if (!$offer) {
            rwImportLog::create([
                'il_import_id' => $this->importId,
                'il_date'      => now(),
                'il_operation' => 0,
                'il_name'      => 'Товар не найден',
                'il_fields'    => json_encode($row),
            ]);
            return;
        }

        // ② Запись строки заказа
        $orderOffer = rwOrderOffer::updateOrCreate(
            [
                'oo_order_id' => $order->o_id,
                'oo_offer_id' => $offer->of_id,
            ],
            [
                'oo_qty'      => $row['oo_qty']      ?? 1,
                'oo_oc_price' => $row['oo_oc_price'] ?? 0,
                'oo_price'    => $row['oo_price']    ?? 0,
            ]
        );

        // ③ Обновление склада
        (new WhCore($this->whId))->saveOffers(
            $order->o_id,
            now(),
            2,
            $orderOffer->oo_id,
            $offer->of_id,
            0,
            $row['oo_qty'] ?? 1,
            null,
            $row['oo_oc_price'] ?? 0
        );
    }

    /* ---------- Служебные геттеры ---------- */

    /** Для redirect’а после импорта */
    public function getFirstOrderId(): ?int
    {
        return array_key_first($this->orderIds) ?: null;
    }

    public function getWhId(): int
    {
        return $this->whId;
    }
}
