<?php

namespace App\Orchid\Screens\DeliveryServices;

use App\Models\rwDeliveryService;
use App\Models\rwLibWhType;
use App\Models\rwWarehouse;
use App\Models\User;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class DeliveryServicesCreateScreen extends Screen
{
    public $dsId;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query($dsId = 0): iterable
    {

        $this->dsId = $dsId;
        $dbDsList = [];

        $currentUser = Auth::user();

        if ($dsId > 0) {
            $dbDsList = rwDeliveryService::query()->where('ds_id', $dsId)->first();
        }

        return [
            'dsList' => $dbDsList,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->dsId > 0 ? CustomTranslator::get('Редактирование службы доставки') : CustomTranslator::get('Создание службы доставки');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        $currentUser = Auth::user();

        return [
            Layout::rows([
                Input::make('dsList.ds_id')
                    ->type('hidden'),

                Input::make('dsList.ds_name')
                    ->width(50)
                    ->title(CustomTranslator::get('Название')),

                Button::make(CustomTranslator::get('Сохранить'))
                    ->type(Color::DARK)
                    ->style('margin-bottom: 20px;')
                    ->method('saveShop'),

            ]),
        ];
    }

    function saveShop(Request $request)
    {

        $data = $request->validate([
            'dsList.ds_id' => 'nullable|integer',
            'dsList.ds_name' => 'required|string|max:150',
        ]);

        if (isset($data['dsList']['ds_id']) && $data['dsList']['ds_id'] > 0) {

            // Обновление существующего склада
            rwDeliveryService::where('ds_id', $data['dsList']['ds_id'])->update([
                'ds_name' => $data['dsList']['ds_name'],
            ]);

            Alert::success(CustomTranslator::get('Служба доставки успешно отредактирована!'));
        } else {

            // Создание нового склада
            $currentDeliveryService = rwDeliveryService::create([
                'ds_name' => $data['dsList']['ds_name'],
            ]);

            Alert::success(CustomTranslator::get('Служба доставка успешно создана!'));
        }


        return redirect()->route('platform.delivery-services.list');
    }
}
