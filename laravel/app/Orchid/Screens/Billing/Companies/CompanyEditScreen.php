<?php

namespace App\Orchid\Screens\Billing\Companies;

use App\Models\rwCompany;
use App\Models\rwLibCity;
use App\Models\rwLibCountry;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Layout;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout as OrchidLayout;
use Orchid\Screen\Fields\Relation;
use Illuminate\Http\Request;

class CompanyEditScreen extends Screen
{
    public ?rwCompany $company = null;

    public function query(rwCompany $company): array
    {
        return [
            'company' => $company
        ];
    }

    public function name(): string
    {
        return $this->company->exists
            ? CustomTranslator::get('Редактировать компанию')
            : CustomTranslator::get('Создать компанию');
    }

    public function commandBar(): array
    {
        return [];
    }

    public function layout(): array
    {
        return [
            OrchidLayout::rows([
                Input::make('company.co_name')->title(CustomTranslator::get('Название'))->required(),
                Input::make('company.co_legal_name')->title(CustomTranslator::get('Юридическое название')),
                Input::make('company.co_vat_number')->title(CustomTranslator::get('ИНН / VAT')),
                Input::make('company.co_registration_number')->title(CustomTranslator::get('Регистрационный номер')),

                Relation::make('company.co_country_id')
                    ->fromModel(rwLibCountry::class, 'lco_name')
                    ->title(CustomTranslator::get('Страна')),

                Relation::make('company.co_city_id')
                    ->fromModel(rwLibCity::class, 'lcit_name')
                    ->title(CustomTranslator::get('Город')),

                Input::make('company.co_postcode')->title(CustomTranslator::get('Индекс')),
                Input::make('company.co_address')->title(CustomTranslator::get('Адрес')),
                Input::make('company.co_phone')->title(CustomTranslator::get('Телефон')),
                Input::make('company.co_email')->title(CustomTranslator::get('Email')),
                Input::make('company.co_website')->title(CustomTranslator::get('Сайт')),
                Input::make('company.co_bank_account')->title(CustomTranslator::get('Расчётный счёт')),
                Input::make('company.co_bank_ks')->title(CustomTranslator::get('Кор.счет')),
                Input::make('company.co_bank_name')->title(CustomTranslator::get('Банк')),
                Input::make('company.co_swift_bic')->title(CustomTranslator::get('SWIFT/BIC')),
                Input::make('company.co_contact_person')->title(CustomTranslator::get('Контактное лицо')),

                Button::make(CustomTranslator::get('Сохранить'))
                    ->icon('check')
                    ->type(Color::DARK)
                    ->style('margin-bottom: 20px;')
                    ->method('save'),
            ])
        ];
    }

    public function save(rwCompany $company, Request $request)
    {
        $data = $request->get('company');
        $data['co_domain_id'] = Auth::user()->domain_id;
        $company->fill($data)->save();

        return redirect()->route('platform.billing.companies.list');
    }
}

