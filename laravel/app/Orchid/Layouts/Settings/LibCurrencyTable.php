<?php

namespace App\Orchid\Layouts\Settings;

use App\Models\rwLibCurrency;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class libCurrencyTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'currencyList';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('lcur_id', 'ID')
                ->sort()
                ->render(function (rwLibCurrency $modelName) {
                    return Link::make($modelName->lcur_id)
                        ->route('platform.settings.currencies.edit',$modelName->lcur_id);
                }),

            TD::make('lcur_name', 'Название')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwLibCurrency $modelName) {
                    return Link::make($modelName->lcur_name)
                        ->route('platform.settings.currencies.edit',$modelName->lcur_id);
                }),

            TD::make('lcur_code', 'Код')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwLibCurrency $modelName) {
                    return Link::make($modelName->lcur_code)
                        ->route('platform.settings.currencies.edit',$modelName->lcur_id);
                }),

            TD::make('lcur_symbol', 'Код')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwLibCurrency $modelName) {
                    return Link::make($modelName->lcur_symbol)
                        ->route('platform.settings.currencies.edit',$modelName->lcur_id);
                }),


            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (rwLibCurrency $modelName) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(__('Edit'))
                            ->route('platform.settings.currencies.edit', $modelName->lcur_id)
                            ->icon('bs.pencil'),
                    ])),

        ];
    }
}
