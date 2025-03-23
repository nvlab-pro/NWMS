<?php

namespace App\Orchid\Screens\ImportsList;

use App\Models\rwImportLog;
use App\Services\CustomTranslator;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use App\Orchid\Layouts\ImportsList\ImportDetailsTable;
use Orchid\Support\Facades\Alert;

class ImportDetailsScreen extends Screen
{
    public $importId;

    public function query(int $importId): array
    {
        $this->importId = $importId;

        return [
            'logs' => rwImportLog::where('il_import_id', $importId)
                ->orderByDesc('il_date')
                ->get(),
        ];
    }

    public function name(): string
    {
        return CustomTranslator::get('Детали импорта');
    }

    public function commandBar(): array
    {
        return [];
    }

    public function layout(): array
    {
        return [
            ImportDetailsTable::class,

            Layout::modal('FieldsModal', [
                Layout::view('Screens.ImportsList.import-fields-preview'),
            ])->async('fields')
                ->withoutApplyButton(),
        ];
    }

    // async-загрузка данных в модалку
    public function asyncFields(int $log_id): array
    {
        $log = \App\Models\rwImportLog::find($log_id);

        return [
            'fields' => $log?->il_fields ?? [],
        ];
    }
}

