<?php

namespace App\Orchid\Layouts\Settings;

use App\Models\rwLibLength;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;

class libLengthTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'lengthsList';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    public function columns(): iterable
    {
        return [
            TD::make('llen_id', 'ID')
                ->sort()
                ->render(function (rwLibLength $modelName) {
                    return Link::make($modelName->llen_id)
                        ->route('platform.settings.length.edit',$modelName->llen_id);
                }),

            TD::make('llen_name', 'Название')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwLibLength $modelName) {
                    return Link::make($modelName->llen_name)
                        ->route('platform.settings.length.edit',$modelName->llen_id);
                }),

            TD::make('llen_unit', 'Код')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwLibLength $modelName) {
                    return Link::make($modelName->llen_unit)
                        ->route('platform.settings.length.edit',$modelName->llen_id);
                }),


            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (rwLibLength $modelName) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(__('Edit'))
                            ->route('platform.settings.length.edit', $modelName->llen_id)
                            ->icon('bs.pencil'),
                    ])),

        ];
    }
}
