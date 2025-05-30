<?php

namespace App\Orchid\Layouts\Warehouses\Places;

use App\Models\rwLibAcceptType;
use App\Models\rwPlace;
use App\Models\rwPlaceTypes;
use App\Models\rwWarehouse;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class PlacesTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'placesList';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        $currentUser = Auth::user();

        return [
            TD::make('pl_id', 'ID')
                ->sort()
                ->filter()
                ->align(TD::ALIGN_CENTER)
                ->render(function (rwPlace $place) {
                    return Link::make($place->pl_id)
                        ->route('platform.warehouses.places.index', $place->pl_id);
                }),

            TD::make('pl_barcode', CustomTranslator::get('Штрих-код'))
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->render(function (rwPlace $place) {
                    return $place->pl_type . '*' . $place->pl_id . '*' . ($place->pl_type + $place->pl_id);
                }),

            TD::make('pl_wh_id', CustomTranslator::get('Склад'))
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->filter(TD::FILTER_SELECT, rwWarehouse::where('wh_domain_id' , $currentUser->domain_id)->where('wh_type', 1)
                    ->pluck('wh_name', 'wh_id')
                    ->toArray())
                ->render(function ($model) {
                    return $model->getWh->wh_name ? $model->getWh->wh_name : '-';
                }),

            TD::make('pl_type', CustomTranslator::get('Тип места'))
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->filter(TD::FILTER_TEXT)
                ->filter(TD::FILTER_SELECT, rwPlaceTypes::query()
                    ->pluck('pt_name', 'pt_id')
                    ->toArray())
                ->render(fn($getType) => $getType->getType->pt_id ?
                    "<b style='background-color: {$getType->getType->pt_bgcolor};
                        color: {$getType->getType->pt_color};
                        padding: 5px;
                        border-radius: 5px;'>
                        ".CustomTranslator::get($getType->getType->pt_name)."
                        </b>"
                    : '-'),


            TD::make('pl_room', CustomTranslator::get('Помещение'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->align(TD::ALIGN_CENTER)
                ->render(function ($model) {
                    return $model->pl_room ? $model->pl_room : '-';
                }),

            TD::make('pl_floor', CustomTranslator::get('Этаж'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->align(TD::ALIGN_CENTER)
                ->render(function ($model) {
                    return $model->pl_floor ? $model->pl_floor : '-';
                }),

            TD::make('pl_section', CustomTranslator::get('Секция'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->popover(CustomTranslator::get("Если вы хотите получить диаппазон значений, то можете использовать значение вида 2-4 (от 2 до 4). Либо (>, <, >=, <=). Например >10 выведет поля больше 10."))
                ->align(TD::ALIGN_CENTER)
                ->render(function ($model) {
                    return $model->pl_section ? $model->pl_section : '-';
                }),

            TD::make('pl_row', CustomTranslator::get('Ряд'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->popover(CustomTranslator::get("Если вы хотите получить диаппазон значений, то можете использовать значение вида 2-4 (от 2 до 4). Либо (>, <, >=, <=). Например >10 выведет поля больше 10."))
                ->align(TD::ALIGN_CENTER)
                ->render(function ($model) {
                    return $model->pl_row ? $model->pl_row : '-';
                }),

            TD::make('pl_rack', CustomTranslator::get('Стеллаж'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->popover(CustomTranslator::get("Если вы хотите получить диаппазон значений, то можете использовать значение вида 2-4 (от 2 до 4). Либо (>, <, >=, <=). Например >10 выведет поля больше 10."))
                ->align(TD::ALIGN_CENTER)
                ->render(function ($model) {
                    return $model->pl_rack ? $model->pl_rack : '-';
                }),

            TD::make('pl_shelf', CustomTranslator::get('Полка'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->popover(CustomTranslator::get("Если вы хотите получить диаппазон значений, то можете использовать значение вида 2-4 (от 2 до 4). Либо (>, <, >=, <=). Например >10 выведет поля больше 10."))
                ->align(TD::ALIGN_CENTER)
                ->render(function ($model) {
                    return $model->pl_shelf ? $model->pl_shelf : '-';
                }),

            TD::make(CustomTranslator::get('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn(rwPlace $place) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([

                        Link::make(CustomTranslator::get('Ред.'))
                            ->route('platform.warehouses.places.index', $place->pl_id)
                            ->icon('bs.pencil'),

                        Link::make(CustomTranslator::get('Удалить'))
                            ->route('platform.warehouses.places.index', $place->pl_id)
                            ->icon('bs.trash3'),

                    ])),
        ];
    }
}

?>