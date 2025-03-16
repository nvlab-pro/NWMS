<?php

namespace App\Orchid\Layouts\Warehouses;

use App\Models\rwShop;
use App\Models\rwWarehouse;
use App\Services\CustomTranslator;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class WarehouseTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'whList';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('wh_id', 'ID')
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->render(function (rwWarehouse $modelName) {
                    return Link::make($modelName->wh_id)
                        ->route('platform.warehouses.edit',$modelName->wh_id);
                }),

            TD::make('wh_type', CustomTranslator::get('Тип склада'))
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->filter(TD::FILTER_TEXT)
                ->render(fn ($whList) => $whList->getWhType ?
                    "<b style='background-color: {$whList->getWhType->lwt_bgcolor};
                        padding: 5px;
                        border-radius: 5px;'>
                        ".CustomTranslator::get($whList->getWhType->lwt_name)."
                        </b>"
                    : '-'),

            TD::make('sh_name', CustomTranslator::get('Название'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwWarehouse $modelName) {
                    return Link::make($modelName->wh_name)
                        ->route('platform.warehouses.edit',$modelName->wh_id);
                }),

            TD::make('getOwner.name', CustomTranslator::get('Владелец'))
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwWarehouse $modelName) {
                    return Link::make($modelName->getOwner->name)
                        ->route('platform.warehouses.edit',$modelName->wh_id);
                }),

            TD::make('getParent.wh_name', CustomTranslator::get('Родитель'))
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwWarehouse $modelName) {
                    if (isset($modelName->getParent->wh_name))
                        return Link::make($modelName->getParent->wh_name)
                            ->route('platform.warehouses.edit',$modelName->wh_id);
                    else return '-';
                }),

            TD::make('getDomain.dm_name', CustomTranslator::get('Домен'))
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwWarehouse $modelName) {
                    if ($modelName->getDomain) {
                        return Link::make($modelName->getDomain->dm_name)
                            ->route('platform.warehouses.edit',$modelName->wh_id);
                    } else {
                        return '-';
                    }
                }),

            TD::make(CustomTranslator::get('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (rwWarehouse $modelName) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(CustomTranslator::get('Ред.'))
                            ->route('platform.warehouses.edit', $modelName->wh_id)
                            ->icon('bs.pencil'),
                    ])),

        ];
    }
}
