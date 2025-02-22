<?php

namespace App\Orchid\Layouts\WhManagement\SingleOrderAssembly;

use App\Models\rwAcceptance;
use App\Models\rwSettingsSoa;
use Carbon\Carbon;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class SOAManagementTable extends Table
{
    protected $target = 'dbWaveAssemblyList';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('ssoa_id', 'ID')
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    return Link::make($modelName->ssoa_id)
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_status_id', __('Статус'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    return '<div onClick="window.location=\'' . route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id) . '\'" style="color: ' . $modelName->getStatus->ls_color . ';
                        background-color: ' . $modelName->getStatus->ls_bgcolor . ';
                        padding: 5px;
                        border-radius: 5px;"><b><nobr>' . $modelName->getStatus->ls_name . '</nobr></b></div>';
                }),

            TD::make('ssoa_priority', __('Приоритет'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    return Link::make($modelName->ssoa_priority)
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_name', __('Название'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    return Link::make($modelName->ssoa_name)
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_wh_id', __('Склад'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    return Link::make($modelName->getWarehouse->wh_name)
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_user_id', __('Пользователь'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {

                    isset($modelName->getUser->name) ? $userName = $modelName->getUser->name : $userName = __('Не указан');

                    return Link::make($userName)
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_date_from', __('Дата с'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    $formattedDate = Carbon::parse($modelName->ssoa_date_from)->format('d.m.Y');
                    return Link::make($formattedDate)
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_date_to', __('Дата по'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    $formattedDate = Carbon::parse($modelName->ssoa_date_to)->format('d.m.Y');
                    return Link::make($formattedDate)
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_offers_count_from', __('Кол-во товаров с'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    return Link::make($modelName->ssoa_offers_count_from ? $modelName->ssoa_offers_count_from : '-')
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_offers_count_to', __('Кол-во товаров по'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    return Link::make($modelName->ssoa_offers_count_to ? $modelName->ssoa_offers_count_to : '-')
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_order_from', __('Заказы с'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    return Link::make($modelName->ssoa_order_from ? $modelName->ssoa_order_from : '-')
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_order_to', __('Заказы по'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    return Link::make($modelName->ssoa_order_to ? $modelName->ssoa_order_to : '-')
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_ds_id', __('Служба доставки'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    $dsName = isset($modelName->getDS->ds_name) ? $modelName->getDS->ds_name : '-';
                    return Link::make($dsName)
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_finish_place_type', __('Место завершения сборки'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    $dsName = isset($modelName->getFinishPlace->pt_name) ? $modelName->getFinishPlace->pt_name : '-';
                    return Link::make($dsName)
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_all_offers', __('Вид сборки'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    $dsName = $modelName->ssoa_all_offers == 0 ? 'Частичная сборка' : 'Все товары';
                    return Link::make($dsName)
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

        ];
    }
}
