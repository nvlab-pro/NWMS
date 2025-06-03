<?php

namespace App\Orchid\Screens\Billing\Companies;

use App\Models\rwCompany;
use App\Models\rwLibCity;
use App\Models\rwLibCountry;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;

class CompaniesScreen extends Screen
{
    public function name(): string
    {
        return CustomTranslator::get('Компании');
    }

    public function query(): array
    {
        return [
            'companies' => rwCompany::filters()
                ->where('co_domain_id', Auth::user()->domain_id)
                ->paginate(20),
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make(CustomTranslator::get('Добавить компанию'))
                ->icon('plus')
                ->route('platform.billing.companies.create'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::table('companies', [
                TD::make('co_name', CustomTranslator::get('Название'))
                    ->sort()
                    ->filter()
                    ->render(function (rwCompany $modelName) {
                        return Link::make($modelName->co_name)
                            ->route('platform.billing.companies.edit', $modelName->co_id);
                    }),

                TD::make('co_vat_number', CustomTranslator::get('ИНН / VAT'))
                    ->sort()
                    ->filter()
                    ->render(function (rwCompany $modelName) {
                        return Link::make($modelName->co_vat_number)
                            ->route('platform.billing.companies.edit', $modelName->co_id);
                    }),

                TD::make('co_email', CustomTranslator::get('Email'))
                    ->render(function (rwCompany $modelName) {
                        return Link::make($modelName->co_email)
                            ->route('platform.billing.companies.edit', $modelName->co_id);
                    }),

                TD::make('co_phone', CustomTranslator::get('Телефон'))
                    ->render(function (rwCompany $modelName) {
                        return Link::make($modelName->co_phone)
                            ->route('platform.billing.companies.edit', $modelName->co_id);
                    }),

                TD::make('co_city_id', CustomTranslator::get('Город'))
                    ->render(function (rwCompany $modelName) {
                        return Link::make($modelName->co_email)
                            ->route('platform.billing.companies.edit', $modelName->co_id);
                    }),
//                    ->render(fn($model) => optional(rwLibCity::find($model->co_city_id))->lcit_name)

                TD::make('Actions', CustomTranslator::get('Действия'))
                    ->align(TD::ALIGN_CENTER)
                    ->render(fn($company) => Link::make(CustomTranslator::get('Редактировать'))
                        ->route('platform.billing.companies.edit', $company->co_id)
                        ->icon('pencil')),
            ]),
        ];
    }
}