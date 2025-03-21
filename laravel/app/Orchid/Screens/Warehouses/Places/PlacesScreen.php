<?php

namespace App\Orchid\Screens\Warehouses\Places;

use App\Models\rwPlace;
use App\Models\rwPlaceTypes;
use App\Models\rwWarehouse;
use App\Orchid\Layouts\Warehouses\Places\PlacesTable;
use App\Services\CustomTranslator;
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

            $dbPlacesList = rwPlace::with('getWh')
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
        return CustomTranslator::get('Места хранения');
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

            Link::make(CustomTranslator::get('Распечатать места'))
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
                CustomTranslator::get('Создание новых мест хранения') => [
                    Layout::rows([

                        Group::make([

                            Select::make('reqLoacation')
                                ->title(CustomTranslator::get('Выберите склад, на котором будут созданы места:'))
                                ->width('100px')
                                ->value($currentUser->storage_id ?? null)
                                ->fromModel(rwWarehouse::where('wh_type', 1)->where('wh_domain_id', $currentUser->domain_id), 'wh_name', 'wh_id')
                                ->empty(CustomTranslator::get('Не выбрано'), 0),

                            Select::make('reqPlaceType')
                                ->title(CustomTranslator::get('Выберите тип места:'))
                                ->width('100px')
                                ->options(
                                    rwPlaceTypes::all()
                                        ->pluck('pt_name', 'pt_id')
                                        ->map(fn($name) => CustomTranslator::get($name)) // Переводим названия
                                ),

                        ])->fullWidth(),

                        Group::make([

                            Input::make('reqRoom')
                                ->title(CustomTranslator::get('Помещение:'))
                                ->maxlength(6)
                                ->popover(CustomTranslator::get('Обозначение помещения в котором будет располагаться место. Не более 6 символов.')),

                            Input::make('reqFloor')
                                ->title(CustomTranslator::get('Этаж:'))
                                ->maxlength(6)
                                ->popover(CustomTranslator::get('Этаж. Не более 6 символов.')),

                        ])->fullWidth(),

                        Group::make([

                            Input::make('reqSectionFrom')
                                ->title(CustomTranslator::get('Секция от:'))
                                ->type('number')
                                ->min(0)
                                ->popover(CustomTranslator::get('Секция (ОТ). Цифровое поле от 1 до бесконечности.')),

                            Input::make('reqSectionTo')
                                ->title(CustomTranslator::get('Секция до:'))
                                ->type('number')
                                ->min(0)
                                ->popover(CustomTranslator::get('Секция (ДО). Цифровое поле от 1 до бесконечности.')),

                        ])->fullWidth(),

                        Group::make([

                            Input::make('reqRowFrom')
                                ->title(CustomTranslator::get('Ряд с:'))
                                ->type('number')
                                ->min(0),

                            Input::make('reqRowTo')
                                ->title(CustomTranslator::get('Ряд до:'))
                                ->type('number')
                                ->min(0),

                        ])->fullWidth(),

                        Group::make([

                            Input::make('reqRackFrom')
                                ->title(CustomTranslator::get('Стеллаж с:'))
                                ->type('number')
                                ->min(0),

                            Input::make('reqRackTo')
                                ->title(CustomTranslator::get('Стеллаж по:'))
                                ->type('number')
                                ->min(0),

                        ])->fullWidth(),

                        Group::make([

                            Input::make('reqShelfFrom')
                                ->title(CustomTranslator::get('Полка с:'))
                                ->type('number')
                                ->min(0)
                                ->required(),

                            Input::make('reqShelfTo')
                                ->title(CustomTranslator::get('Полка по:'))
                                ->type('number')
                                ->min(0)
                                ->required(),

                        ])->fullWidth(),

                        Group::make([

                            Button::make(CustomTranslator::get('Создать'))
                                ->type(Color::DARK)
                                ->method('MakeBarcodes')
                                ->confirm(CustomTranslator::get('Вы уверены, что хотите создать места?')),

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

                        $id = rwPlace::insertGetId([
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

        return redirect()->back()->with('success', CustomTranslator::get('Места хранения успешно созданы.'));
    }
}
