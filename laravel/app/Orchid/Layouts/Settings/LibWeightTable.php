<?php

namespace App\Orchid\Layouts\Settings;

use App\Models\rwLibWeight;
use App\Services\CustomTranslator;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class LibWeightTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'weightList';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('lw_id', 'ID')
                ->sort()
                ->render(function (rwLibWeight $modelName) {
                    return Link::make($modelName->lw_id)
                        ->route('platform.settings.weight.edit',$modelName->lw_id);
                }),

            TD::make('lw_name', CustomTranslator::get('Название'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwLibWeight $modelName) {
                    return Link::make($modelName->lw_name)
                        ->route('platform.settings.weight.edit',$modelName->lw_id);
                }),

            TD::make('lw_unit', CustomTranslator::get('Код'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwLibWeight $modelName) {
                    return Link::make($modelName->lw_unit)
                        ->route('platform.settings.weight.edit',$modelName->lw_id);
                }),


            TD::make(CustomTranslator::get('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (rwLibWeight $modelName) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(CustomTranslator::get('Edit'))
                            ->route('platform.settings.weight.edit', $modelName->lw_id)
                            ->icon('bs.pencil'),
                    ])),

        ];
    }
}
