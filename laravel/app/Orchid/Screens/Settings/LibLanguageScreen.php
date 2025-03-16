<?php

namespace App\Orchid\Screens\Settings;

use App\Models\rwLibLanguage;
use App\Orchid\Layouts\Settings\LibLanguageTable;
use App\Services\CustomTranslator;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class LibLanguageScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {

        $LanguageList = new rwLibLanguage();

        return [
            'LanguageList' => $LanguageList->paginate(50),
        ];

    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return CustomTranslator::get('Языки');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(CustomTranslator::get('Добавить новый язык'))
                ->icon('bs.plus-circle')
                ->route('platform.settings.languages.create'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            LibLanguageTable::class,
        ];
    }
}
