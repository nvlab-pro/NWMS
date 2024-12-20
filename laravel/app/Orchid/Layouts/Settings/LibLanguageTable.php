<?php

namespace App\Orchid\Layouts\Settings;

use App\Models\rwLibLanguage;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class libLanguageTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'LanguageList';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('llang_id', 'ID')
                ->sort()
                ->render(function (rwLibLanguage $modelName) {
                    return Link::make($modelName->llang_id)
                        ->route('platform.settings.languages.edit',$modelName->llang_id);
                }),

            TD::make('llang_name', 'Название')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwLibLanguage $modelName) {
                    return Link::make($modelName->llang_name)
                        ->route('platform.settings.languages.edit',$modelName->llang_id);
                }),

            TD::make('llang_code', 'Код')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwLibLanguage $modelName) {
                    return Link::make($modelName->llang_code)
                        ->route('platform.settings.languages.edit',$modelName->llang_id);
                }),


            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (rwLibLanguage $modelName) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(__('Edit'))
                            ->route('platform.settings.languages.edit', $modelName->llang_id)
                            ->icon('bs.pencil'),
                    ])),

        ];
    }
}
