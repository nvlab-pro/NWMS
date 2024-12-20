<?php

namespace App\Orchid\Screens\Settings;

use App\Models\rwLibCurrency;
use App\Models\rwLibLength;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class LibCurrencyCreateScreen extends Screen
{
    public $curId = 0;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query($curId = 0): iterable
    {
        $this->curId = $curId;

        if ($curId == 0) {
            return [];
        } else {
            return [
                'rwLibCurrency' => rwLibCurrency::where('lcur_id', $curId)->first(),
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
        return $this->curId > 0 ? __('Редактирование валюты') : __('Создание новой валюты');
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

                Input::make('rwLibCurrency.lcur_id')
                    ->type('hidden')
                    ->width(50),

                Input::make('rwLibCurrency.lcur_name')
                    ->width(50)
                    ->title(__('Название')),

                Input::make('rwLibCurrency.lcur_code')
                    ->title(__('Код')),

                Input::make('rwLibCurrency.lcur_symbol')
                    ->title(__('Символ')),

                Button::make(__('Сохранить'))
                    ->type(Color::DARK)
                    ->style('margin-bottom: 20px;')
                    ->method('saveCurrency'),

            ]),
        ];
    }


    function saveCurrency(Request $request)
    {

        $request->validate([
            'rwLibCurrency.lcur_id'    => 'nullable|integer',
            'rwLibCurrency.lcur_name'  => 'required|string|max:30',
            'rwLibCurrency.lcur_code'  => 'required|string|max:6',
            'rwLibCurrency.lcur_symbol' => 'required|string|max:5',
        ]);

        if (isset($request->cmLibCurrency['lcur_id']) && $request->cmLibCurrency['lcur_id'] > 0) {

            rwLibCurrency::where('lcur_id', $request->cmLibCurrency['lcur_id'])->update([
                'lcur_code' => $request->cmLibCurrency['lcur_code'],
                'lcur_name' => $request->cmLibCurrency['lcur_name'],
                'lcur_symbol' => $request->cmLibCurrency['lcur_symbol'],
            ]);

            Alert::success(__('Данные успешно отредактированы!'));

        } else {

            rwLibCurrency::insert([
                'lcur_code' => $request->cmLibCurrency['lcur_code'],
                'lcur_name' => $request->cmLibCurrency['lcur_name'],
                'lcur_symbol' => $request->cmLibCurrency['lcur_symbol'],
            ]);

            Alert::success(__('Данные успешно добавлены!'));

        }


        return redirect()->route('platform.settings.currencies.index');
    }
}
