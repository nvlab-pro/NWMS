<?php

namespace App\Orchid\Screens\Settings;

use App\Models\rwLibWeight;
use App\Orchid\Layouts\Settings\libWeightTable;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class LibWeightScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $weightList = new rwLibWeight();

        return [
            'weightList' => $weightList->paginate(50),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Единицы веса');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Добавить новую единицу веса'))
                ->icon('bs.plus-circle')
                ->route('platform.settings.weight.create'),
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
            libWeightTable::class,
        ];
    }
}
