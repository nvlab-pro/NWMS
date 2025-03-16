<?php

namespace App\Orchid\Layouts\Shops;

use App\Models\rwShop;
use App\Services\CustomTranslator;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ShopsTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'shopsList';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('sh_id', 'ID')
                ->sort()
                ->render(function (rwShop $modelName) {
                    return Link::make($modelName->sh_id)
                        ->route('platform.shops.edit',$modelName->sh_id);
                }),

            TD::make('sh_name', CustomTranslator::get('Название'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwShop $modelName) {
                    return Link::make($modelName->sh_name)
                        ->route('platform.shops.edit',$modelName->sh_id);
                }),

            TD::make('getOwner.name', CustomTranslator::get('Владелец'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwShop $modelName) {
                    return Link::make($modelName->getOwner->name)
                        ->route('platform.shops.edit',$modelName->sh_id);
                }),

            TD::make(CustomTranslator::get('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (rwShop $modelName) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(CustomTranslator::get('Ред.'))
                            ->route('platform.shops.edit', $modelName->sh_id)
                            ->icon('bs.pencil'),
                    ])),

        ];
    }
}
