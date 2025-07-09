<?php

namespace App\Orchid\Screens\Billing\Accounts;

use App\Models\rwBillingTransactions;
use App\Models\rwWarehouse;
use App\Orchid\Layouts\Billings\Accounts\AccountNavigation;
use App\Services\CustomTranslator as CT;
use Orchid\Screen\Fields\Label;
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
    private bool $hasCompanyRequisites = false;
    private bool $hasExecutorRequisites = false;

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

            $companyRequisites = $resWhList->getCompany ?? null;
            $executorRequisites = $resWhList->getParent->getCompany ?? null;

        }

        $this->hasCompanyRequisites = isset($companyRequisites->co_id);
        $this->hasExecutorRequisites = isset($executorRequisites->co_id);

        return [
            'companyRequisites' => $companyRequisites,
            'executorRequisites' => $executorRequisites,
            'hasCompanyRequisites' => is_object($companyRequisites),
            'hasExecutorRequisites' => is_object($executorRequisites),
        ];
    }

    public function name(): ?string
    {
        return CT::get('Список транзакций по складу');
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
                            Sight::make('co_name', CT::get('Название'))->canSee($this->hasCompanyRequisites),
                            Sight::make('co_legal_name', CT::get('Юридическое название'))->canSee($this->hasCompanyRequisites),
                            Sight::make('co_vat_number', CT::get('ИНН / VAT'))->canSee($this->hasCompanyRequisites),
                            Sight::make('co_vat_availability', CT::get('НДС'))
                                ->render(fn($c) => $c->co_vat_availability ? 'С НДС' : 'Без НДС')->canSee($this->hasCompanyRequisites),
                            Sight::make('co_vat_proc', CT::get('Ставка НДС'))
                                ->render(fn($c) => $c->co_vat_proc . ' %')->canSee($this->hasCompanyRequisites),
                            Sight::make('co_registration_number', CT::get('Регистрационный номер'))->canSee($this->hasCompanyRequisites),
                            Sight::make('co_country_id', CT::get('Страна'))->canSee($this->hasCompanyRequisites),
                            Sight::make('co_city_id', CT::get('Город'))
                                ->render(fn($c) => $c->getCity->lcit_name ?? '-')->canSee($this->hasCompanyRequisites),
                            Sight::make('co_postcode', CT::get('Индекс'))->canSee($this->hasCompanyRequisites),
                            Sight::make('co_address', CT::get('Адрес'))->canSee($this->hasCompanyRequisites),
                            Sight::make('co_phone', CT::get('Телефон'))->canSee($this->hasCompanyRequisites),
                            Sight::make('co_email', CT::get('Email'))->canSee($this->hasCompanyRequisites),
                            Sight::make('co_website', CT::get('Сайт'))->canSee($this->hasCompanyRequisites),
                            Sight::make('co_bank_account', CT::get('Расчётный счёт'))->canSee($this->hasCompanyRequisites),
                            Sight::make('co_bank_ks', CT::get('Кор.счёт'))->canSee($this->hasCompanyRequisites),
                            Sight::make('co_bank_name', CT::get('Банк'))->canSee($this->hasCompanyRequisites),
                            Sight::make('co_swift_bic', CT::get('SWIFT/BIC'))->canSee($this->hasCompanyRequisites),
                            Sight::make('co_contact_person', CT::get('Контактное лицо'))->canSee($this->hasCompanyRequisites),

                            Sight::make('')->render(fn () => CT::get('Реквизиты склада клиента не заданы. Пожалуйста, проверьте настройки.'))
                                ->canSee(!$this->hasCompanyRequisites)

                ])->title('Реквизиты клиента'),

                // --- правая колонка: исполнитель ----------
                Layout::legend('executorRequisites', [
                            Sight::make('co_name', CT::get('Название'))->canSee($this->hasExecutorRequisites),
                            Sight::make('co_legal_name', CT::get('Юридическое название'))->canSee($this->hasExecutorRequisites),
                            Sight::make('co_vat_number', CT::get('ИНН / VAT'))->canSee($this->hasExecutorRequisites),
                            Sight::make('co_vat_availability', CT::get('НДС'))
                                ->render(fn($c) => $c->co_vat_availability ? 'С НДС' : 'Без НДС')->canSee($this->hasExecutorRequisites),
                            Sight::make('co_vat_proc', CT::get('Ставка НДС'))
                                ->render(fn($c) => $c->co_vat_proc . ' %')->canSee($this->hasExecutorRequisites),
                            Sight::make('co_registration_number', CT::get('Регистрационный номер'))->canSee($this->hasExecutorRequisites),
                            Sight::make('co_country_id', CT::get('Страна'))->canSee($this->hasExecutorRequisites),
                            Sight::make('co_city_id', CT::get('Город'))
                                ->render(fn($c) => $c->getCity->lcit_name ?? '-')->canSee($this->hasExecutorRequisites),
                            Sight::make('co_postcode', CT::get('Индекс'))->canSee($this->hasExecutorRequisites),
                            Sight::make('co_address', CT::get('Адрес'))->canSee($this->hasExecutorRequisites),
                            Sight::make('co_phone', CT::get('Телефон'))->canSee($this->hasExecutorRequisites),
                            Sight::make('co_email', CT::get('Email'))->canSee($this->hasExecutorRequisites),
                            Sight::make('co_website', CT::get('Сайт'))->canSee($this->hasExecutorRequisites),
                            Sight::make('co_bank_account', CT::get('Расчётный счёт'))->canSee($this->hasExecutorRequisites),
                            Sight::make('co_bank_ks', CT::get('Кор.счёт'))->canSee($this->hasExecutorRequisites),
                            Sight::make('co_bank_name', CT::get('Банк'))->canSee($this->hasExecutorRequisites),
                            Sight::make('co_swift_bic', CT::get('SWIFT/BIC'))->canSee($this->hasExecutorRequisites),
                            Sight::make('co_contact_person', CT::get('Контактное лицо'))->canSee($this->hasExecutorRequisites),

                            Sight::make('')->render(fn () => CT::get('Реквизиты склада клиента не заданы. Пожалуйста, проверьте настройки.'))
                                ->canSee(!$this->hasExecutorRequisites)

                ])->title('Реквизиты исполнителя')
                    ->canSee($this->hasExecutorRequisites),

            ]),
        ];
    }
}
