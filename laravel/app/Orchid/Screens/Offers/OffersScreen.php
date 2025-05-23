<?php

namespace App\Orchid\Screens\Offers;

use App\Exports\OffersExport;
use App\Models\rwOffer;
use App\Orchid\Layouts\Offers\OffersTable;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Illuminate\Http\Request;

class OffersScreen extends Screen
{

    private $currentOffers;

    public function query(Request $request): iterable
    {
        $currentUser = Auth::user();

        if ($currentUser->hasRole('admin')) {

            $dbOffers = rwOffer::with('barcodes');

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

        $this->currentOffers = $dbOffers->filters();

        if ($request->has('filter.barcodes')) {
            $filter = $request->get('filter');

            $dbOffers->whereHas('barcodes', function ($query) use ($filter) {
                $query->where('br_barcode', 'like', '%' . $filter['barcodes'] . '%');
            });
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
                ->icon('bs.cloud-upload')
                ->route('platform.offers.import'),

            Link::make(CustomTranslator::get('Экспорт в Excel'))
                ->route('platform.offers.export', request()->all()) // 🔥 передаём фильтры из текущего запроса
                ->icon('bs.cloud-download'),

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

    public function export()
    {
        return (new OffersExport($this->currentOffers->get()))
            ->download('offers.xlsx');
    }
}
