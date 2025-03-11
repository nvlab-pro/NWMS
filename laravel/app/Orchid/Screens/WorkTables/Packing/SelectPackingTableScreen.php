<?php

namespace App\Orchid\Screens\WorkTables\Packing;

use App\Models\rwOrder;
use App\Models\rwOrderOffer;
use App\Models\rwOrderPacking;
use App\Models\rwPlace;
use App\Models\rwSettingsProcPacking;
use App\Orchid\Services\PackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class SelectPackingTableScreen extends Screen
{
    public function screenBaseView(): string
    {
        return 'loyouts.base';

    }

    protected $queueName, $queueId;

    public function query($queueId, Request $request): iterable
    {
        $currentUser = Auth::user();
        $this->queueId = $queueId;
        $arOrdersList = $arBusyTables = [];
        $currentOrder = 0;

        // Закрываем заказ
        if (isset($request->action) && $request->action == 'finishOrder') {

            rwOrder::where('o_id', $request->orderId)
                ->update([
                    'o_status_id'  => 100,
                ]);

            Alert::success(__('Заказ № ') . $request->orderId . __(' упакован!'));

        }

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager') || $currentUser->hasRole('warehouse_worker')) {

            // Получаем список столов упаковки
            $dbTablesList = rwPlace::where('pl_domain_id', $currentUser->domain_id)
                ->where('pl_wh_id', $currentUser->wh_id)
                ->where('pl_type', 105)
                ->get();

            foreach($dbTablesList as $curretTable) {

                $arOrdersList[$curretTable->pl_id] = PackingService::getOrdersRedyToPacking($queueId, $curretTable->pl_id);
                $arBusyTables[$curretTable->pl_id] = PackingService::checkPackingTable($curretTable->pl_id);

            }

            // Получаем настройки очереди
            $currentQueue = rwSettingsProcPacking::where('spp_id', $queueId)
                ->first();

            if ($currentQueue) {

                // Получаем данные очереди
                $queueWhId = $currentQueue->spp_wh_id;
                $this->queueName = $currentQueue->spp_name;
                $queueStartPlaceType = $currentQueue->spp_start_place_type;
                $queueRackFrom = $currentQueue->spp_place_rack_from;
                $queueRackTo = $currentQueue->spp_place_rack_to;
                $queuePackType = $currentQueue->spp_packing_type;

            }

            if (isset($request->tableId)) {

                // Выясняем не собирается ли уже на этом столе заказ
                $currentOrder = PackingService::getCurrentOrderIdFromTable($request->tableId);

                if (!$currentOrder) {

                    // Начинаем сборку заказа
                    $currentOrder = array_key_first($arOrdersList[$request->tableId]);

                    if ($currentOrder > 0) {

                        rwOrder::where('o_id', $currentOrder)
                            ->update([
                                'o_status_id'  => 90,
                                'o_order_place' => $request->tableId,
                                'o_operation_user_id' => $currentUser->id,
                            ]);

                        rwOrderOffer::where('o_order_id', $currentOrder)
                            ->update([
                                'oo_operation_user_id'  => 0,
                            ]);

                        rwOrderPacking::where('op_order_id', $currentOrder)->delete();

                    }

                }
            }
        }

        return [
            'queueId' => $queueId,
            'dbTablesList' => $dbTablesList,
            'arOrdersList' => $arOrdersList,
            'currentOrder' => $currentOrder,
            'currentUser'  => $currentUser,
            'arBusyTables' => $arBusyTables,
            'tableId'      => $request->tableId,
        ];
    }

    public function name(): ?string
    {
        return __('Выбор стола упаковки');
    }

    public function description(): ?string
    {
        return __('Очередь упаковки') . ': ' . $this->queueName . ' (' . $this->queueId . ')';
    }

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
//            Layout::view('Screens.WorkTables.Packing.ScanInput'),
            Layout::view('Screens.WorkTables.Packing.TablesList'),

        ];
    }
}
