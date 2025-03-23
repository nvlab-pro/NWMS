<?php

namespace App\Orchid\Layouts\ImportsList;

use App\Services\CustomTranslator;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;

class ImportsListTable extends Table
{
    protected $target = 'imports'; // Ключ массива из метода query()

    protected function columns(): array
    {
        return [
            TD::make('id', 'ID')
                ->render(function ($import) {
                    return Link::make((string)$import->id)
                        ->route('platform.whmanagement.import.details', ['importId' => $import->id]);
                }),

            TD::make('status', CustomTranslator::get('Статус'))
                ->render(fn ($item) => Link::make(match ((int)$item->status) {
                    0 => CustomTranslator::get('Загружено'),
                    1 => CustomTranslator::get('Обрабатывается'),
                    2 => CustomTranslator::get('Импорт окончен'),
                    3 => CustomTranslator::get('Ошибка'),
                    default => '—',
                })->route('platform.whmanagement.import.details', ['importId' => $item->id])),

            TD::make('group', CustomTranslator::get('Группа'))
                ->render(fn ($item) => Link::make($item->group)
                    ->route('platform.whmanagement.import.details', ['importId' => $item->id])),

            TD::make('import_type', CustomTranslator::get('Тип импорта'))
                ->render(fn ($item) => Link::make($item->import_type == 1
                    ? CustomTranslator::get('Отложенный')
                    : CustomTranslator::get('Немедленный')
                )->route('platform.whmanagement.import.details', ['importId' => $item->id])),

            TD::make('original_name', CustomTranslator::get('Оригинальное имя файла'))
                ->render(fn ($item) => Link::make($item->original_name)
                    ->route('platform.whmanagement.import.details', ['importId' => $item->id])),

            TD::make('created_at', CustomTranslator::get('Дата загрузки'))
                ->render(fn ($item) => Link::make($item->created_at?->format('d.m.Y H:i:s'))
                    ->route('platform.whmanagement.import.details', ['importId' => $item->id])),

            // 🔧 Кнопка редактирования
            TD::make('edit', '')
                ->alignRight()
                ->render(fn ($item) =>
                Link::make('')
                    ->icon('chat-right-text')
                    ->route('platform.whmanagement.import.details', ['importId' => $item->id])
                    ->class('btn btn-sm btn-outline-secondary')
                ),
        ];
    }
}

