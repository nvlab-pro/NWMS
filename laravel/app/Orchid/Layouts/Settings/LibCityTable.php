<?php

namespace App\Orchid\Layouts\Settings;

use App\Models\rwLibCity;
use App\Models\rwLibCountry;
use App\Services\CustomTranslator;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class LibCityTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'cityList';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('lcit_id', 'ID')
                ->sort()
                ->render(function (rwLibCity $modelName) {
                    return Link::make($modelName->lcit_id)
                        ->route('platform.settings.cities.edit', $modelName->lcit_id);
                }),

            TD::make('lcit_name', CustomTranslator::get('Название'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwLibCity $modelName) {
                    return Link::make($modelName->lcit_name)
                        ->route('platform.settings.cities.edit', $modelName->lcit_id);
                }),

            TD::make('getCountry.lco_name', CustomTranslator::get('Страна'))
                ->sort()
                ->render(function (rwLibCity $modelName) {
                    return Link::make($modelName->getCountry->lco_name)
                        ->route('platform.settings.cities.edit',$modelName->lcit_id);
                }),

            TD::make('lcit_coord_latitude', CustomTranslator::get('Широта'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwLibCity $modelName) {
                    return Link::make($modelName->lcit_coord_latitude)
                        ->route('platform.settings.cities.edit', $modelName->lcit_id);
                }),

            TD::make('lcit_coord_longitude', CustomTranslator::get('Долгота'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwLibCity $modelName) {
                    return Link::make($modelName->lcit_coord_longitude)
                        ->route('platform.settings.cities.edit', $modelName->lcit_id);
                }),


            TD::make(CustomTranslator::get(CustomTranslator::get('Actions')))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (rwLibCity $modelName) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(CustomTranslator::get('Edit'))
                            ->route('platform.settings.cities.edit', $modelName->lcit_id)
                            ->icon('bs.pencil'),
                    ])),

        ];
    }
}
