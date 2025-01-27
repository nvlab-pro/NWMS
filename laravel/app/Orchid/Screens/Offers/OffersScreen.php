<?php

namespace App\Orchid\Screens\Offers;

use App\Models\rwOffer;
use App\Orchid\Layouts\Offers\OffersTable;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class OffersScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $currentUser = Auth::user();

        $dbOffers = rwOffer::where('of_domain_id', $currentUser->domain_id);

        return [
            'offersList' => $dbOffers->filters()->paginate(50),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Список товаров');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Добавить новый товар'))
                ->icon('bs.plus-circle')
                ->route('platform.offers.create'),
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
            OffersTable::class,
        ];
    }
}
