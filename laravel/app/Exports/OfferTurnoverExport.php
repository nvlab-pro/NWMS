<?php

namespace App\Exports;

use App\Models\rwAcceptance;
use App\Models\rwOffer;
use App\Models\rwOrder;
use App\Models\rwPlace;
use App\Models\rwWarehouse;
use App\Services\CustomTranslator;
use App\WhCore\WhCore;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OfferTurnoverExport implements FromCollection, WithHeadings, WithColumnWidths
{
    use Exportable;

    protected $whId, $offerId;

    public function __construct($whId, $offerId)
    {
        $this->whId = $whId;
        $this->offerId = $offerId;
    }

    public function collection()
    {
        $currentCore = new WhCore($this->whId);
        $dbOffersTurnover = $currentCore->getDocumentOfferTurnover($this->offerId);

        $docStatus = [];
        $docPlace = [];
        $docPlace2 = [];
        $docReserv = [];
        $docTotal = [];
        $total = 0;

        foreach ($dbOffersTurnover as $item) {
            $docStatus[$item->whci_id] = '-';
            $docPlace[$item->whci_id] = '';
            $docPlace2[$item->whci_id] = '';
            $docReserv[$item->whci_id] = '';

            // Статус документа
            if ($item->whci_doc_type == 1) {
                $status = rwAcceptance::where('acc_id', $item->whci_doc_id)->with('getAccStatus')->first();
                $docStatus[$item->whci_id] = $status->getAccStatus->las_name ?? '-';
            } elseif ($item->whci_doc_type == 2) {
                $status = rwOrder::where('o_id', $item->whci_doc_id)->with('getStatus')->first();
                $docStatus[$item->whci_id] = $status->getStatus->os_name ?? '-';

                if (!empty($status->o_order_place)) {
                    $place = rwPlace::with('getType')->find($status->o_order_place);
                    $docPlace2[$item->whci_id] = $this->formatPlace($place);
                }
            }

            // Основное место хранения
            if (!empty($item->whci_place_id)) {
                $place = rwPlace::with('getType')->find($item->whci_place_id);
                $docPlace[$item->whci_id] = $this->formatPlace($place);
            }

            $total += $item->whci_count;

            $docTotal[$item->whci_id] = $total;
        }

        $num = 0;

        return $dbOffersTurnover->map(function ($item) use (&$num, $docStatus, $docPlace, $docPlace2, $docTotal) {
            $num++;

            $typeName = CustomTranslator::get('Приемка');
            if ($item->whci_doc_type == 2) {
                $typeName = CustomTranslator::get('Отгрузка');
            }

            return [
                $num,
                $item->whci_reserved ?? '',
                $this->formatDateTime($item->whci_date),
                $typeName,
                $item->whci_doc_id ?? '',
                $docStatus[$item->whci_id] ?? '-',
                $this->formatDate($item->whci_production_date),
                $this->formatDate($item->whci_expiration_date),
                $item->whci_batch ?? '',
                $docPlace[$item->whci_id] ?? '',
                $item->whci_count ?? '0',
                $docTotal[$item->whci_id] ?? '',
            ];
        });
    }

    protected function formatPlace($place): string
    {
        if (!$place) return '';

        $parts = array_filter([
            $place->pl_room,
            $place->pl_floor,
            $place->pl_section,
            $place->pl_row > 0 ? $place->pl_row : null,
            $place->pl_rack > 0 ? $place->pl_rack : null,
            $place->pl_shelf > 0 ? $place->pl_shelf : null,
        ]);

        return implode(' | ', $parts) . ($place->getType->pt_name ? " ({$place->getType->pt_name})" : '');
    }

    protected function formatDateTime($value): string
    {
        if (!$value) {
            return '';
        }

        try {
            if ($value == '0000-00-00 00:00:00') {
                return '00.00.0000 00:00:00';
            } else {
                return \Carbon\Carbon::parse($value)->format('d.m.Y H:i:s');
            }
        } catch (\Exception $e) {
            return '';
        }
    }

    protected function formatDate($value): string
    {
        if (!$value) {
            return '';
        }

        try {
            return \Carbon\Carbon::parse($value)->format('d.m.Y');
        } catch (\Exception $e) {
            return '';
        }
    }

    public function headings(): array
    {
        return [
            CustomTranslator::get('№'),
            CustomTranslator::get('Резерв'),
            CustomTranslator::get('Дата'),
            CustomTranslator::get('Тип'),
            CustomTranslator::get('№ документа'),
            CustomTranslator::get('Статус'),
            CustomTranslator::get('Дата производства'),
            CustomTranslator::get('Срок годности'),
            CustomTranslator::get('Партия'),
            CustomTranslator::get('Место'),
            CustomTranslator::get('Количество'),
            CustomTranslator::get('Итого'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8, 'B' => 15, 'C' => 25, 'D' => 15, 'E' => 12,
            'F' => 35, 'G' => 10, 'H' => 10, 'I' => 10, 'J' => 50,
            'K' => 10, 'L' => 10,
        ];
    }
}
