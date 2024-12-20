<?php

namespace App\Orchid\Screens\Settings;

use App\Models\rwLibCountry;
use App\Orchid\Layouts\Settings\LibCountryTable;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class LibCountryScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $countryList = rwLibCountry::with('getCurrency')
            ->with('getLanguage')
            ->with('getWeight')
            ->with('getLength');

        return [
            'countryList' => $countryList->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Список стран');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Создать новую страну'))
                ->icon('bs.plus-circle')
                ->route('platform.settings.countries.create'),
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
            LibCountryTable::class,
        ];
    }
}
