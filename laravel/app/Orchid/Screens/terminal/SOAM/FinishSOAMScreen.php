<?php
// Выводим информацию о том, что заказ собран.
// Завершаем заказ.


namespace App\Orchid\Screens\terminal\SOAM;

use App\Models\rwOrder;
use App\Models\rwOrderAssembly;
use App\Models\rwOrderSorting;
use App\Models\rwSettingsSoa;
use App\Models\rwWarehouse;
use App\Orchid\Services\OrderService;
use App\Orchid\Services\SOAService;
use App\Services\CustomTranslator;
use App\WhPlaces\WhPlaces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class FinishSOAMScreen extends Screen
{
    private $orderId;

    public function screenBaseView(): string
    {
        return 'loyouts.base';

    }

    public function query($soaId, $orderId, Request $request): iterable
    {
        $validatedData = $request->validate([
            'barcode' => 'nullable|string',
        ]);

        $currentUser = Auth::user();

        isset($validatedData['barcode']) ? $barcode = $validatedData['barcode'] : $barcode = '';

        $this->orderId = $orderId;
        $placeTypeName = '';
        $placeType = '';
        $action = '';

        // Получаем заказ для сборки
        $dbOrder = rwOrder::find($orderId);

        // Если заказ есть
        if ($dbOrder) {

            $dbSOASettings = rwSettingsSoa::where('ssoa_id', $soaId)
                ->with('getFinishPlace')
                ->first();

            if ($dbSOASettings) {

                $placeTypeName = $dbSOASettings->getFinishPlace->pt_name;
                $placeType = $dbSOASettings->getFinishPlace->pt_id;

            }

            // Если есть баркод
            if ($barcode != '') {

                $ffWhId = rwWarehouse::where('wh_id', $dbOrder->o_wh_id)->first()->wh_parent_id;

                $currentPlace = new WhPlaces($barcode, $ffWhId);

                // Если отсканировано правильное место хранения завершаю заказ
                if ($currentPlace->getPlaceId() > 0 && $currentPlace->getType() == $placeType) {

                    $dbOrder->o_status_id = 60;
                    $dbOrder->o_order_place = $currentPlace->getPlaceId();
                    $dbOrder->save();

                    // Сохраняю место завершения заказа в таблице сортировки
                    rwOrderSorting::where('os_order_id', $orderId)->delete();

                    $dbOrderOffers = rwOrderAssembly::where('oa_order_id', $orderId)->get();

                    foreach ($dbOrderOffers as $dbOrderOffer) {

                        rwOrderSorting::create([
                            'os_user_id' => $currentUser->id,
                            'os_order_id' => $orderId,
                            'os_offer_id' => $dbOrderOffer->oa_offer_id,
                            'os_place_id' => $currentPlace->getPlaceId(),
                            'os_qty' => $dbOrderOffer->oa_qty,
                            'os_barcode' => $dbOrderOffer->oa_barcode,
                            'os_data' => date('Y-m-d H:i:s', time()),
                            'os_cash' => time(),
                        ]);

                    }

                    $action = 'finishOrder';

                    $serviceOrder = new OrderService($dbOrder->o_id);
                    $serviceOrder->resaveOrderRests();

                }

                // Если отсканировано место хранения для отмены заказа, отменяю заказ
                if ($currentPlace->getPlaceId() > 0 && $currentPlace->getType() == 108) {

                    $dbOrder->o_status_id = 65;
                    $dbOrder->o_order_place = $currentPlace->getPlaceId();
                    $dbOrder->save();

                    $action = 'cancelOrder';

                }

            }

        }

        return [
            'soaId' => $soaId,
            'dbOrder' => $dbOrder,
            'placeTypeName' => $placeTypeName,
            'placeType' => $placeType,
            'action' => $action,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return CustomTranslator::get('Завершение упаковки заказа') . ' №: ' . $this->orderId;
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
            Layout::view('Screens.Terminal.SOAM.FinishSOAM'),
            Layout::view('Screens.Terminal.SOAM.EndFinishSOAM'),
            Layout::view('Screens.Terminal.SOAM.cancelSOAM'),
        ];
    }
}
