<?php

namespace App\Orchid\Layouts\Settings;

use App\Models\rwLibLanguage;
use App\Services\CustomTranslator;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class LibLanguageTable extends Table
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

            TD::make('llang_name', CustomTranslator::get('Название'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwLibLanguage $modelName) {
                    return Link::make($modelName->llang_name)
                        ->route('platform.settings.languages.edit',$modelName->llang_id);
                }),

            TD::make('llang_code', CustomTranslator::get('Код'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwLibLanguage $modelName) {
                    return Link::make($modelName->llang_code)
                        ->route('platform.settings.languages.edit',$modelName->llang_id);
                }),


            TD::make(CustomTranslator::get('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (rwLibLanguage $modelName) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(CustomTranslator::get('Edit'))
                            ->route('platform.settings.languages.edit', $modelName->llang_id)
                            ->icon('bs.pencil'),
                    ])),

        ];
    }
}
