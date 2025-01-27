<?php

declare(strict_types=1);

use App\Orchid\Screens\Examples\ExampleActionsScreen;
use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Examples\ExampleGridScreen;
use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\Settings\LibCityCreateScreen;
use App\Orchid\Screens\Settings\LibCityScreen;
use App\Orchid\Screens\Settings\LibCountryCreateScreen;
use App\Orchid\Screens\Settings\LibCountryScreen;
use App\Orchid\Screens\Settings\LibCurrencyCreateScreen;
use App\Orchid\Screens\Settings\LibCurrencyScreen;
use App\Orchid\Screens\Settings\LibLanguageCreateScreen;
use App\Orchid\Screens\Settings\LibLanguageScreen;
use App\Orchid\Screens\Settings\LibLengthCreateScreen;
use App\Orchid\Screens\Settings\LibLengthScreen;
use App\Orchid\Screens\Settings\LibWeightCreateScreen;
use App\Orchid\Screens\Settings\LibWeightScreen;
use App\Orchid\Screens\Settings\SettingScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// ******************************************************
// *** Список товаров
// *** Platform > offers
// ******************************************************

Route::screen('offers', \App\Orchid\Screens\Offers\OffersScreen::class)
    ->name('platform.offers.index')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Список товаров'), route('platform.offers.index')));

Route::screen('offers/create', \App\Orchid\Screens\Offers\OffersCreateScreen::class)
    ->name('platform.offers.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.offers.index')
        ->push(__('Создание нового товара'), route('platform.offers.create')));

Route::screen('offers/{offerId}/edit', \App\Orchid\Screens\Offers\OffersCreateScreen::class)
    ->name('platform.offers.edit')
    ->breadcrumbs(fn(Trail $trail, $offerId) => $trail
        ->parent('platform.offers.index')
        ->push(__('Создание нового товара'), route('platform.offers.edit', $offerId)));


// ******************************************************
// *** Список товаров
// *** Platform > orders
// ******************************************************

Route::screen('orders', \App\Orchid\Screens\Orders\OrdersScreen::class)
    ->name('platform.orders.index')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Список заказов'), route('platform.orders.index')));

// ******************************************************
// *** Список магазинов
// *** Platform > shops
// ******************************************************

Route::screen('shops', \App\Orchid\Screens\Shops\ShopsScreen::class)
    ->name('platform.shops.index')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Магазины'), route('platform.shops.index')));

Route::screen('shops/create', \App\Orchid\Screens\Shops\ShopsCreateScreen::class)
    ->name('platform.shops.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Магазины'), route('platform.shops.create')));

Route::screen('shops/{shopId}/edit', \App\Orchid\Screens\Shops\ShopsCreateScreen::class)
    ->name('platform.shops.edit')
    ->breadcrumbs(fn(Trail $trail, $shopId) => $trail
        ->parent('platform.shops.index')
        ->push(__('Магазины'), route('platform.shops.edit', $shopId)));

// ******************************************************
// *** Список складов
// *** Platform > warehouses
// ******************************************************

Route::screen('warehouses', \App\Orchid\Screens\Warehouses\WarehouseScreen::class)
    ->name('platform.warehouses.index')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Список складов'), route('platform.warehouses.index')));

Route::screen('warehouses/create', \App\Orchid\Screens\Warehouses\WarehouseCreateScreen::class)
    ->name('platform.warehouses.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.warehouses.index')
        ->push(__('Список складов'), route('platform.warehouses.create')));

Route::screen('warehouses/{whId}/edit', \App\Orchid\Screens\Warehouses\WarehouseCreateScreen::class)
    ->name('platform.warehouses.edit')
    ->breadcrumbs(fn(Trail $trail, $whId) => $trail
        ->parent('platform.warehouses.index')
        ->push(__('Список складов'), route('platform.warehouses.edit', $whId)));

// ******************************************************
// *** Список складов
// *** Platform > acceptances
// ******************************************************

Route::screen('acceptances', \App\Orchid\Screens\Acceptances\AcceptancesScreen::class)
    ->name('platform.acceptances.index')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Список накладных'), route('platform.acceptances.index')));

Route::screen('acceptances/create', \App\Orchid\Screens\Acceptances\AcceptanceCreateScreen::class)
    ->name('platform.acceptances.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.acceptances.index')
        ->push(__('Список накладных'), route('platform.acceptances.create')));

