<?php

namespace App\Orchid\Screens\Warehouses\Places;

use App\Models\rwPlaces;
use App\Models\rwPlaceTypes;
use App\Models\rwWarehouse;
use App\Orchid\Layouts\Warehouses\Places\PlacesTable;
use App\WhPlaces\WhPlaces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class PlacesScreen extends Screen
{

    public function query(Request $request): iterable
    {
        $currentUser = Auth::user();

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {

            $dbPlacesList = rwPlaces::with('getWh')
                ->with('getType')
                ->where('pl_domain_id', $currentUser->domain_id);

            // ***************************************
            // *** Фильтр

            $filterFields = ['pl_section', 'pl_row', 'pl_rack', 'pl_shelf'];

            foreach ($filterFields as $field) {
                if (isset($request['filter'][$field]) && $filter = $request['filter'][$field]) {
                    if (preg_match('/^(\d+)\.\.(\d+)$/', $filter, $matches)) {
                        // Фильтр диапазона, например "10..50"
                        $dbPlacesList->whereBetween($field, [$matches[1], $matches[2]]);
                    } elseif (preg_match('/^(\d+)\-(\d+)$/', $filter, $matches)) {
                        // Фильтр диапазона с дефисом, например "4-6"
                        $dbPlacesList->whereBetween($field, [$matches[1], $matches[2]]);
                    } elseif (preg_match('/^(<=|>=|<|>)(\d+)$/', $filter, $matches)) {
                        // Фильтр с оператором, например "<=50"
                        $dbPlacesList->where($field, $matches[1], $matches[2]);
                    } elseif (is_numeric($filter)) {
                        // Точное совпадение
                        $dbPlacesList->where($field, $filter);
                    }
                }
            }
        }

        return [

            'placesList' => $dbPlacesList
                ->filters()
                ->paginate(100),

        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Места хранения');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $queryParams = http_build_query(request()->query());

        $urlWithQuery = route('platform.warehouses.print.labels.index') . '?' . $queryParams;

        return [

            Link::make(__('Распечатать места'))
                ->icon('bs.printer')
                ->href($urlWithQuery),

        ];
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

            Layout::accordion([
                __('Создание новых мест хранения') => [
                    Layout::rows([

                        Group::make([

                            Select::make('reqLoacation')
                                ->title(__('Выберите склад, на котором будут созданы места:'))
                                ->width('100px')
                                ->value($currentUser->storage_id ?? null)
                                ->fromModel(rwWarehouse::where('wh_type', 1)->where('wh_domain_id', $currentUser->domain_id), 'wh_name', 'wh_id')
                                ->empty('Не выбрано', 0),

                            Select::make('reqPlaceType')
                                ->title(__('Выберите тип места:'))
                                ->width('100px')
                                ->fromModel(rwPlaceTypes::class, 'pt_name', 'pt_id'),

                        ])->fullWidth(),

                        Group::make([

                            Input::make('reqRoom')
                                ->title(__('Помещение:'))
                                ->maxlength(6)
                                ->popover('Обозначение помещения в котором будет располагаться место. Не более 6 символов.'),

                            Input::make('reqFloor')
                                ->title(__('Этаж:'))
                                ->maxlength(6)
                                ->popover('Этаж. Не более 6 символов.'),

                        ])->fullWidth(),

                        Group::make([

                            Input::make('reqSectionFrom')
                                ->title(__('Секция от:'))
                                ->type('number')
                                ->min(0)
                                ->popover('Секция (ОТ). Цифровое поле от 1 до бесконечности.'),

                            Input::make('reqSectionTo')
                                ->title(__('Секция до:'))
                                ->type('number')
                                ->min(0)
                                ->popover('Секция (ДО). Цифровое поле от 1 до бесконечности.'),

                        ])->fullWidth(),

                        Group::make([

                            Input::make('reqRowFrom')
                                ->title(__('Ряд с:'))
                                ->type('number')
                                ->min(0),

                            Input::make('reqRowTo')
                                ->title(__('Ряд до:'))
                                ->type('number')
                                ->min(0),

                        ])->fullWidth(),

                        Group::make([

                            Input::make('reqRackFrom')
                                ->title(__('Стеллаж с:'))
                                ->type('number')
                                ->min(0),

                            Input::make('reqRackTo')
                                ->title(__('Стеллаж по:'))
                                ->type('number')
                                ->min(0),

                        ])->fullWidth(),

                        Group::make([

                            Input::make('reqShelfFrom')
                                ->title(__('Полка с:'))
                                ->type('number')
                                ->min(0)
                                ->required(),

                            Input::make('reqShelfTo')
                                ->title(__('Полка по:'))
                                ->type('number')
                                ->min(0)
                                ->required(),

                        ])->fullWidth(),

                        Group::make([

                            Button::make(__('Создать'))
                                ->type(Color::DARK)
                                ->method('MakeBarcodes')
                                ->confirm(__('Вы уверены, что хотите создать места?')),

                        ])->fullWidth(),

                    ]),
                ],
            ], false),

            Layout::view('vendor.platform.actions.closeAllAccordions'), // Подключаем шаблон с анимацией загрузки

            PlacesTable::class,
        ];
    }

    // Создаем баркод
    public function MakeBarcodes(Request $request)
    {
        $currentUser = Auth::user();

        $validatedData = $request->validate([
            'reqLoacation' => 'required|integer|min:1',
            'reqPlaceType' => 'nullable|numeric|min:0',
            'reqRoom' => 'nullable|string|max:6',
            'reqFloor' => 'nullable|string|max:6',
            'reqSectionFrom' => 'nullable|numeric|min:0',
            'reqSectionTo' => 'nullable|numeric|min:0',
            'reqRowFrom' => 'nullable|numeric|min:0',
            'reqRowTo' => 'nullable|numeric|min:0',
            'reqRackFrom' => 'nullable|numeric|min:0',
            'reqRackTo' => 'nullable|numeric|min:0',
            'reqShelfFrom' => 'required|integer|min:0',
            'reqShelfTo' => 'required|integer|min:0',
        ]);

        for ($section = $validatedData['reqSectionFrom']; $section <= $validatedData['reqSectionTo']; $section++) {
            for ($row = $validatedData['reqRowFrom']; $row <= $validatedData['reqRowTo']; $row++) {
                for ($rack = $validatedData['reqRackFrom']; $rack <= $validatedData['reqRackTo']; $rack++) {
                    for ($shelf = $validatedData['reqShelfFrom']; $shelf <= $validatedData['reqShelfTo']; $shelf++) {

                        // Считаем вес ячейки
                        $placeWeight = $shelf;
                        if ($rack > 0)      $placeWeight += $rack * 10;
                        if ($row > 0)       $placeWeight += $row * 1000;
                        if ($section > 0)   $placeWeight += $section * 10000;

                        $id = rwPlaces::insertGetId([
                            'pl_domain_id'      => $currentUser->domain_id,
                            'pl_wh_id'          => $validatedData['reqLoacation'],
                            'pl_type'           => $validatedData['reqPlaceType'] ?? 0,
                            'pl_room'           => $validatedData['reqRoom'] ?: null,
                            'pl_floor'          => $validatedData['reqFloor'] ?: null,
                            'pl_section'        => $section ?: null,
                            'pl_row'            => $row ?: null,
                            'pl_rack'           => $rack ?: null,
                            'pl_shelf'          => $shelf,
                            'pl_place_weight'   => $placeWeight,
                        ]);

                    }
                }
            }
        }


        Alert::success('Места успешно созданы!');

        return redirect()->back()->with('success', __('Места хранения успешно созданы.'));
    }
}
