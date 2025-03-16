<?php

namespace App\Orchid\Layouts\WhManagement\SingleOrderAssembly;

use App\Models\rwAcceptance;
use App\Models\rwSettingsSoa;
use App\Services\CustomTranslator;
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

            TD::make('ssoa_status_id', CustomTranslator::get('Статус'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    return '<div onClick="window.location=\'' . route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id) . '\'" style="color: ' . $modelName->getStatus->ls_color . ';
                        background-color: ' . $modelName->getStatus->ls_bgcolor . ';
                        padding: 5px;
                        border-radius: 5px;"><b><nobr>' . CustomTranslator::get($modelName->getStatus->ls_name) . '</nobr></b></div>';
                }),

            TD::make('ssoa_priority', CustomTranslator::get('Приоритет'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    return Link::make($modelName->ssoa_priority)
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_name', CustomTranslator::get('Название'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    return Link::make($modelName->ssoa_name)
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_wh_id', CustomTranslator::get('Склад'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    return Link::make($modelName->getWarehouse->wh_name)
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_user_id', CustomTranslator::get('Пользователь'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {

                    isset($modelName->getUser->name) ? $userName = $modelName->getUser->name : $userName = CustomTranslator::get('Не указан');

                    return Link::make($userName)
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_date_from', CustomTranslator::get('Дата с'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    $formattedDate = Carbon::parse($modelName->ssoa_date_from)->format('d.m.Y');
                    return Link::make($formattedDate)
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_date_to', CustomTranslator::get('Дата по'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    $formattedDate = Carbon::parse($modelName->ssoa_date_to)->format('d.m.Y');
                    return Link::make($formattedDate)
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_offers_count_from', CustomTranslator::get('Кол-во товаров с'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    return Link::make($modelName->ssoa_offers_count_from ? $modelName->ssoa_offers_count_from : '-')
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_offers_count_to', CustomTranslator::get('Кол-во товаров по'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    return Link::make($modelName->ssoa_offers_count_to ? $modelName->ssoa_offers_count_to : '-')
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_order_from', CustomTranslator::get('Заказы с'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    return Link::make($modelName->ssoa_order_from ? $modelName->ssoa_order_from : '-')
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_order_to', CustomTranslator::get('Заказы по'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    return Link::make($modelName->ssoa_order_to ? $modelName->ssoa_order_to : '-')
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_ds_id', CustomTranslator::get('Служба доставки'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    $dsName = isset($modelName->getDS->ds_name) ? $modelName->getDS->ds_name : '-';
                    return Link::make($dsName)
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_finish_place_type', CustomTranslator::get('Место завершения сборки'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    $dsName = isset($modelName->getFinishPlace->pt_name) ? $modelName->getFinishPlace->pt_name : '-';
                    return Link::make($dsName)
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

            TD::make('ssoa_all_offers', CustomTranslator::get('Вид сборки'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwSettingsSoa $modelName) {
                    $dsName = $modelName->ssoa_all_offers == 0 ? CustomTranslator::get('Частичная сборка') : CustomTranslator::get('Все товары');
                    return Link::make($dsName)
                        ->route('platform.whmanagement.single-order-assembly.edit', $modelName->ssoa_id);
                }),

        ];
    }
}
