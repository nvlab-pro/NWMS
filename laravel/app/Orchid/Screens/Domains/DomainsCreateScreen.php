<?php

namespace App\Orchid\Screens\Domains;

use App\Models\rwDomain;
use App\Models\rwLibCountry;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class DomainsCreateScreen extends Screen
{
    public $domainId;

    public function query($domainId = 0): iterable
    {
        $this->domainId = $domainId;

        return [
            'rwDomain' => rwDomain::find($domainId) ?? new rwDomain(),
        ];
    }

    public function name(): ?string
    {
        return $this->domainId ? CustomTranslator::get('Редактировать домен') : CustomTranslator::get('Создать домен');
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('rwDomain.dm_name')
                    ->title(CustomTranslator::get('Домен'))
                    ->required()
                    ->maxlength(20),

                Select::make('rwDomain.dm_country_id')
                    ->title(CustomTranslator::get('Страна'))
                    ->fromModel(rwLibCountry::class, 'lco_name')
                    ->required(),

                Select::make('rwDomain.dm_timezone')
                    ->title(CustomTranslator::get('Часовой пояс'))
                    ->options($this->getTimezoneOptions())
                    ->empty(CustomTranslator::get('Не выбрано')),

                Button::make(CustomTranslator::get('Сохранить'))
                    ->icon('check')
                    ->method('save'),
            ]),
        ];
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'rwDomain.dm_name' => 'required|max:20',
            'rwDomain.dm_country_id' => 'required|integer',
            'rwDomain.dm_timezone' => 'nullable|string|max:64',
        ]);

        $data = $request->get('rwDomain');

        rwDomain::updateOrCreate(
            ['dm_id' => $this->domainId],
            $data
        );

        Alert::success(CustomTranslator::get('Сохранено'));

        return redirect()->route('platform.settings.domains');
    }

    private function getTimezoneOptions(): array
    {
        return collect(\DateTimeZone::listIdentifiers())
            ->mapWithKeys(fn($tz) => [$tz => $tz])
            ->toArray();
    }
}
