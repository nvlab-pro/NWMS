<?php

namespace App\Orchid\Layouts\ImportsList;

use App\Services\CustomTranslator;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Layouts\Table;

class ImportDetailsTable extends Table
{
    protected $target = 'logs';

    protected function columns(): array
    {
        return [
            TD::make('il_date', CustomTranslator::get('Дата'))->render(
                fn ($log) => \Carbon\Carbon::parse($log->il_date)->format('d.m.Y H:i:s')
            ),

            TD::make('il_operation', CustomTranslator::get('Операция'))->render(fn ($log) => match ((int)$log->il_operation) {
                1 => CustomTranslator::get('Создание'),
                2 => CustomTranslator::get('Обновление'),
                3 => CustomTranslator::get('Ошибка'),
                default => CustomTranslator::get('Неизвестно'),
            }),

            TD::make('il_name', CustomTranslator::get('Описание')),

            TD::make('il_fields', CustomTranslator::get('Изменения'))->render(fn ($log) =>
            ModalToggle::make(CustomTranslator::get('Показать'))
                ->modal('FieldsModal')
                ->modalTitle(CustomTranslator::get('Изменённые поля'))
                ->asyncParameters([
                    'log_id' => $log->il_id,
                ])
            ),
        ];
    }
}

