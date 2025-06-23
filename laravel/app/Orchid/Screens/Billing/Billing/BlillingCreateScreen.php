<?php

namespace App\Orchid\Screens\Billing\Billing;

use App\Models\rwBillingSetting;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Actions\Button;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Alert;


class BlillingCreateScreen extends Screen
{
    public $name = 'Создать настройку биллинга';

    public function query(): iterable
    {
        return [];
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        $domainId = Auth::user()->domain_id;

        return [
            Layout::rows([
                Input::make('billing.bs_name')->title('Название')->required(),
                Input::make('billing.bs_domain_id')
                    ->type('hidden')
                    ->value($domainId),
                DateTimer::make('billing.bs_data')->title('Дата')->format('Y-m-d')->value(date('Y-m-d'))->required(),
                Button::make('Сохранить')->method('save')->type(Color::DARK),
            ]),
        ];
    }

    public function save(Request $request)
    {
        $resBilling = rwBillingSetting::create($request->input('billing'));

        Alert::error(CustomTranslator::get('Биллинг создан!'));
        return redirect()->route('platform.billing.billing.edit', ['billing' => $resBilling->bs_id]);
    }
}
