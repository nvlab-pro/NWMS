<?php

namespace App\Orchid\Screens\WorkTables\Marking;

use App\Integrations\DeliveryServices;
use App\Models\rwOrder;
use App\Models\rwOrderDs;
use App\Models\rwOrderMeasurement;
use App\Models\rwSettingsMarking;
use App\Services\CustomTranslator;
use Auth;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Alert;


class MarkingScreen extends Screen
{
    public function screenBaseView(): string
    {
        return 'loyouts.base';

    }

    public function query($queueId, Request $request): iterable
    {
        $currentUser = Auth::user();
        $orderId = 0;
        $order = [];
        $label = 0;

        $dsOrderId = '';
        $currentLabel = [];

        $dX = $dY = $dZ = $Weight = 0;

        // Получаем заказ если найден
        if (isset($request->orderId)) {
            $orderId = $request->orderId;

            $order = rwOrder::where('o_domain_id', $currentUser->domain_id)
                ->where('o_id', $orderId)
                ->with(['offers', 'getMeasurements', 'getDs.getSource', 'getDs.getDsName', 'offers.offer'])
                ->first();

            if ($order) {
                $dX = $order->getMeasurements->om_x ?? 0;
                $dY = $order->getMeasurements->om_y ?? 0;
                $dZ = $order->getMeasurements->om_z ?? 0;
                $Weight = $order->getMeasurements->om_weight ?? 0;
            }

        }

        // Считываем настройки очереди
        $currentQueues = rwSettingsMarking::where('sm_domain_id', $currentUser->domain_id)
            ->where('sm_status_id', 1)
            ->where('sm_id', $queueId)
            ->with('getDS')
            ->first();

        $dsId = $currentQueues->sm_ds_id;


        // Отсканирован штрих-код ВГХ
        if (isset($request->barcode) && $orderId > 0) {
            if (strpos($request->barcode, '/') > 0) {

                $code = explode('/', str_replace('*', '', $request->barcode));
                if ($code[0] == 'X') {
                    $dX = $code[1];

                    $resMes = rwOrderMeasurement::firstOrNew(['om_id' => $orderId]);
                    $resMes->om_x = $dX;
                    $resMes->save();

                }
                if ($code[0] == 'Y') {
                    $dY = $code[1];

                    $resMes = rwOrderMeasurement::firstOrNew(['om_id' => $orderId]);
                    $resMes->om_y = $dY;
                    $resMes->save();

                }
                if ($code[0] == 'Z') {
                    $dZ = $code[1];

                    $resMes = rwOrderMeasurement::firstOrNew(['om_id' => $orderId]);
                    $resMes->om_z = $dZ;
                    $resMes->save();

                }
            } else {
                $Weight = (int)$request->barcode;

                $resMes = rwOrderMeasurement::firstOrNew(['om_id' => $orderId]);
                $resMes->om_weight = $Weight;
                $resMes->save();

            }
        }

        // Отсканирован штрих-код заказа
        if (isset($request->barcode) && $orderId == 0) {

            $barcode = $request->barcode;
            $orderId = 0;

            $orderBarcode = explode('*', $barcode);

            if (isset($orderBarcode[0]) && isset($orderBarcode[1]) && isset($orderBarcode[2]) && $orderBarcode[0] = 101 && ($orderBarcode[0] + $orderBarcode[1] == $orderBarcode[2])) {
                $orderId = $orderBarcode[1];
            }

            $order = rwOrder::where('o_domain_id', $currentUser->domain_id)
//                ->where('o_status_id', 10)
                ->where(function ($q) use ($barcode, $orderId) {
                    $q->where('o_id', $orderId)
                        ->orWhere('o_ext_id', $barcode);
                })
                ->with(['offers', 'getMeasurements', 'getDs.getSource', 'getDs.getDsName', 'offers.offer'])
                ->first();

            if ($order) {
                $dX = $order->getMeasurements->om_x ?? 0;
                $dY = $order->getMeasurements->om_y ?? 0;
                $dZ = $order->getMeasurements->om_z ?? 0;
                $Weight = $order->getMeasurements->om_weight ?? 0;
            }

            if (!$order) {
                Alert::error(CustomTranslator::get('Товар с таким ID не найден!'));
            }

        }

        //  Есть заказ и заданы все габариты. Получаем и печатаем этикету
        if (isset($request->barcode) && $orderId > 0 && $dX > 0 && $dY > 0 && $dZ > 0 && $Weight > 0) {

            $label = new DeliveryServices($dsId);
            $ydOrderId = $label->uploadOrderToDeliveryService($order);

            if ($ydOrderId['status'] == 'ERROR') {

                Alert::error(CustomTranslator::get('Создать заказ в службе доставки не удалось. Служба доставки вернула следующую ошибку: ') . $ydOrderId['message']);

            } else {

                Alert::success(CustomTranslator::get('Заказ выгружен!'));

                // Сохраняем ID
                if ($ydOrderId['id']) {
                    rwOrderDs::where('ods_id', $order->o_id)
                        ->update(
                            [
                                'ods_order_ds_id' => $ydOrderId['id']
                            ]
                        );

                    $dsOrderId = $ydOrderId['id'];

                }

            }

        }

        // *** Получаем этикетку
        if ($dsOrderId != '' &&  $orderId > 0) {

            $currentLabel = $label->getOrderLabel($order, $dsOrderId);

        }

        return [
            'queueId' => $queueId,
            'order' => $order,
            'currentQueues' => $currentQueues,
            'dX' => $dX,
            'dY' => $dY,
            'dZ' => $dZ,
            'Weight' => $Weight,
            'currentLabel' => $currentLabel,
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Маркировка заказа');
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
            Layout::view('Screens.WorkTables.Marking.ScanInput'),
            Layout::view('Screens.WorkTables.Marking.Detail'),
        ];
    }
}
