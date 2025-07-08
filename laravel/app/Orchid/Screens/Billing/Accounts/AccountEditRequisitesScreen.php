<?php

namespace App\Orchid\Screens\Billing\Accounts;

use App\Models\rwBillingTransactions;
use App\Models\rwWarehouse;
use App\Orchid\Layouts\Billings\Accounts\AccountNavigation;
use App\Services\CustomTranslator;
use Orchid\Screen\Screen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Orchid\Screen\Sight;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class AccountEditRequisitesScreen extends Screen
{
    private $whId;

    public function query($whId): iterable
    {
        $this->whId = $whId;
        $transactionsList = [];
        $companyRequisites = [];
        $executorRequisites = [];

        $currentUser = Auth::user();

        $dbWhList = rwWarehouse::where('wh_id', $this->whId);

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {


        } else {

            $dbWhList = $dbWhList->where('wh_user_id', $currentUser->id);

        }

        $resWhList = $dbWhList->first();

        if (isset($resWhList->wh_id)) {

            $companyRequisites = $resWhList->getCompany;
            $executorRequisites = $resWhList->getParent->getCompany;

        }

        return [
            'companyRequisites' => $companyRequisites,
            'executorRequisites' => $executorRequisites,
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Список транзакций по складу');
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
            AccountNavigation::class,

            Layout::columns([
                // --- левая колонка: клиент -----------------
                Layout::legend('companyRequisites', [
                            Sight::make('co_name', CustomTranslator::get('Название')),
                            Sight::make('co_legal_name', CustomTranslator::get('Юридическое название')),
                            Sight::make('co_vat_number', CustomTranslator::get('ИНН / VAT')),
                            Sight::make('co_vat_availability', CustomTranslator::get('НДС'))
                                ->render(fn($c) => $c->co_vat_availability ? 'С НДС' : 'Без НДС'),
                            Sight::make('co_vat_proc', CustomTranslator::get('Ставка НДС'))
                                ->render(fn($c) => $c->co_vat_proc . ' %'),
                            Sight::make('co_registration_number', CustomTranslator::get('Регистрационный номер')),
                            Sight::make('co_country_id', CustomTranslator::get('Страна')),
                            Sight::make('co_city_id', CustomTranslator::get('Город'))
                                ->render(fn($c) => $c->getCity->lcit_name ?? '-'),
                            Sight::make('co_postcode', CustomTranslator::get('Индекс')),
                            Sight::make('co_address', CustomTranslator::get('Адрес')),
                            Sight::make('co_phone', CustomTranslator::get('Телефон')),
                            Sight::make('co_email', CustomTranslator::get('Email')),
                            Sight::make('co_website', CustomTranslator::get('Сайт')),
                            Sight::make('co_bank_account', CustomTranslator::get('Расчётный счёт')),
                            Sight::make('co_bank_ks', CustomTranslator::get('Кор.счёт')),
                            Sight::make('co_bank_name', CustomTranslator::get('Банк')),
                            Sight::make('co_swift_bic', CustomTranslator::get('SWIFT/BIC')),
                            Sight::make('co_contact_person', CustomTranslator::get('Контактное лицо')),
                ])->title('Реквизиты клиента'),

                // --- правая колонка: исполнитель ----------
                Layout::legend('executorRequisites', [
                            Sight::make('co_name', CustomTranslator::get('Название')),
                            Sight::make('co_legal_name', CustomTranslator::get('Юридическое название')),
                            Sight::make('co_vat_number', CustomTranslator::get('ИНН / VAT')),
                            Sight::make('co_vat_availability', CustomTranslator::get('НДС'))
                                ->render(fn($c) => $c->co_vat_availability ? 'С НДС' : 'Без НДС'),
                            Sight::make('co_vat_proc', CustomTranslator::get('Ставка НДС'))
                                ->render(fn($c) => $c->co_vat_proc . ' %'),
                            Sight::make('co_registration_number', CustomTranslator::get('Регистрационный номер')),
                            Sight::make('co_country_id', CustomTranslator::get('Страна')),
                            Sight::make('co_city_id', CustomTranslator::get('Город'))
                                ->render(fn($c) => $c->getCity->lcit_name ?? '-'),
                            Sight::make('co_postcode', CustomTranslator::get('Индекс')),
                            Sight::make('co_address', CustomTranslator::get('Адрес')),
                            Sight::make('co_phone', CustomTranslator::get('Телефон')),
                            Sight::make('co_email', CustomTranslator::get('Email')),
                            Sight::make('co_website', CustomTranslator::get('Сайт')),
                            Sight::make('co_bank_account', CustomTranslator::get('Расчётный счёт')),
                            Sight::make('co_bank_ks', CustomTranslator::get('Кор.счёт')),
                            Sight::make('co_bank_name', CustomTranslator::get('Банк')),
                            Sight::make('co_swift_bic', CustomTranslator::get('SWIFT/BIC')),
                            Sight::make('co_contact_person', CustomTranslator::get('Контактное лицо')),
                ])->title('Реквизиты исполнителя'),

            ]),
        ];
    }
}
