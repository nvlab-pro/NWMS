<?php

namespace App\Orchid\Layouts\WhManagement\MarkingSettings;

use App\Models\rwSettingsMarking;
use App\Models\rwSettingsProcPacking;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\ModalToggle;
use App\Services\CustomTranslator;

class MarkingSettingsTable extends Table
{
    /**
     * The data source key from the Screen's query() method.
     */
    protected $target = 'dbMarkingList';

    /**
     * Define the table columns.
     */
    protected function columns(): iterable
    {
        return [
            TD::make('sm_id', 'ID')
                ->render(fn($sm) => $sm->sm_id),

            TD::make('sm_name', CustomTranslator::get('Название'))
                ->render(fn($sm) => $sm->sm_name),

            TD::make('sm_priority', CustomTranslator::get('Приоритет'))
                ->render(fn($sm) => $sm->sm_priority),

            TD::make('sm_status_id', CustomTranslator::get('Статус'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsMarking $modelName) {
                    return '<div style="color: ' . $modelName->getStatus->ls_color . ';
                        background-color: ' . $modelName->getStatus->ls_bgcolor . ';
                        padding: 5px;
                        border-radius: 5px;"><b><nobr>' . CustomTranslator::get($modelName->getStatus->ls_name) . '</nobr></b></div>';
                }),

            TD::make('sm_user_id', CustomTranslator::get('Пользователь'))
                ->render(fn($sm) => $sm->getRelation('getUser')
                    ? $sm->getUser->name
                    : CustomTranslator::get('Не выбрано')
                ),

            TD::make('sm_ds_id', CustomTranslator::get('Служба доставки'))
                ->render(function (rwSettingsMarking $modelName) {
                    return '<nobr>' . CustomTranslator::get($modelName->getDS->ds_name) . '</nobr>';
                }),

            TD::make('sm_date_from', CustomTranslator::get('Дата от'))
                ->render(fn($sm) => $sm->sm_date_from ? $sm->sm_date_from->format('Y-m-d') : '-'),

            TD::make('sm_date_to', CustomTranslator::get('Дата до'))
                ->render(fn($sm) => $sm->sm_date_to ? $sm->sm_date_to->format('Y-m-d') : '-'),

            TD::make('created_at', CustomTranslator::get('Дата создания'))
                ->render(fn($sm) => $sm->created_at ? $sm->created_at->format('Y-m-d H:i') : '-'),

            TD::make('updated_at', CustomTranslator::get('Дата обновления'))
                ->render(fn($sm) => $sm->updated_at ? $sm->updated_at->format('Y-m-d H:i') : '-'),

            // Actions column: Edit button (opens edit modal)
            TD::make('actions', '')
                ->align(TD::ALIGN_RIGHT)
                ->render(function ($sm) {
                    return ModalToggle::make(CustomTranslator::get('Редактировать'))
                        ->icon('bs.pencil')
                        ->modal('editMarkingModal')
                        ->modalTitle(CustomTranslator::get('Редактировать запись') . ' #' . $sm->sm_id)
                        ->method('updateMarking')
                        ->asyncParameters([
                            'smId' => $sm->sm_id,
                        ]);
                }),
        ];
    }
}
