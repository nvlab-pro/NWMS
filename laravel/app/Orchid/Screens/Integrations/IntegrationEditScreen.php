<?php

namespace App\Orchid\Screens\Integrations;

use App\Models\rwIntegration;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;

class IntegrationEditScreen extends Screen
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
        return $this->integration->exists
            ? CustomTranslator::get('Редактировать интеграцию')
            : CustomTranslator::get('Создать интеграцию');
    }

    public function commandBar(): iterable
    {
        return [
            Button::make(CustomTranslator::get('Сохранить'))
                ->icon('check')
                ->method('save'),

            Button::make(CustomTranslator::get('Удалить'))
                ->icon('trash')
                ->method('remove')
                ->canSee($this->integration->exists),
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

                Select::make('integration.int_type')
                    ->title(CustomTranslator::get('Тип интеграции'))
                    ->options([
                        1 => 'Тип 1',
                        2 => 'Тип 2',
                        3 => 'Тип 3',
                    ])
                    ->required(),
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

    public function remove(rwIntegration $integration)
    {
        $integration->delete();

        return redirect()->route('platform.delivery-services.integrations.list');
    }
}