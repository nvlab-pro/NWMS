<?php

namespace App\Orchid\Screens\Warehouses;

use App\Models\rwCompany;
use App\Models\rwDomain;
use App\Models\rwLibWhType;
use App\Models\rwWarehouse;
use App\Models\User;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
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

        $arAddFields2[] = Select::make('whList.wh_company_id')
            ->title(CustomTranslator::get('Компания'))
            ->fromModel(rwCompany::where('co_domain_id', $currentUser->domain_id), 'co_name', 'co_id')
            ->empty(CustomTranslator::get('Не выбрано'), '');

        $arAddFields2[] = CheckBox::make('whList.wh_set_production_date')
            ->title(CustomTranslator::get('Использовать дату производства товара'))
            ->horizontal()
            ->sendTrueOrFalse()
            ->value($currentUser->wh_set_production_date);

        $arAddFields2[] = CheckBox::make('whList.wh_set_expiration_date')
            ->title(CustomTranslator::get('Использовать срок годности товара'))
            ->horizontal()
            ->sendTrueOrFalse()
            ->value($currentUser->wh_set_expiration_date);

        $arAddFields2[] = CheckBox::make('whList.wh_set_batch')
            ->title(CustomTranslator::get('Использовать поле партия'))
            ->horizontal()
            ->sendTrueOrFalse()
            ->value($currentUser->wh_set_batch);

        return [
            Layout::tabs([
                CustomTranslator::get('Основная') => [
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
                                    ->method('saveWh'),
                            ]
                        )
                    ),
                ],
                CustomTranslator::get('Настройка этикетки') => [
                    Layout::rows([
                        CheckBox::make('whList.wh_use_custom_label')
                            ->sendTrueOrFalse()
                            ->placeholder(CustomTranslator::get(' - При включении, при печати этикетки на упаковке будет использоваться HTML-код ниже')),

                        TextArea::make('whList.wh_custom_label')
                            ->rows(20)
                            ->cols(100)
                            ->title(CustomTranslator::get('HTML-код этикетки'))
                            ->popover(CustomTranslator::get('Введите HTML для печатной этикетки.')),

                        Button::make(CustomTranslator::get('Сохранить'))
                            ->type(Color::DARK)
                            ->style('margin-bottom: 20px;')
                            ->method('saveLabel'),
                    ]),
                    Layout::view('Screens.Warehouses.WhLabelInstructions'),
                ],
            ]),
        ];

    }

    function saveLabel(Request $request)
    {
        $currentUser = Auth::user();

        $data = $request->validate([
            'whList.wh_id' => 'nullable|integer',
            'whList.wh_use_custom_label' => 'nullable|integer',
            'whList.wh_custom_label' => 'required|string',
        ]);

        $whId = $data['whList']['wh_id'];

        $dbWarehouse = rwWarehouse::find($whId);
        $dbWarehouse->wh_use_custom_label = $data['whList']['wh_use_custom_label'];
        $dbWarehouse->wh_custom_label = $data['whList']['wh_custom_label'];
        $dbWarehouse->save();

        Alert::success(CustomTranslator::get('Данные этикетки сохранены!'));

        return redirect()->route('platform.warehouses.edit', $whId);
    }
    function saveWh(Request $request)
    {
        $currentUser = Auth::user();

        $data = $request->validate([
            'whList.wh_id' => 'nullable|integer',
            'whList.wh_name' => 'required|string|max:150',
            'whList.wh_user_id' => 'required|integer',
            'whList.wh_company_id' => 'required|integer',
            'whList.wh_type' => 'required|integer',
            'whList.wh_parent_id' => 'nullable|integer',
            'whList.wh_domain_id' => 'nullable|integer',
            'whList.wh_set_production_date' => 'nullable|integer',
            'whList.wh_set_expiration_date' => 'nullable|integer',
            'whList.wh_set_batch' => 'nullable|integer',
        ]);

        // Если тип склада равен 1, то родительский ID не требуется
        if ($data['whList']['wh_type'] == 1) {
            $data['whList']['wh_parent_id'] = null;
        }

        if (!isset($data['whList']['wh_set_production_date'])) $data['whList']['wh_set_production_date'] = 0;
        if (!isset($data['whList']['wh_set_expiration_date'])) $data['whList']['wh_set_expiration_date'] = 0;
        if (!isset($data['whList']['wh_set_batch'])) $data['whList']['wh_set_batch'] = 0;

        if (isset($data['whList']['wh_id']) && $data['whList']['wh_id'] > 0) {

            // Обновление существующего склада
            rwWarehouse::where('wh_id', $data['whList']['wh_id'])->update([
                'wh_name' => $data['whList']['wh_name'],
                'wh_user_id' => $data['whList']['wh_user_id'],
                'wh_country_id' => $data['whList']['wh_user_id'],
                'wh_company_id' => $data['whList']['wh_company_id'],
                'wh_type' => $data['whList']['wh_type'],
                'wh_parent_id' => $data['whList']['wh_parent_id'],
                'wh_domain_id' => $data['whList']['wh_domain_id'],
                'wh_set_production_date' => $data['whList']['wh_set_production_date'],
                'wh_set_expiration_date' => $data['whList']['wh_set_expiration_date'],
                'wh_set_batch' => $data['whList']['wh_set_batch'],
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
                'wh_company_id' => $data['whList']['wh_company_id'],
                'wh_set_production_date' => $data['whList']['wh_set_production_date'],
                'wh_set_expiration_date' => $data['whList']['wh_set_expiration_date'],
                'wh_set_batch' => $data['whList']['wh_set_batch'],
            ]);

            Alert::success(CustomTranslator::get('Склад успешно создан!'));
        }


        return redirect()->route('platform.warehouses.index');
    }
}
