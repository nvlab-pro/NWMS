<?php

namespace App\Orchid\Screens\Warehouses;

use App\Models\rwLibWhType;
use App\Models\rwWarehouse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class WarehouseCreateScreen extends Screen
{
    public $whId;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query($whId = 0): iterable
    {

        $this->whId = $whId;

        $currentUser = Auth::user();

        $dbWhList = rwWarehouse::query();

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {


        } else {

            $dbWhList = $dbWhList->where('wh_user_id', $currentUser->id);

        }

        return [
            'whList' => $dbWhList->where('wh_id', $this->whId)->first(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->whId > 0 ? __('Редактирование склада') : __('Создание склада');
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

        $arAddFields = [];

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {
            $arAddFields[] = Select::make('whList.wh_user_id')
                ->fromModel(User::get(), 'name', 'id')
                ->title(__('Выберите владельца'));

            $arAddFields[] = Select::make('whList.wh_type')
                ->fromModel(rwLibWhType::get(), 'lwt_name', 'lwt_id')
                ->title(__('Выберите тип склада'));

            $arAddFields[] = Select::make('whList.wh_parent_id')
                ->fromModel(rwWarehouse::where('wh_type', 1)->where('wh_domain_id', $currentUser->domain_id)->get(), 'wh_name', 'wh_id')
                ->empty('Не выбрано')
                ->title(__('Выберите склад ФФ'));

        } else {
            $arAddFields[] = Input::make('whList.wh_user_id')
                ->type('hidden')
                ->value($currentUser->id);

            $arAddFields[] = Input::make('whList.wh_type')
                ->type('hidden')
                ->value(2);
        }

        return [
            Layout::rows(
                array_merge(
                    [
                        Input::make('whList.wh_id')
                            ->type('hidden'),

                        Input::make('whList.wh_name')
                            ->width(50)
                            ->title(__('Название')),
                    ],
                    $arAddFields,
                    [
                        Button::make(__('Сохранить'))
                            ->type(Color::DARK)
                            ->style('margin-bottom: 20px;')
                            ->method('saveShop'),

                    ])
            ),
        ];
    }

    function saveShop(Request $request)
    {
        $currentUser = Auth::user();

        $data = $request->validate([
            'whList.wh_id' => 'nullable|integer',
            'whList.wh_name' => 'required|string|max:150',
            'whList.wh_user_id' => 'required|integer',
            'whList.wh_type' => 'required|integer',
            'whList.wh_parent_id' => 'nullable|integer',
        ]);

        // Если тип склада равен 2, то родительский ID не требуется
        if ($data['whList']['wh_type'] == 2) {
            $data['whList']['wh_parent_id'] = null;
        }

        if (isset($data['whList']['wh_id']) && $data['whList']['wh_id'] > 0) {

            // Обновление существующего склада
            rwWarehouse::where('wh_id', $data['whList']['wh_id'])->update([
                'wh_name' => $data['whList']['wh_name'],
                'wh_user_id' => $data['whList']['wh_user_id'],
                'wh_type' => $data['whList']['wh_type'],
                'wh_parent_id' => $data['whList']['wh_parent_id'],
            ]);

            Alert::success(__('Склад успешно отредактирован!'));
        } else {

            // Создание нового склада
            $currentWarehouse = rwWarehouse::create([
                'wh_name' => $data['whList']['wh_name'],
                'wh_user_id' => $data['whList']['wh_user_id'],
                'wh_type' => $data['whList']['wh_type'],
                'wh_parent_id' => $data['whList']['wh_parent_id'],
                'wh_domain_id' => $currentUser->domain_id,
            ]);

            Alert::success(__('Склад успешно создан!'));
        }


        return redirect()->route('platform.warehouses.index');
    }
}