Route::screen('acceptances/{acceptId}/edit', \App\Orchid\Screens\Acceptances\AcceptanceCreateScreen::class)
    ->name('platform.acceptances.edit')
    ->breadcrumbs(fn(Trail $trail, $acceptId) => $trail
        ->parent('platform.acceptances.index')
        ->push(__('Список накладных'), route('platform.acceptances.edit', $acceptId)));

Route::screen('acceptances/{acceptId}/offers', \App\Orchid\Screens\Acceptances\AcceptancesOffersScreen::class)
    ->name('platform.acceptances.offers')
    ->breadcrumbs(fn(Trail $trail, $acceptId) => $trail
        ->parent('platform.acceptances.index')
        ->push(__('Список накладных'), route('platform.acceptances.offers', $acceptId)));

// Route::post('acceptances/{acceptId}/offers/save', [\App\Orchid\Screens\Acceptances\AcceptancesOffersScreen::class, 'save'])
//    ->name('platform.acceptances.offers.save');

// ******************************************************
// *** Настройки системы
// *** Platform > Settings
// ******************************************************

Route::screen('settings', SettingScreen::class)
    ->name('platform.settings.index')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Настройки'), route('platform.settings.index')));

Route::prefix('settings')
    ->name('platform.settings')
    ->group(function () {

        // Страны
        Route::screen('countries', LibCountryScreen::class)
            ->name('.countries.index')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.index')
                ->push(__('Страны'), route('platform.settings.countries.index')));

        Route::screen('countries/create', LibCountryCreateScreen::class)
            ->name('.countries.create')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.countries.index')
                ->push(__('Создание новой страны'), route('platform.settings.countries.create')));

        Route::screen('countries/{countryId}/edit', LibCountryCreateScreen::class)
            ->name('.countries.edit')
            ->breadcrumbs(fn(Trail $trail, $countryId) => $trail
                ->parent('platform.settings.countries.index')
                ->push(__('Редактирование страны'), route('platform.settings.countries.edit', $countryId)));

        // Города
        Route::screen('cities', LibCityScreen::class)
            ->name('.cities.index')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.index')
                ->push(__('Города'), route('platform.settings.cities.index')));

        Route::screen('cities/create', LibCityCreateScreen::class)
            ->name('.cities.create')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.cities.index')
                ->push(__('Создание нового города'), route('platform.settings.cities.create')));

        Route::screen('cities/{cityId}/edit', LibCityCreateScreen::class)
            ->name('.cities.edit')
            ->breadcrumbs(fn(Trail $trail, $cityId) => $trail
                ->parent('platform.settings.cities.index')
                ->push(__('Редактирование города'), route('platform.settings.cities.edit', $cityId)));

        // Валюты
        Route::screen('currencies', LibCurrencyScreen::class)
            ->name('.currencies.index')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.index')
                ->push(__('Валюта'), route('platform.settings.currencies.index')));

        Route::screen('currencies/create', LibCurrencyCreateScreen::class)
            ->name('.currencies.create')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.currencies.index')
                ->push(__('Создание новой валюты'), route('platform.settings.currencies.create')));

        Route::screen('currencies/{curId}/edit', LibCurrencyCreateScreen::class)
            ->name('.currencies.edit')
            ->breadcrumbs(fn(Trail $trail, $langId) => $trail
                ->parent('platform.settings.currencies.index')
                ->push(__('Редактирование валюты'), route('platform.settings.currencies.edit', $langId)));

        // Языки
        Route::screen('languages', LibLanguageScreen::class)
            ->name('.languages.index')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.index')
                ->push(__('Языки'), route('platform.settings.languages.index')));

        Route::screen('languages/create', LibLanguageCreateScreen::class)
            ->name('.languages.create')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.languages.index')
                ->push(__('Создание новой единицы веса'), route('platform.settings.languages.create')));

        Route::screen('languages/{langId}/edit', LibLanguageCreateScreen::class)
            ->name('.languages.edit')
            ->breadcrumbs(fn(Trail $trail, $langId) => $trail
                ->parent('platform.settings.languages.index')
                ->push(__('Редактирование единицы веса'), route('platform.settings.languages.edit', $langId)));

        // Единицы размера
        Route::screen('weight', LibWeightScreen::class)
            ->name('.weight.index')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.index')
                ->push(__('Веса'), route('platform.settings.weight.index')));

        Route::screen('weight/create', LibWeightCreateScreen::class)
            ->name('.weight.create')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.weight.index')
                ->push(__('Создание новой единицы веса'), route('platform.settings.weight.create')));

        Route::screen('weight/{whId}/edit', LibWeightCreateScreen::class)
            ->name('.weight.edit')
            ->breadcrumbs(fn(Trail $trail, $whId) => $trail
                ->parent('platform.settings.weight.index')
                ->push(__('Редактирование единицы веса'), route('platform.settings.weight.edit', $whId)));

        // Единицы размера
        Route::screen('length', LibLengthScreen::class)
            ->name('.length.index')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.index')
                ->push(__('Размеры'), route('platform.settings.length.index')));

        Route::screen('length/create', LibLengthCreateScreen::class)
            ->name('.length.create')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.length.index')
                ->push(__('Создание новой единицы измерения'), route('platform.settings.length.create')));

        Route::screen('length/{libId}/edit', LibLengthCreateScreen::class)
            ->name('.length.edit')
            ->breadcrumbs(fn(Trail $trail, $lenId) => $trail
                ->parent('platform.settings.length.index')
                ->push(__('Редактирование единицы измерения'), route('platform.settings.length.edit', $lenId)));

    });

