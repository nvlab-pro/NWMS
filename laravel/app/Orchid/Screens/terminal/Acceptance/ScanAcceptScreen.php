<?php

namespace App\Orchid\Screens\terminal\Acceptance;

use App\Models\rwAcceptance;
use App\Models\rwAcceptanceOffer;
use App\Models\rwBarcode;
use App\Models\rwOffer;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Services\DocumentService;
use App\Orchid\Services\WarehouseUserActionService;
use App\Services\CustomTranslator;
use App\WhCore\WhCore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class ScanAcceptScreen extends Screen
{
    public $docId = null, $whName, $whId, $shopId;

    public function screenBaseView(): string
    {
        return 'loyouts.base';

    }

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query($docId, Request $request): iterable
    {

        $validatedData = $request->validate([
            'offerId' => 'nullable|numeric',
            'offerWhId' => 'nullable|numeric',
            'scanCount' => 'nullable|numeric|min:0',
            'currentTime' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string',
            'saveBarcode' => 'nullable|string',
            'scanProdDate' => 'nullable|string',
            'scanExpDate' => 'nullable|string',
            'scanBatch' => 'nullable|string',
        ]);

        $currentUser = Auth::user();
        $this->docId = $docId;
        $currentOffer = [];
        $currentTime = 0;
        $barcode = '';
        $skip = false;
        $docDate = date('Y-m-d H:i:s');
        isset($validatedData['scanProdDate']) ? $scanProdDate = $validatedData['scanProdDate'] : $scanProdDate = NULL;
        isset($validatedData['scanExpDate']) ? $scanExpDate = $validatedData['scanExpDate'] : $scanExpDate = NULL;
        isset($validatedData['scanBatch']) ? $scanBatch = $validatedData['scanBatch'] : $scanBatch = NULL;

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager') || $currentUser->hasRole('warehouse_worker')) {

            // **********************************************************
            // *** Получаем данные о документе
            // **********************************************************

            $dbAcceptList = rwAcceptance::where('acc_domain_id', $currentUser->domain_id)
                ->whereIn('acc_status', [1, 2])
                ->where('acc_id', $docId)
                ->with('getAccStatus')
                ->with('getWarehouse')
                ->orderByDesc('acc_id')
                ->first();

            $this->whName = $dbAcceptList->getWarehouse->wh_name;
            $this->whId = $dbAcceptList->getWarehouse->wh_id;
            $this->shopId = $dbAcceptList->acc_shop_id;
            $docDate = $dbAcceptList->acc_date;

            $currentDocument = new DocumentService($this->docId);

            if (isset($validatedData['barcode'])) $barcode = $validatedData['barcode'];

            // **********************************************************
            // *** Если отсканирован баркод, ищем товар по баркоду
            // **********************************************************

            if (isset($validatedData['barcode'])) {

                $barcode = $validatedData['barcode'];

                $dbOfferBarcode = rwBarcode::where('br_barcode', $barcode)->where('br_shop_id', $this->shopId)->first();

                if (isset($dbOfferBarcode->br_offer_id)) {
                    $offerId = $dbOfferBarcode->br_offer_id;

                    $validatedData['offerWhId'] = $currentDocument->getWhOfferId($offerId, 1);

                    if ($validatedData['offerWhId'] == 0) {

                        $dbOffer = rwOffer::find($dbOfferBarcode->br_offer_id);

                        if (isset($dbOffer->of_id)) {

                            $currentOffer = [
                                'ao_id' => 0,
                                'ao_offer_id' => $dbOfferBarcode->br_offer_id,
                                'ao_wh_offer_id' => -1,
                                'oa_status' => 0,
                                'ao_img' => $dbOffer->of_img,
                                'ao_name' => $dbOffer->of_name,
                                'ao_article' => $dbOffer->of_article,
                                'ao_batch' => $scanBatch,
                                'ao_production_date' => $scanProdDate,
                                'ao_expiration_date' => $scanExpDate,
                                'ao_barcode' => $barcode,
                                'ao_expected' => 0,
                                'ao_accepted' => 0,
                                'ao_placed' => 0,

                            ];

                            Alert::warning(CustomTranslator::get('Такого товара нет в накладной, но вы можете его добавить!'));

                        } else {

                            Alert::error(CustomTranslator::get('Товара с таким штрих-кодом нет в базе!'));

                        }

                    }

                }

            }

            // ********************************************************************
            // *** Формируем информацию о выбранном товаре и сохраняем данные
            // ********************************************************************

            if (isset($validatedData['offerWhId'])) {

                // ************************************************
                // *** Сохраняем полученные данные в накладной

                if (isset($validatedData['currentTime']) && $validatedData['currentTime'] > 0) $currentTime = $validatedData['currentTime'];

                if ($validatedData['offerWhId'] > 0 && isset($validatedData['scanCount']) && $validatedData['scanCount'] != 0) {

                    // Если товар есть в накладной добавляем в нее товар (если это не перезагрузка страницы)

                    if ($currentDocument->addItemCount($validatedData['offerWhId'], $docDate, $validatedData['scanCount'], $currentTime, $scanExpDate, $scanBatch, $scanProdDate, $currentTime)) {

                        Alert::success(CustomTranslator::get('Товар добавлен в накладную!'));

                        // Меняем статус у накладной с new на "принимается"
                        rwAcceptance::where('acc_id', $this->docId)
                            ->where('acc_status', 1)
                            ->update([
                                'acc_status' => 2,
                            ]);

                        // Обновляем остатки на морде документа
                        $currentDocument->updateRest(1);

                        // Пересчитываем остатки
                        $currentWarehouse = new WhCore($this->whId);
                        $currentWarehouse->calcRestOffer($validatedData['offerId']);

                        $validatedData['offerWhId'] = 0;

                        // Сохраняем данные в статистике
                        WarehouseUserActionService::logAction([
                            'ua_user_id' => $currentUser->id, // ID текущего кладовщика
                            'ua_lat_id' => 1,            // ID типа действия (например, 1 — "подбор товара")
                            'ua_domain_id' => $currentUser->domain_id,    // ID компании / окружения
                            'ua_wh_id' => $this->whId, // ID склада
                            'ua_shop_id' => $this->shopId,      // ID магазина, если применимо
                            'ua_place_id' => NULL,     // ID ячейки склада
                            'ua_entity_type' => 'offer',      // Тип сущности (например, offer, order)
                            'ua_doc_id' => $this->docId,     // ID выбранного товара
                            'ua_entity_id' => $validatedData['offerId'],     // ID выбранного товара
                            'ua_quantity' => $validatedData['scanCount'],          // Количество товара
                        ]);
                    }

                } else {

                    // Если товара нет в накладной добавляем новый товар в накладную

                    if (isset($validatedData['offerId']) && $validatedData['offerWhId'] == -1 && $validatedData['offerId'] > 0) {

                        $currentOffer = rwOffer::where('of_id', $validatedData['offerId'])->where('of_shop_id', $this->shopId)->first();

                        if (isset($currentOffer->of_id)) {

                            // Добавляем новый товар в накладную

                            $dbAccptence = rwAcceptanceOffer::create([
                                'ao_acceptance_id' => $this->docId,
                                'ao_offer_id' => $validatedData['offerId'],
                            ]);

                            $currentWarehouse = new WhCore($this->whId);

                            $tmpBarcode = '';
                            $expDate = NULL;
                            $prodDate = NULL;
                            $batch = NULL;
                            if (isset($validatedData['saveBarcode'])) $tmpBarcode = $validatedData['saveBarcode'];
                            if (isset($validatedData['scanExpDate'])) $expDate = $validatedData['scanExpDate'];
                            if (isset($validatedData['scanProdDate'])) $prodDate = $validatedData['scanProdDate'];
                            if (isset($validatedData['scanBatch'])) $batch = $validatedData['scanBatch'];

                            if (strlen($expDate) == 6) {
                                $expDate = '20' . substr($expDate, 4, 2) . '-' . substr($expDate, 2, 2) . '-' . substr($expDate, 0, 2);
                            }

                            $currentWarehouse->saveOffers(
                                $this->docId,
                                $docDate,
                                1,                       // Приемка (таблица rw_lib_type_doc)
                                $dbAccptence->ao_id,                                // ID офера в документе
                                $validatedData['offerId'],                                // оригинальный ID товара
                                0,
                                $validatedData['scanCount'],
                                $tmpBarcode,
                                0,
                                $expDate,
                                $batch,
                                $prodDate,
                                $currentTime,
                            );

                            // Сохраняем данные в статистике
                            WarehouseUserActionService::logAction([
                                'ua_user_id' => $currentUser->id, // ID текущего кладовщика
                                'ua_lat_id' => 1,            // ID типа действия (например, 1 — "подбор товара")
                                'ua_domain_id' => $currentUser->domain_id,    // ID компании / окружения
                                'ua_wh_id' => $this->whId, // ID склада
                                'ua_shop_id' => $this->shopId,      // ID магазина, если применимо
                                'ua_place_id' => NULL,     // ID ячейки склада
                                'ua_entity_type' => 'offer',      // Тип сущности (например, offer, order)
                                'ua_doc_id' => $this->docId,     // ID выбранного товара
                                'ua_entity_id' => $validatedData['offerId'],     // ID выбранного товара
                                'ua_barcode'     => $tmpBarcode,          // Баркод
                                'ua_quantity' => $count,          // Количество товара
                            ]);

                            $skip = true;

                            Alert::success(CustomTranslator::get('Новый товар добавлен в накладную!'));

                            // Меняем статус у накладной с new на "принимается"
                            rwAcceptance::where('acc_id', $this->docId)
                                ->where('acc_status', 1)
                                ->update([
                                    'acc_status' => 2,
                                ]);

                        } else {
                            Alert::error(CustomTranslator::get('Такого товара нет в базе!!'));
                        }

                    }

                }

                // *********************************************
                // *** Формируем информацию о выбранном товаре

                if ($validatedData['offerWhId'] > 0) {
                    $currentOffer = $currentDocument->getAcceptanceOffer($validatedData['offerWhId']);
                }

            }

            // *********************************************
            // *** Формируем список оферов для вывода
            // *********************************************

            $dbOffersList = $currentDocument->getAcceptanceList();

            return [
                'docId' => $docId,
                'offersList' => $dbOffersList,
                'currentOffer' => $currentOffer,
                'settingExeptDate' => true,
                'saveBarcode' => $barcode,
                'skip' => $skip,
            ];

        }

        return [];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return CustomTranslator::get('Приемкам № ' . $this->docId . ' (' . $this->whName . ')');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::view('Screens.Terminal.Acceptance.ScanInput'),
            Layout::view('Screens.Terminal.Acceptance.OfferDetail'),
            Layout::view('Screens.Terminal.Acceptance.OffersList'),
        ];
    }
}
