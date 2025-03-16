<?php

namespace App\Orchid\Screens\Settings;

use App\Models\rwLibCity;
use App\Orchid\Layouts\Settings\LibCityTable;
use App\Services\CustomTranslator;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class LibCityScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $cityList = rwLibCity::with('getCountry');

        return [
            'cityList' => $cityList->paginate(50),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return CustomTranslator::get('Список городов');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(CustomTranslator::get('Добавить новый город'))
                ->icon('bs.plus-circle')
                ->route('platform.settings.cities.create'),

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
            LibCityTable::class,
        ];
    }
}
