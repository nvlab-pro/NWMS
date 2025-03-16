<?php

namespace App\Orchid\Screens\Settings;

use App\Models\rwLibCurrency;
use App\Orchid\Layouts\Settings\LibCurrencyTable;
use App\Services\CustomTranslator;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class LibCurrencyScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $currencyList = new rwLibCurrency();

        return [
            'currencyList' => $currencyList->paginate(50),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return CustomTranslator::get('Валюты');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(CustomTranslator::get('Добавить новую валюту'))
                ->icon('bs.plus-circle')
                ->route('platform.settings.currencies.create'),
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
            LibCurrencyTable::class,
        ];
    }
}
