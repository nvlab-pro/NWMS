<?php

namespace App\Orchid\Screens\Warehouses\Places;

use App\Models\rwPlaces;
use App\Orchid\Layouts\Warehouses\Places\PlacesTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

class LabelPrintScreen extends Screen
{
    /**
     * Данные экрана.
     *
     * @var array
     */
    public $name = 'Печать этикеток';
    public $description = 'Экран для печати этикеток';

    /**
     * Данные, которые будут переданы в экран.
     *
     * @return array
     */
    public function query(Request $request): array
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
                ->get(),

        ];
    }

    /**
     * Действия, доступные на экране.
     *
     * @return array
     */
    public function commandBar(): array
    {
        return [];
    }

    /**
     * Разметка экрана.
     *
     * @return Layout
     */
    public function layout(): array
    {
        return [

            Layout::view('Screens.Terminal.Warehouses.Places.LabelPrint'),

        ];
    }
}
