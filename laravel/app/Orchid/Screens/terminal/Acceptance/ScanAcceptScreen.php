<?php

namespace App\Orchid\Screens\terminal\Acceptance;

use App\Models\rwAcceptance;
use App\Models\rwBarcode;
use App\Orchid\Services\DocumentService;
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
            'offerId' => 'nullable|numeric|min:0', // Цена должна быть числом >= 0
            'scanCount' => 'nullable|numeric|min:0', // Цена должна быть числом >= 0
            'currentTime' => 'nullable|numeric|min:0', // Цена должна быть числом >= 0
            'barcode' => 'nullable|string', // Цена должна быть числом >= 0
        ]);

        $currentTime = 0;

        $this->docId = $docId;
        $currentUser = Auth::user();
        $currentOffer = [];

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager') || $currentUser->hasRole('warehouse_worker')) {

            // Получаем данные о документе

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

            $currentDocument = new DocumentService($this->whId);

            // Если отсканирован баркод, ищем товар по баркоду

            if (isset($validatedData['barcode'])) {
                $barcode = $validatedData['barcode'];

                $dbOffer = rwBarcode::where('br_barcode', $barcode)->where('br_shop_id', $this->shopId)->first();

                if (isset($dbOffer->br_offer_id)) {
                    $offerId = $dbOffer->br_offer_id;

                    $validatedData['offerId'] = $currentDocument->getWhOfferId($this->docId, $offerId, 1);

                }

            }

            // Формируем информацию о выбранном товаре

            if (isset($validatedData['offerId']) && $validatedData['offerId'] > 0) {

                // Сохраняем полученное количество
                if (isset($validatedData['scanCount']) && $validatedData['scanCount'] != 0) {

                    if ($validatedData['currentTime'] > 0) $currentTime = $validatedData['currentTime'];

                    $currentDocument->addItemCount($validatedData['offerId'], $validatedData['scanCount'], $currentTime);

                    Alert::success(__('Товар добавлен в накладную!'));

                    $validatedData['offerId'] = 0;

                }

                // Получаем данные о товаре
                if ($validatedData['offerId'] > 0) {
                    $currentOffer = $currentDocument->getAcceptanceOffer($this->docId, $validatedData['offerId']);
                }

//                dump($currentOffer);

            }

            // Формируем список оферов для вывода

            $dbOffersList = $currentDocument->getAcceptanceList($this->docId);

            return [
                'docId' => $docId,
                'offersList' => $dbOffersList,
                'currentOffer' => $currentOffer,
                'settingExeptDate' => true,
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
        return __('Приемкам № ' . $this->docId . ' (' . $this->whName . ')');
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
