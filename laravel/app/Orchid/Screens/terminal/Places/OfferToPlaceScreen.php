<?php

namespace App\Orchid\Screens\terminal\Places;

use App\Models\rwAcceptance;
use App\Models\rwBarcode;
use App\Models\rwOffer;
use App\Models\rwWarehouse;
use App\Orchid\Services\DocumentService;
use App\WhCore\WhCore;
use App\WhPlaces\WhPlaces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class OfferToPlaceScreen extends Screen
{

    private $docId, $shopId, $whId, $whName, $docStatus;

    public function screenBaseView(): string
    {
        return 'loyouts.base';

    }

    public function query($docId, Request $request): iterable
    {
        $validatedData = $request->validate([
            'offerWhId' => 'nullable|numeric',
            'scanCount' => 'nullable|numeric|min:0',
            'currentTime' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string',
            'action' => 'nullable|string',
        ]);

        $currentOffer = [];
        $currentTime = $scanCount = 0;
        $barcode = $action = '';
        $skip = false;

        $this->docId = $docId;

        $currentUser = Auth::user();

        if (isset($validatedData['barcode'])) $barcode = $validatedData['barcode'];
        if (isset($validatedData['currentTime'])) $currentTime = $validatedData['currentTime'];


        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager') || $currentUser->hasRole('warehouse_worker')) {

            if (isset($validatedData['action'])) $action = $validatedData['action'];
            if (isset($validatedData['scanCount'])) $scanCount = $validatedData['scanCount'];

            $dbCurrentAcceptance = rwAcceptance::where('acc_id', $this->docId)->where('acc_domain_id', $currentUser->domain_id)->with('getWarehouse')->first();
            $this->shopId = $dbCurrentAcceptance->acc_shop_id;
            $this->whId = $dbCurrentAcceptance->acc_wh_id;
            $this->whName = $dbCurrentAcceptance->getWarehouse->wh_name;
            $this->docStatus = $dbCurrentAcceptance->acc_status;

            $currentDocument = new DocumentService($docId);

            // **********************************************************
            // *** Выбран товар и отсканировано место. Сохраняем все
            // **********************************************************

            if ($action == 'savePlace' && isset($validatedData['offerWhId']) && isset($scanCount)) {

                $placeId = 0;
                $dbParentWh = rwWarehouse::find($this->whId);

                if (isset($dbParentWh) &&  $dbParentWh->wh_parent_id > 0) {
                    $place = new WhPlaces($barcode, $dbParentWh->wh_parent_id);
                    $placeId = $place->getPlaceId();
                }

                if ($placeId > 0) {
                    // ШК нормальный, привязываем товар

                    $currentDocument->saveOfferToPlace($validatedData['offerWhId'], $placeId, $scanCount, $currentTime);
                    $action = '';
                    $barcode = '';
                    $validatedData['offerWhId'] = 0;

                    Alert::info(__('Товар привязан!'));

                } else {
                    // Это ШК не место хранения

                    Alert::error(__('Отсканированный штрих-код не является штрих-кодом места хранения!'));
                    $action = 'selectPlace';
                    $barcode = '';

                }

            }

            // **********************************************************
            // *** Выбран конкретный товар, выбираем место
            // **********************************************************

            if ($action == 'selectPlace' && isset($validatedData['offerWhId']) && isset($scanCount)) {

                // Проверяем достаточно ли товара имеем для привязки

                $currentOffer = $currentDocument->getAcceptanceOffer($validatedData['offerWhId']);

                if (isset($currentOffer['ao_id'])) {

                    if ($currentOffer['ao_accepted'] >= $scanCount) {

                        // Все нормально, товара достаточно. Сканируем ШК места
                        $action = "scanPlace";

                    } else {

                        Alert::error(__('Товара слишком много! Я не смогу столько привязать!'));

                    }

                }

            }

            // **********************************************************
            // *** Если отсканирован баркод, ищем товар по баркоду
            // **********************************************************

            if ($barcode != '') {

                $dbOfferBarcode = rwBarcode::where('br_barcode', $barcode)->where('br_shop_id', $this->shopId)->first();

                if (isset($dbOfferBarcode->br_offer_id)) {
                    $offerId = $dbOfferBarcode->br_offer_id;

                    $validatedData['offerWhId'] = $currentDocument->getWhOfferId($offerId, 1);

                    if ($validatedData['offerWhId'] == 0)
                        Alert::error(__('Товара с таким штрих-кодом нет в данной накладной или он весь превязан!'));

                } else {

                    Alert::error(__('Товара с таким штрих-кодом нет в базе!'));

                }
            }

            // ********************************************************************
            // *** Формируем информацию о выбранном товаре и сохраняем данные
            // ********************************************************************

            if (isset($validatedData['offerWhId'])) {

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
                'docId' => $this->docId,
                'whId' => $this->whId,
                'shopId' => $this->shopId,
                'currentOffer' => $currentOffer,
                'offersList' => $dbOffersList,
                'action' => $action,
                'scanCount' => $scanCount,
            ];

        }

        return [];
    }

    public function name(): ?string
    {
        return __('Размещение товаров для приемки № ' . $this->docId);
    }

    public function description(): ?string
    {
        return __('Склад: ' . $this->whName);
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        return [
            Layout::view('Screens.Terminal.Places.ScanInput'),
            Layout::view('Screens.Terminal.Places.OfferDetail'),
            Layout::view('Screens.Terminal.Places.PlaceOffer'),
            Layout::view('Screens.Terminal.Places.OffersList'),
        ];
    }
}
