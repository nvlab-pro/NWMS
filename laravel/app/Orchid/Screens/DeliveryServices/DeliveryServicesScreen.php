<?php

namespace App\Orchid\Screens\DeliveryServices;

use App\Models\rwDeliveryService;
use App\Orchid\Layouts\DeliveryServices\DeliveryServicesTable;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class DeliveryServicesScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {

        $currentUser = Auth::user();

        $dbDsList = rwDeliveryService::query();

        return [
            'dsList'  => $dbDsList->paginate(50),
        ];

    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Список магазинов');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Добавить новую службу доставки'))
                ->icon('bs.plus-circle')
                ->route('platform.delivery-services.create'),
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
            DeliveryServicesTable::class
        ];
    }
}
