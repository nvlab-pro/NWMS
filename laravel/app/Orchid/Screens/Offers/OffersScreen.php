<?php

namespace App\Orchid\Screens\Offers;

use App\Models\rwOffer;
use App\Orchid\Layouts\Offers\OffersTable;
use App\Services\CustomTranslator;
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

        if ($currentUser->hasRole('admin')) {

            $dbOffers = rwOffer::query();

        } else {

            if ($currentUser->hasRole('warehouse_manager')) {

                $dbOffers = rwOffer::where('of_domain_id', $currentUser->domain_id);

            } else {

                $dbOffers = rwOffer::where('of_domain_id', $currentUser->domain_id)
                    ->whereHas('getShop', function ($query) use ($currentUser) {
                        $query->whereIn('sh_user_id', [$currentUser->id, $currentUser->parent_id]);
                    });

            }

        }

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
        return CustomTranslator::get('Список товаров');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(CustomTranslator::get('Добавить новый товар'))
                ->icon('bs.plus-circle')
                ->route('platform.offers.create'),

            Link::make(CustomTranslator::get('Импорт товаров'))
                ->icon('bs.upload')
                ->route('platform.offers.import'),
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
