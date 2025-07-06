<?php

namespace App\Orchid\Screens\Billing\Companies;

use App\Models\rwCompany;
use App\Models\rwLibCity;
use App\Models\rwLibCountry;
use App\Models\rwWarehouse;
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
        $currentUser = Auth::user();
        $dbCompanies = rwCompany::where('co_domain_id', $currentUser->domain_id)->with('getCity');

//        if (!$currentUser->hasRole('admin')) {
//            if ($currentUser->hasRole('warehouse_manager') || $currentUser->hasRole('warehouse_worker')) {
//                $arWhList = rwWarehouse::where('wh_parent_id', $currentUser->wh_id)
//                    ->pluck('wh_id')
//                    ->toArray();
//                $dbOrders = $dbOrders->whereIn('o_wh_id', $arWhList);
//            } else {
//                $dbCompanies->where('getShop', );
//            }
//        }


        return [
            'companies' => $dbCompanies
                ->filters()
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
                    ->align(TD::ALIGN_CENTER)
                    ->render(function (rwCompany $modelName) {
                        return Link::make($modelName->co_vat_number)
                            ->route('platform.billing.companies.edit', $modelName->co_id);
                    }),

                TD::make('co_vat_availability', CustomTranslator::get('НДС'))
                    ->sort()
                    ->filter()
                    ->align(TD::ALIGN_CENTER)
                    ->render(function (rwCompany $modelName) {
                        if ($modelName->co_vat_availability == 0)
                            return Link::make(CustomTranslator::get('Без НДС'))
                                ->route('platform.billing.companies.edit', $modelName->co_id);
                        else
                            return Link::make($modelName->co_vat_proc . '%')
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
                        return Link::make($modelName->getCity->lcit_name ?? '-')
                            ->route('platform.billing.companies.edit', $modelName->co_id);
                    }),

                TD::make('Actions', CustomTranslator::get('Действия'))
                    ->align(TD::ALIGN_CENTER)
                    ->render(fn($company) => Link::make(CustomTranslator::get('Редактировать'))
                        ->route('platform.billing.companies.edit', $company->co_id)
                        ->icon('pencil')),
            ]),
        ];
    }
}