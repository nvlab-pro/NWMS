<?php

namespace App\Orchid\Screens\Integrations;

use App\Models\rwDeliveryService;
use App\Models\rwIntegration;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;

class IntegrationCreateScreen extends Screen
{
    public $integration;

    public function query(rwIntegration $integration): iterable
    {
        return [
            'integration' => $integration,
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Создать интеграцию');
    }

    public function commandBar(): iterable
    {
        return [
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('integration.int_name')
                    ->title(CustomTranslator::get('Название'))
                    ->required(),

                Input::make('integration.int_url')
                    ->title(CustomTranslator::get('URL'))
                    ->required(),

                Input::make('integration.int_token')
                    ->title(CustomTranslator::get('Токен'))
                    ->required(),

                Select::make('integration.int_ds_id')
                    ->title(CustomTranslator::get('Служба доставки'))
                    ->fromModel(rwDeliveryService::query()->get(), 'ds_name', 'ds_id')
                    ->required(),

                Button::make(CustomTranslator::get('Создать'))
                    ->type(Color::DARK)
                    ->style('margin-bottom: 20px;')
                    ->icon('plus')
                    ->method('save'),
            ]),
        ];
    }

    public function save(rwIntegration $integration, Request $request)
    {
        $integration->fill($request->get('integration'));
        $integration->int_domain_id = Auth::user()->domain_id;
        $integration->int_user_id = Auth::id();
        $integration->save();

        return redirect()->route('platform.delivery-services.integrations.list');
    }

}