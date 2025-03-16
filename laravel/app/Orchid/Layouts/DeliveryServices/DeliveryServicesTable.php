<?php

namespace App\Orchid\Layouts\DeliveryServices;

use App\Models\rwDeliveryService;
use App\Services\CustomTranslator;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class DeliveryServicesTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'dsList';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('ds_id', 'ID')
                ->sort()
                ->render(function (rwDeliveryService $modelName) {
                    return Link::make($modelName->ds_id)
                        ->route('platform.delivery-services.edit',$modelName->ds_id);
                }),

            TD::make('ds_name', CustomTranslator::get('Название'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwDeliveryService $modelName) {
                    return Link::make($modelName->ds_name)
                        ->route('platform.delivery-services.edit',$modelName->ds_id);
                }),

            TD::make(CustomTranslator::get('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (rwDeliveryService $modelName) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(CustomTranslator::get('Ред.'))
                            ->route('platform.delivery-services.edit', $modelName->ds_id)
                            ->icon('bs.pencil'),
                    ])),

        ];
    }
}
