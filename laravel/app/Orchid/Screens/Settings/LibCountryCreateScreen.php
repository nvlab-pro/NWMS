<?php

namespace App\Orchid\Screens\Settings;

use App\Models\rwLibCountry;
use App\Models\rwLibCurrency;
use App\Models\rwLibLanguage;
use App\Models\rwLibLength;
use App\Models\rwLibWeight;
use App\Models\MistralPartners;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class LibCountryCreateScreen extends Screen
{
    public $countryId = 0;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query($countryId = 0): iterable
    {
        $this->countryId = $countryId;

        if ($countryId == 0) {
            return [];
        } else {
            return [
                'rwLibCountry' => rwLibCountry::where('lco_id', $countryId)->first(),
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
        return $this->countryId > 0 ? CustomTranslator::get('Редактирование страны') : CustomTranslator::get('Создание новой страны');
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

                Input::make('rwLibCountry.lco_id')
                    ->type('hidden')
                    ->width(50),

                Input::make('rwLibCountry.lco_name')
                    ->width(50)
                    ->title(CustomTranslator::get('Название')),

                Input::make('rwLibCountry.lco_code')
                    ->title(CustomTranslator::get('Код')),

                Select::make('rwLibCountry.lco_currency_id')
                    ->title(CustomTranslator::get('Валюта'))
                    ->width('100px')
                    ->fromModel(rwLibCurrency::class, 'lcur_name', 'lcur_id')
                    ->empty(CustomTranslator::get('Не выбрано'), 0)
                    ->required(0),

                Select::make('rwLibCountry.lco_lang_id')
                    ->title(CustomTranslator::get('Язык'))
                    ->width('100px')
                    ->fromModel(rwLibLanguage::class, 'llang_name', 'llang_id')
                    ->empty(CustomTranslator::get('Не выбрано'), 0)
                    ->required(0),

                Select::make('rwLibCountry.lco_weight_id')
                    ->title(CustomTranslator::get('Мера веса'))
                    ->width('100px')
                    ->fromModel(rwLibWeight::class, 'lw_name', 'lw_id')
                    ->empty(CustomTranslator::get('Не выбрано'), 0)
                    ->required(0),

                Select::make('rwLibCountry.lco_length_id')
                    ->title(CustomTranslator::get('Мера длины'))
                    ->width('100px')
                    ->fromModel(rwLibLength::class, 'llen_name', 'llen_id')
                    ->empty(CustomTranslator::get('Не выбрано'), 0)
                    ->required(0),

                Button::make(CustomTranslator::get('Сохранить'))
                    ->type(Color::DARK)
                    ->style('margin-bottom: 20px;')
                    ->method('saveCurrency'),

            ]),
        ];
    }


    function saveCurrency(Request $request)
    {

        $request->validate([
            'rwLibCountry.lco_id'           => 'required|integer',
            'rwLibCountry.lco_name'         => 'required|string|max:100',
            'rwLibCountry.lco_code'         => 'required|string|max:6',
            'rwLibCountry.lco_currency_id'  => 'required|integer',
            'rwLibCountry.lco_lang_id'      => 'required|integer',
            'rwLibCountry.lco_weight_id'    => 'required|integer',
            'rwLibCountry.lco_length_id'    => 'required|integer',
        ]);

        if (isset($request->cmLibCountry['lco_id']) && $request->cmLibCountry['lco_id'] > 0) {

            rwLibCountry::where('lco_id', $request->cmLibCountry['lco_id'])->update([
                'lco_code'          => $request->cmLibCountry['lco_code'],
                'lco_name'          => $request->cmLibCountry['lco_name'],
                'lco_currency_id'   => $request->cmLibCountry['lco_currency_id'],
                'lco_lang_id'       => $request->cmLibCountry['lco_lang_id'],
                'lco_weight_id'     => $request->cmLibCountry['lco_weight_id'],
                'lco_length_id'     => $request->cmLibCountry['lco_length_id'],
            ]);

            Alert::success(CustomTranslator::get('Данные успешно отредактированы!'));

        } else {

            rwLibCountry::insert([
                'lco_code'          => $request->cmLibCountry['lco_code'],
                'lco_name'          => $request->cmLibCountry['lco_name'],
                'lco_currency_id'   => $request->cmLibCountry['lco_currency_id'],
                'lco_lang_id'       => $request->cmLibCountry['lco_lang_id'],
                'lco_weight_id'     => $request->cmLibCountry['lco_weight_id'],
                'lco_length_id'     => $request->cmLibCountry['lco_length_id'],
            ]);

            Alert::success(CustomTranslator::get('Данные успешно добавлены!'));

        }


        return redirect()->route('platform.settings.countries');
    }
}
