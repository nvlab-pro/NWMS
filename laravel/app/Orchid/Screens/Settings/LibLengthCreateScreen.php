<?php

namespace App\Orchid\Screens\Settings;

use App\Models\rwLibLength;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Alert;

class LibLengthCreateScreen extends Screen
{
    public $libId = 0;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query($libId = 0): iterable
    {
        $this->libId = $libId;

        if ($libId == 0) {
            return [];
        } else {
            return [
                'rwLibLength' => rwLibLength::where('llen_id', $libId)->first(),
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
        return $this->libId > 0 ? CustomTranslator::get('Редактирование единицы измерения') : CustomTranslator::get('Создание новой единицы измерения');
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

                Input::make('rwLibLength.llen_id')
                    ->type('hidden')
                    ->width(50),

                Input::make('rwLibLength.llen_name')
                    ->width(50)
                    ->title(CustomTranslator::get('Название')),

                Input::make('rwLibLength.llen_unit')
                    ->title(CustomTranslator::get('Код')),

                Button::make(CustomTranslator::get('Сохранить'))
                    ->type(Color::DARK)
                    ->style('margin-bottom: 20px;')
                    ->method('saveLength'),

            ]),
        ];
    }

    function saveLength(Request $request)
    {

        $request->validate([
            'rwLibLength.llen_id' => 'nullable|integer',
            'rwLibLength.llen_name' => 'required|string|max:100',
            'rwLibLength.llen_unit' => 'required|string|max:6',
        ]);

        if (isset($request->cmLibLength['llen_id']) && $request->cmLibLength['llen_id'] > 0) {

            rwLibLength::where('llen_id', $request->cmLibLength['llen_id'])->update([
                'llen_unit' => $request->cmLibLength['llen_unit'],
                'llen_name' => $request->cmLibLength['llen_name'],
            ]);

            Alert::success(CustomTranslator::get('Данные успешно отредактированы!'));

        } else {

            rwLibLength::insert([
                'llen_unit' => $request->cmLibLength['llen_unit'],
                'llen_name' => $request->cmLibLength['llen_name'],
            ]);

            Alert::success(CustomTranslator::get('Данные успешно добавлены!'));

        }


        return redirect()->route('platform.settings.length');
    }
}
