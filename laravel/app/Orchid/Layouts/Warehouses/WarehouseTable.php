<?php

namespace App\Orchid\Layouts\Warehouses;

use App\Models\rwShop;
use App\Models\rwWarehouse;
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
                ->render(function (rwWarehouse $modelName) {
                    return Link::make($modelName->wh_id)
                        ->route('platform.warehouses.edit',$modelName->wh_id);
                }),

            TD::make('wh_type', 'Тип склада')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(fn ($whList) => $whList->getWhType ?
                    "<b style='background-color: {$whList->getWhType->lwt_bgcolor};
                        padding: 5px;
                        border-radius: 5px;'>
                        {$whList->getWhType->lwt_name}
                        </b>"
                    : '-'),

            TD::make('sh_name', 'Название')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwWarehouse $modelName) {
                    return Link::make($modelName->wh_name)
                        ->route('platform.warehouses.edit',$modelName->wh_id);
                }),

            TD::make('getOwner.name', 'Владелец')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwWarehouse $modelName) {
                    return Link::make($modelName->getOwner->name)
                        ->route('platform.warehouses.edit',$modelName->wh_id);
                }),

            TD::make(__('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (rwWarehouse $modelName) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(__('Ред.'))
                            ->route('platform.warehouses.edit', $modelName->wh_id)
                            ->icon('bs.pencil'),
                    ])),

        ];
    }
}
