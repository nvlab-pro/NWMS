<?php

namespace App\Orchid\Screens\Settings;

use App\Models\rwLibLength;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use App\Orchid\Layouts\Settings\LibLengthTable;

class LibLengthScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $lengthsList = new rwLibLength();

        return [
            'lengthsList' => $lengthsList->paginate(50),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Единицы размера');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Добавить новую единицу измерения'))
                ->icon('bs.plus-circle')
                ->route('platform.settings.length.create'),
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
            LibLengthTable::class,
        ];
    }

}
