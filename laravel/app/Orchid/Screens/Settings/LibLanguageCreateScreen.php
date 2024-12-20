<?php

namespace App\Orchid\Screens\Settings;

use App\Models\rwLibLanguage;
use App\Models\rwLibLength;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class LibLanguageCreateScreen extends Screen
{
    public $langId = 0;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query($langId = 0): iterable
    {
        $this->langId = $langId;

        if ($langId == 0) {
            return [];
        } else {
            return [
                'rwLibLanguage' => rwLibLanguage::where('llang_id', $langId)->first(),
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
        return $this->langId > 0 ? __('Редактирование языка') : __('Создание нового языка');
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

                Input::make('rwLibLanguage.llang_id')
                    ->type('hidden')
                    ->width(50),

                Input::make('rwLibLanguage.llang_name')
                    ->width(50)
                    ->title(__('Название')),

                Input::make('rwLibLanguage.llang_code')
                    ->title(__('Код')),

                Button::make(__('Сохранить'))
                    ->type(Color::DARK)
                    ->style('margin-bottom: 20px;')
                    ->method('saveLang'),

            ]),
        ];
    }


    function saveLang(Request $request)
    {

        $request->validate([
            'rwLibLanguage.llang_id' => 'nullable|integer',
            'rwLibLanguage.llang_name' => 'required|string|max:100',
            'rwLibLanguage.llang_code' => 'required|string|max:6',
        ]);

        if (isset($request->cmLibLanguage['llang_id']) && $request->cmLibLanguage['llang_id'] > 0) {

            rwLibLanguage::where('llang_id', $request->cmLibLanguage['llang_id'])->update([
                'llang_code' => $request->cmLibLanguage['llang_code'],
                'llang_name' => $request->cmLibLanguage['llang_name'],
            ]);

            Alert::success(__('Данные успешно отредактированы!'));

        } else {

            rwLibLanguage::insert([
                'llang_code' => $request->cmLibLanguage['llang_code'],
                'llang_name' => $request->cmLibLanguage['llang_name'],
            ]);

            Alert::success(__('Данные успешно добавлены!'));

        }


        return redirect()->route('platform.settings.languages');
    }
}
