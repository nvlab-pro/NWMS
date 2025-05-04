<?php

namespace App\Orchid\Screens\Offers;

use App\Models\rwAcceptance;
use App\Models\rwOffer;
use App\Models\rwOrder;
use App\Models\rwOrderOffer;
use App\Models\rwPlace;
use App\Models\rwWarehouse;
use App\Services\CustomTranslator;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;
use App\WhCore\WhCore;
use Orchid\Screen\Screen;

class TurnoverScreen extends Screen
{
    private $whName, $offerName, $whId, $offerId;

    public function query($whId, $offerId): iterable
    {

        $currentCore = new WhCore($whId);
        $dbOffersTurnover = $currentCore->getDocumentOfferTurnover($offerId);

        $dbWh = rwWarehouse::where('wh_id', $whId)->first();
        $dbOffer = rwOffer::where('of_id', $offerId)->first();

        $this->whName = $dbWh->wh_name;
        $this->offerName = $dbOffer->of_name;

        $this->whId = $whId;
        $this->offerId = $offerId;

        $docStatus = [];
        $docPlace = [];

        foreach ($dbOffersTurnover as $item) {

            $docStatus[$item->whci_id] = '-';
            $docPlace[$item->whci_id] = '';
            $docPlace2[$item->whci_id] = '';
            $docReserv[$item->whci_id] = '';

            // Выясняем статус приемки
            if ($item->whci_doc_type == 1) {

                $dbAcceptanceStatus = rwAcceptance::where('acc_id', $item->whci_doc_id)
                    ->with('getAccStatus')
                    ->first();

                if (isset($dbAcceptanceStatus->getAccStatus->las_name))
                    $docStatus[$item->whci_id] = '<div style="color: '.$dbAcceptanceStatus->getAccStatus->las_color.'; background-color: '.$dbAcceptanceStatus->getAccStatus->las_bgcolor.'; border-radius: 5px; padding: 3px;"><b>'.CustomTranslator::get($dbAcceptanceStatus->getAccStatus->las_name).'</b></div>';
            }
                // Выясняем статус заказа
            if ($item->whci_doc_type == 2) {

                $dbOrderStatus = rwOrder::where('o_id', $item->whci_doc_id)
                    ->with('getStatus')
                    ->first();

                if (isset($dbOrderStatus->getStatus->os_name))
                    $docStatus[$item->whci_id] ='<div style="color: '.$dbOrderStatus->getStatus->os_color.'; background-color: '.$dbOrderStatus->getStatus->os_bgcolor.'; border-radius: 5px; padding: 3px;"><b>'.CustomTranslator::get($dbOrderStatus->getStatus->os_name).'</b></div>';

                if (isset($dbOrderStatus->o_order_place) && $dbOrderStatus->o_order_place > 0) {
                    $dbPlace = rwPlace::with('getType')
                        ->where('pl_id', $dbOrderStatus->o_order_place)
                        ->first();

                    if (isset($dbPlace->pl_id)) {

                        $docPlace2[$item->whci_id] = '';

                        $docPlace2[$item->whci_id] .= $dbPlace->pl_room;
                        if (strlen($dbPlace->pl_floor) > 0) $docPlace2[$item->whci_id] .= ' | ' . $dbPlace->pl_floor;
                        if (strlen($dbPlace->pl_section) > 0) $docPlace2[$item->whci_id] .= ' | ' . $dbPlace->pl_section;
                        if ($dbPlace->pl_row > 0) $docPlace2[$item->whci_id] .= ' | ' . $dbPlace->pl_row;
                        if ($dbPlace->pl_rack > 0) $docPlace2[$item->whci_id] .= ' | ' . $dbPlace->pl_rack;
                        if ($dbPlace->pl_shelf > 0) $docPlace2[$item->whci_id] .= $dbPlace->pl_shelf;

                        $docPlace2[$item->whci_id] .= '<br><span style="color: #999999; font-size: 10px;">'. $dbPlace->getType->pt_name.'</span>';

                        $docPlace2[$item->whci_id] .= '';

                    }
                }

            }

            // Берем место хранения
            if ($item->whci_place_id > 0) {

                $dbPlace = rwPlace::with('getType')
                    ->where('pl_id', $item->whci_place_id)
                    ->first();

                if (isset($dbPlace->pl_id)) {

                    $docPlace[$item->whci_id] = '<nobr>';

                    $docPlace[$item->whci_id] .= $dbPlace->pl_room;
                    if (strlen($dbPlace->pl_floor) > 0) $docPlace[$item->whci_id] .= ' | ' . $dbPlace->pl_floor;
                    if (strlen($dbPlace->pl_section) > 0) $docPlace[$item->whci_id] .= ' | ' . $dbPlace->pl_section;
                    if ($dbPlace->pl_row > 0) $docPlace[$item->whci_id] .= ' | ' . $dbPlace->pl_row;
                    if ($dbPlace->pl_rack > 0) $docPlace[$item->whci_id] .= ' | ' . $dbPlace->pl_rack;
                    if ($dbPlace->pl_shelf > 0) $docPlace[$item->whci_id] .= ' | ' . $dbPlace->pl_shelf;

                    $docPlace[$item->whci_id] .= '</nobr><br><span style="color: #999999; font-size: 10px;">'. CustomTranslator::get($dbPlace->getType->pt_name).'</span>';

                    $docPlace[$item->whci_id] .= '';

                }

            }

        }

        return [
            'dbOffersTurnover' => $dbOffersTurnover,
            'whId' => $whId,
            'offerId' => $offerId,
            'docStatus' => $docStatus,
            'docPlace' => $docPlace,
            'docPlace2' => $docPlace2,
            'dbWh' => $dbWh,
        ];
    }

    public function name(): string
    {
        return  $this->offerName;
    }

    public function description(): ?string
    {
        return CustomTranslator::get('Склад') . ': ' . $this->whName;
    }

    public function commandBar(): iterable
    {
        return [

            Link::make(CustomTranslator::get('Экспорт в Excel'))
                ->route('platform.offers.turnover.export', [$this->whId, $this->offerId]) // 🔥 передаём фильтры из текущего запроса
                ->icon('bs.cloud-download'),

        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
//            TurnoverTable::class,
            Layout::view('Offers.OfferTurnoverList'),
        ];
    }
}
