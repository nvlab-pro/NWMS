<?php

namespace App\Orchid\Screens\Settings;

use App\Models\rwLibCity;
use App\Models\rwLibCountry;
use App\Models\rwLibLength;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class LibCityCreateScreen extends Screen
{
    public $cityId = 0;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query($cityId = 0): iterable
    {
        $this->cityId = $cityId;

        if ($cityId == 0) {
            return [];
        } else {
            return [
                'rwLibCity' => rwLibCity::where('lcit_id', $cityId)->first(),
            ];
        }
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->cityId > 0 ? CustomTranslator::get('Редактирование единицы измерения') : CustomTranslator::get('Создание новой единицы измерения');
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
        return [
            Layout::rows([

                Input::make('rwLibCity.lcit_id')
                    ->type('hidden')
                    ->width(50),

                Input::make('rwLibCity.lcit_name')
                    ->width(50)
                    ->title(CustomTranslator::get('Название')),

                Input::make('rwLibCity.lcit_coord_latitude')
                    ->title(CustomTranslator::get('Широта')),

                Input::make('rwLibCity.lcit_coord_longitude')
                    ->title(CustomTranslator::get('Долгота')),


                Select::make('rwLibCity.lcit_country_id')
                    ->title(CustomTranslator::get('Страна'))
                    ->width('100px')
                    ->fromModel(rwLibCountry::class, 'lco_name', 'lco_id')
                    ->empty(CustomTranslator::get('Не выбрано'), 0)
                    ->required(0),

                Button::make(CustomTranslator::get('Сохранить'))
                    ->type(Color::DARK)
                    ->style('margin-bottom: 20px;')
                    ->method('saveCity'),

            ]),
        ];
    }


    function saveCity(Request $request)
    {

        $request->validate([
            'rwLibCity.lcit_id'                 => 'nullable|integer',
            'rwLibCity.lcit_name'               => 'required|string|max:100',
            'rwLibCity.lcit_coord_latitude'     => 'required',
            'rwLibCity.lcit_coord_longitude'    => 'required',
            'rwLibCity.lcit_country_id'         => 'nullable|integer',
        ]);

        if (isset($request->cmLibCity['lcit_id']) && $request->cmLibCity['lcit_id'] > 0) {

            rwLibCity::where('lcit_id', $request->cmLibCity['lcit_id'])->update([
                'lcit_name'             => $request->cmLibCity['lcit_name'],
                'lcit_coord_latitude'   => $request->cmLibCity['lcit_coord_latitude'],
                'lcit_coord_longitude'  => $request->cmLibCity['lcit_coord_longitude'],
                'lcit_country_id'       => $request->cmLibCity['lcit_country_id'],
            ]);

            Alert::success(CustomTranslator::get('Данные успешно отредактированы!'));

        } else {

            rwLibCity::insert([
                'lcit_name'             => $request->cmLibCity['lcit_name'],
                'lcit_coord_latitude'   => $request->cmLibCity['lcit_coord_latitude'],
                'lcit_coord_longitude'  => $request->cmLibCity['lcit_coord_longitude'],
                'lcit_country_id'       => $request->cmLibCity['lcit_country_id'],
            ]);

            Alert::success(CustomTranslator::get('Данные успешно добавлены!'));

        }


        return redirect()->route('platform.settings.cities');
    }

}
