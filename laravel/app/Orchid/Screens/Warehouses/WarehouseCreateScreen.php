<?php

namespace App\Orchid\Screens\Warehouses;

use App\Models\rwDomain;
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
        return $this->whId > 0 ? CustomTranslator::get('Редактирование склада') : CustomTranslator::get('Создание склада');
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
        $arAddFields2 = [];

        if ($currentUser->hasRole('admin')) {

            $arAddFields2[] = Select::make('whList.wh_domain_id')
                ->fromModel(rwDomain::get(), 'dm_name', 'dm_id')
                ->value($currentUser->domain_id)
                ->title(CustomTranslator::get('Выберите домен'));

        } else {

            $arAddFields2[] = Input::make('whList.wh_domain_id')
                ->type('hidden')
                ->value($currentUser->domain_id);

        }

        if ($currentUser->hasRole('admin')) {

            $arAddFields[] = Select::make('whList.wh_user_id')
                ->options(
                    User::with('getDomain')->get()->pluck('name', 'id')->map(function ($name, $id) {
                        $user = User::find($id);
                        isset($user->getDomain->dm_name) ? $domain = $user->getDomain->dm_name : $domain = '-';
                        return $domain ? "$name ($domain)" : $name;
                    })->toArray()
                )
                ->title(CustomTranslator::get('Выберите владельца'));

            $arAddFields[] = Select::make('whList.wh_parent_id')
//                ->fromModel(rwWarehouse::where('wh_type', 1)->where('wh_domain_id', $currentUser->domain_id)->get(), 'wh_name', 'wh_id')
                ->options(
                    rwWarehouse::where('wh_type', 1)->with('getDomain')->get()->pluck('wh_name', 'wh_id')->map(function ($name, $id) {
                        $wh = rwWarehouse::find($id);
                        isset($wh->getDomain->dm_name) ? $domain = $wh->getDomain->dm_name : $domain = '-';
                        return $domain ? "$name ($domain)" : $name;
                    })->toArray()
                )
                ->empty(CustomTranslator::get('Не задан'))
                ->title(CustomTranslator::get('Выберите склад ФФ'));

            $arAddFields[] = Select::make('whList.wh_type')
                ->options(
                    rwLibWhType::all()
                        ->pluck('lwt_name', 'lwt_id')
                        ->map(fn($name) => CustomTranslator::get($name)) // Переводим названия
                )
                ->title(CustomTranslator::get('Выберите тип склада'));

        } else {

            if ($currentUser->hasRole('warehouse_manager')) {

                $arAddFields[] = Select::make('whList.wh_user_id')
                    ->fromModel(
                        User::where('domain_id', $currentUser->domain_id),
                        'name', 'id')
                    ->title(CustomTranslator::get('Выберите владельца'));

                $arAddFields[] = Select::make('whList.wh_parent_id')
                    ->fromModel(rwWarehouse::where('wh_type', 1)->where('wh_domain_id', $currentUser->domain_id)->get(), 'wh_name', 'wh_id')
                    ->title(CustomTranslator::get('Выберите склад ФФ'));

                $arAddFields[] = Input::make('whList.wh_type')
                    ->type('hidden')
                    ->value(2);

            } else {

                $arAddFields[] = Select::make('whList.wh_parent_id')
                    ->fromModel(rwWarehouse::where('wh_type', 1)->where('wh_domain_id', $currentUser->domain_id)->get(), 'wh_name', 'wh_id')
                    ->title(CustomTranslator::get('Выберите склад ФФ'));

                $arAddFields[] = Input::make('whList.wh_user_id')
                    ->type('hidden')
                    ->value($currentUser->id);

                $arAddFields[] = Input::make('whList.wh_type')
                    ->type('hidden')
                    ->value(2);

            }
        }

        return [

            Layout::rows(
                array_merge(
                    [
                        Input::make('whList.wh_id')
                            ->type('hidden'),

                        Input::make('whList.wh_name')
                            ->width(50)
                            ->title(CustomTranslator::get('Название')),
                    ],
                    $arAddFields,
                    $arAddFields2,
                    [
                        Button::make(CustomTranslator::get('Сохранить'))
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
            'whList.wh_domain_id' => 'nullable|integer',
        ]);

        // Если тип склада равен 1, то родительский ID не требуется
        if ($data['whList']['wh_type'] == 1) {
            $data['whList']['wh_parent_id'] = null;
        }

        if (isset($data['whList']['wh_id']) && $data['whList']['wh_id'] > 0) {

            // Обновление существующего склада
            rwWarehouse::where('wh_id', $data['whList']['wh_id'])->update([
                'wh_name' => $data['whList']['wh_name'],
                'wh_user_id' => $data['whList']['wh_user_id'],
                'wh_type' => $data['whList']['wh_type'],
                'wh_parent_id' => $data['whList']['wh_parent_id'],
                'wh_domain_id' => $data['whList']['wh_domain_id'],
            ]);

            Alert::success(CustomTranslator::get('Склад успешно отредактирован!'));
        } else {

            // Создание нового склада
            $currentWarehouse = rwWarehouse::create([
                'wh_name' => $data['whList']['wh_name'],
                'wh_user_id' => $data['whList']['wh_user_id'],
                'wh_type' => $data['whList']['wh_type'],
                'wh_parent_id' => $data['whList']['wh_parent_id'],
                'wh_domain_id' => $data['whList']['wh_domain_id'],
            ]);

            Alert::success(CustomTranslator::get('Склад успешно создан!'));
        }


        return redirect()->route('platform.warehouses.index');
    }
}