Route::prefix('delivery-services')
    ->name('platform.delivery-services')
    ->group(function () {

        // Страны
        Route::screen('list', \App\Orchid\Screens\DeliveryServices\DeliveryServicesScreen::class)
            ->name('.list')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.index')
                ->push(__('Службы доставки')),
            );

        Route::screen('create', \App\Orchid\Screens\DeliveryServices\DeliveryServicesCreateScreen::class)
            ->name('.create')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.delivery-services.list')
                ->push(__('Создание службы доставки'), route('platform.delivery-services.create')));

        Route::screen('{dsId}/edit', \App\Orchid\Screens\DeliveryServices\DeliveryServicesCreateScreen::class)
            ->name('.edit')
            ->breadcrumbs(fn(Trail $trail, $dsId) => $trail
                ->parent('platform.delivery-services.list')
                ->push(__('Редактирование службы доставки'), route('platform.delivery-services.edit', $dsId)));

    });

// **************************************************
// *** Терминал
// **************************************************

Route::prefix('terminal')
    ->name('platform.terminal')
    ->group(function () {

        // Териминал -> Главная старница терминала
        Route::screen('main', \App\Orchid\Screens\terminal\mainScreen::class)
            ->name('.main')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.index')
                ->push(__('Терминал'), route('platform.terminal.main')),
            );

        // Териминал -> Приемка товара (выбор товара)
        Route::screen('acceptance/select', \App\Orchid\Screens\terminal\Acceptance\SelectAcceptanceScreen::class)
            ->name('.acceptance.select')
            ->breadcrumbs(function (Trail $trail) {
                return $trail
                    ->parent('platform.terminal.main')
                    ->push('Выбор приемки', route('platform.terminal.acceptance.select'));
            });

        // Териминал -> Приемка товара (основной экран)
        Route::screen('acceptance/{docId}/scan', \App\Orchid\Screens\terminal\Acceptance\ScanAcceptScreen::class)
            ->name('.acceptance.scan')
            ->breadcrumbs(function (Trail $trail, $docId) {
                return $trail
                    ->parent('platform.terminal.acceptance.select')
                    ->push('Приемка', route('platform.terminal.acceptance.scan', $docId));
            });

    });

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn(Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn(Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

// Example...
Route::screen('example', ExampleScreen::class)
    ->name('platform.example')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Example Screen'));

Route::screen('/examples/form/fields', ExampleFieldsScreen::class)->name('platform.example.fields');
Route::screen('/examples/form/advanced', ExampleFieldsAdvancedScreen::class)->name('platform.example.advanced');
Route::screen('/examples/form/editors', ExampleTextEditorsScreen::class)->name('platform.example.editors');
Route::screen('/examples/form/actions', ExampleActionsScreen::class)->name('platform.example.actions');

Route::screen('/examples/layouts', ExampleLayoutsScreen::class)->name('platform.example.layouts');
Route::screen('/examples/grid', ExampleGridScreen::class)->name('platform.example.grid');
Route::screen('/examples/charts', ExampleChartsScreen::class)->name('platform.example.charts');
Route::screen('/examples/cards', ExampleCardsScreen::class)->name('platform.example.cards');

//Route::screen('idea', Idea::class, 'platform.screens.idea');
