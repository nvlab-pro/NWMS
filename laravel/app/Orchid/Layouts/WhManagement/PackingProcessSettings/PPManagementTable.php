<?php

namespace App\Orchid\Layouts\WhManagement\PackingProcessSettings;

use App\Models\rwSettingsProcPacking;
use Carbon\Carbon;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class PPManagementTable extends Table
{
    protected $target = 'dbSettingsList';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('spp_id', 'ID')
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsProcPacking $modelName) {
                    return Link::make($modelName->spp_id)
                        ->route('platform.whmanagement.packing-process-settings.edit', $modelName->spp_id);
                }),

            TD::make('spp_status_id', __('Статус'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsProcPacking $modelName) {
                    return '<div onClick="window.location=\'' . route('platform.whmanagement.packing-process-settings.edit', $modelName->spp_id) . '\'" style="color: ' . $modelName->getStatus->ls_color . ';
                        background-color: ' . $modelName->getStatus->ls_bgcolor . ';
                        padding: 5px;
                        border-radius: 5px;"><b><nobr>' . $modelName->getStatus->ls_name . '</nobr></b></div>';
                }),

            TD::make('spp_priority', __('Приоритет'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsProcPacking $modelName) {
                    return Link::make($modelName->spp_priority)
                        ->route('platform.whmanagement.packing-process-settings.edit', $modelName->spp_id);
                }),

            TD::make('spp_name', __('Название'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsProcPacking $modelName) {
                    return Link::make($modelName->spp_name)
                        ->route('platform.whmanagement.packing-process-settings.edit', $modelName->spp_id);
                }),

            TD::make('spp_wh_id', __('Склад'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsProcPacking $modelName) {
                    return Link::make($modelName->getWarehouse->wh_name)
                        ->route('platform.whmanagement.packing-process-settings.edit', $modelName->spp_id);
                }),

            TD::make('spp_user_id', __('Пользователь'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsProcPacking $modelName) {
                    isset($modelName->getUser->name) ? $userName = $modelName->getUser->name : $userName = __('Не указан');
                    return Link::make($userName)
                        ->route('platform.whmanagement.packing-process-settings.edit', $modelName->spp_id);
                }),
            TD::make('spp_start_place_type', __('Место начала упаковки'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsProcPacking $modelName) {
                    $dsName = isset($modelName->getStartPlace->pt_name) ? $modelName->getStartPlace->pt_name : '-';
                    return Link::make($dsName)
                        ->route('platform.whmanagement.packing-process-settings.edit', $modelName->spp_id);
                }),
            TD::make('spp_packing_type', __('Тип пикинга'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsProcPacking $modelName) {
                    $type = "";
                    if($modelName->spp_packing_type == 0) $type = __('Скан артикула (под пересчет)');
                    if($modelName->spp_packing_type == 1) $type = __('Скан каждого товара');
                    if($modelName->spp_packing_type == 2) $type = __('Со сканом честного знака');
                    return Link::make($type)
                        ->route('platform.whmanagement.packing-process-settings.edit', $modelName->spp_id);
                }),

            TD::make('spp_ds_id', __('Служба доставки'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsProcPacking $modelName) {
                    $dsName = isset($modelName->getDS->ds_name) ? $modelName->getDS->ds_name : '-';
                    return Link::make($dsName)
                        ->route('platform.whmanagement.packing-process-settings.edit', $modelName->spp_id);
                }),
        ];
    }
}