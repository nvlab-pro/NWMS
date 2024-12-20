<?php

namespace App\Orchid\Screens\Warehouses;

use App\Models\rwLibWhType;
use App\Models\rwWarehouse;
use App\Models\User;
use App\Models\rwShop;
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
            'whList' => $dbWhList->first(),
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
                ->fromModel(rwWarehouse::where('wh_type', 1)->get(), 'wh_name', 'wh_id')
                ->title(__('Выберите склад ФФ'));

        } else {
            $arAddFields[] = Input::make('whList.wh_user_id')
                ->type('hidden')
                ->value($currentUser->id);
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

        $request->validate([
            'rwShop.sh_id' => 'nullable|integer',
            'rwShop.sh_name' => 'required|string|max:150',
            'rwShop.sh_user_id' => 'required|integer',
        ]);

        if (isset($request->rwShop['sh_id']) && $request->rwShop['sh_id'] > 0) {

            rwShop::where('sh_id', $request->rwShop['sh_id'])->update([
                'sh_name' => $request->rwShop['sh_name'],
                'sh_user_id' => $request->rwShop['sh_user_id'],
            ]);

            Alert::success(__('Магазин успешно отредактирован!'));

        } else {

            rwShop::insert([
                'sh_user_id' => $request->rwShop['sh_user_id'],
                'sh_name' => $request->rwShop['sh_name'],
            ]);

            Alert::success(__('Магазин успешно создан!'));

        }


        return redirect()->route('platform.shops.index');
    }
}
