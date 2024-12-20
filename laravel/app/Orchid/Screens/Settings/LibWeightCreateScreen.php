<?php

namespace App\Orchid\Screens\Settings;

use App\Models\rwLibLength;
use App\Models\rwLibWeight;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class LibWeightCreateScreen extends Screen
{
    public $whId = 0;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query($whId = 0): iterable
    {
        $this->whId = $whId;

        if ($whId == 0) {
            return [];
        } else {
            return [
                'rwLibWeight' => rwLibWeight::where('lw_id', $whId)->first(),
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
        return $this->whId > 0 ? __('Редактирование единицы веса') : __('Создание новой единицы веса');
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

                Input::make('rwLibWeight.lw_id')
                    ->type('hidden')
                    ->width(50),

                Input::make('rwLibWeight.lw_name')
                    ->width(50)
                    ->title(__('Название')),

                Input::make('rwLibWeight.lw_unit')
                    ->title(__('Код')),

                Button::make(__('Сохранить'))
                    ->type(Color::DARK)
                    ->style('margin-bottom: 20px;')
                    ->method('saveWeight'),

            ]),
        ];
    }

    function saveWeight(Request $request)
    {

        $request->validate([
            'rwLibWeight.lw_id' => 'nullable|integer',
            'rwLibWeight.lw_name' => 'required|string|max:100',
            'rwLibWeight.lw_unit' => 'required|string|max:6',
        ]);

        if (isset($request->cmLibWeight['lw_id']) && $request->cmLibWeight['lw_id'] > 0) {

            rwLibWeight::where('lw_id', $request->cmLibWeight['lw_id'])->update([
                'lw_unit' => $request->cmLibWeight['lw_unit'],
                'lw_name' => $request->cmLibWeight['lw_name'],
            ]);

            Alert::success(__('Данные успешно отредактированы!'));

        } else {

            rwLibWeight::insert([
                'lw_unit' => $request->cmLibWeight['lw_unit'],
                'lw_name' => $request->cmLibWeight['lw_name'],
            ]);

            Alert::success(__('Данные успешно добавлены!'));

        }


        return redirect()->route('platform.settings.weight');
    }
}
