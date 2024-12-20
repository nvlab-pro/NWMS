<?php

namespace App\Orchid\Layouts\Settings;

use App\Models\rwLibCountry;
use App\Models\rwLibLength;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class libCountryTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'countryList';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('lco_id', 'ID')
                ->sort()
                ->render(function (rwLibCountry $modelName) {
                    return Link::make($modelName->lco_id)
                        ->route('platform.settings.countries.edit', $modelName->lco_id);
                }),

            TD::make('lco_name', 'Название')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwLibCountry $modelName) {
                    return Link::make($modelName->lco_name)
                        ->route('platform.settings.countries.edit', $modelName->lco_id);
                }),

            TD::make('lco_code', 'Код')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwLibCountry $modelName) {
                    return Link::make($modelName->lco_code)
                        ->route('platform.settings.countries.edit', $modelName->lco_id);
                }),

            TD::make('getCurrency.lcur_name', __('Валюта'))
                ->sort()
                ->render(function (rwLibCountry $modelName) {
                    return Link::make($modelName->getCurrency->lcur_name)
                        ->route('platform.settings.countries.edit', $modelName->lco_id);
                }),

            TD::make('getLanguage.llang_name', __('Язык'))
                ->sort()
                ->render(function (rwLibCountry $modelName) {
                    return Link::make($modelName->getLanguage->llang_name)
                        ->route('platform.settings.countries.edit', $modelName->lco_id);
                }),

            TD::make('getWeight.lw_name', __('Мера веса'))
                ->sort()
                ->render(function (rwLibCountry $modelName) {
                    return Link::make($modelName->getWeight->lw_name)
                        ->route('platform.settings.countries.edit', $modelName->lco_id);
                }),

            TD::make('getLength.llen_name', __('Мера длины'))
                ->sort()
                ->render(function (rwLibCountry $modelName) {
                    return Link::make($modelName->getLength->llen_name)
                        ->route('platform.settings.countries.edit', $modelName->lco_id);
                }),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (rwLibCountry $modelName) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(__('Edit'))
                            ->route('platform.settings.countries.edit', $modelName->lco_id)
                            ->icon('bs.pencil'),
                    ])),

        ];
    }
}
