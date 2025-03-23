<?php

namespace App\Orchid\Screens\ImportsList;

use App\Services\CustomTranslator;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use App\Orchid\Layouts\ImportsList\ImportsListTable;
use Orchid\Screen\Actions\Link;
use Illuminate\Support\Facades\Auth;

class ImportsListScreen extends Screen
{
    public function name(): string
    {
        return CustomTranslator::get('Список импортов') ;
    }

    public function query(): array
    {
        $user = Auth::user();

        $imports = Attachment::query()
            ->where('type', 'импорт')
            ->orderByDesc('created_at')
            ->paginate();

        return [
            'imports' => $imports,
        ];
    }

    public function commandBar(): array
    {
        return [];
    }

    public function layout(): array
    {
        return [
            Layout::legend('imports', [
                // Можно добавить счетчики, итоги, фильтры и т.д.
            ]),
            ImportsListTable::class,
        ];
    }
}
