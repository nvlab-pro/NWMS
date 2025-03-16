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
use App\Services\CustomTranslator;
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
                ->push(CustomTranslator::get('Терминал'), route('platform.terminal.main')),
            );

        // Териминал -> Приемка товара (выбор товара)
        Route::screen('acceptance/select', \App\Orchid\Screens\terminal\Acceptance\SelectAcceptanceScreen::class)
            ->name('.acceptance.select')
            ->breadcrumbs(function (Trail $trail) {
                return $trail
                    ->parent('platform.terminal.main')
                    ->push(CustomTranslator::get('Выбор приемки'), route('platform.terminal.acceptance.select'));
            });

        // Териминал -> Приемка товара (основной экран)
        Route::screen('acceptance/{docId}/scan', \App\Orchid\Screens\terminal\Acceptance\ScanAcceptScreen::class)
            ->name('.acceptance.scan')
            ->breadcrumbs(function (Trail $trail, $docId) {
                return $trail
                    ->parent('platform.terminal.acceptance.select')
                    ->push(CustomTranslator::get('Приемка'), route('platform.terminal.acceptance.scan', $docId));
            });

        // Териминал -> Приемка товара (выбор товара)
        Route::screen('places/select', \App\Orchid\Screens\terminal\Places\SelectPlaceScreen::class)
            ->name('.places.select')
            ->breadcrumbs(function (Trail $trail) {
                return $trail
                    ->parent('platform.terminal.main')
                    ->push(CustomTranslator::get('Выбор приемки'), route('platform.terminal.places.select'));
            });

        // Териминал -> Приемка товара (выбор товара)
        Route::screen('places/{docId}/offer2place', \App\Orchid\Screens\terminal\Places\OfferToPlaceScreen::class)
            ->name('.places.offer2place.index')
            ->breadcrumbs(function (Trail $trail, $docId) {
                return $trail
                    ->parent('platform.terminal.main')
                    ->push(CustomTranslator::get('Место хранения'), route('platform.terminal.places.offer2place.index', $docId));
            });

        // Териминал -> Позаказная сборка (manually) -> Выбираем очередь
        Route::screen('soam/select', \App\Orchid\Screens\terminal\SOAM\SelectSOAMScreen::class)
            ->name('.soam.select')
            ->breadcrumbs(function (Trail $trail) {
                return $trail
                    ->parent('platform.terminal.main')
                    ->push(CustomTranslator::get('Очередь'), route('platform.terminal.soam.select'));
            });

        // Териминал -> Позаказная сборка (manually) -> Определяем местоположение кладовщика
        Route::screen('soam/{soaId}/{orderId}/order', \App\Orchid\Screens\terminal\SOAM\SelectOrderSOAMScreen::class)
            ->name('.soam.order')
            ->breadcrumbs(function (Trail $trail, $soaId, $orderId) {
                return $trail
                    ->parent('platform.terminal.soam.select')
                    ->push(CustomTranslator::get('Сборка'), route('platform.terminal.soam.order', [$soaId, $orderId]));
            });

        // Териминал -> Позаказная сборка (manually) -> Определяем местоположение кладовщика
        Route::screen('soam/{soaId}/{orderId}/offer', \App\Orchid\Screens\terminal\SOAM\GetOfferSOAMScreen::class)
            ->name('.soam.offer')
            ->breadcrumbs(function (Trail $trail, $soaId, $orderId) {
                return $trail
                    ->parent('platform.terminal.soam.select')
                    ->push(CustomTranslator::get('Сборка'), route('platform.terminal.soam.offer', [$soaId, $orderId]));
            });

        // Териминал -> Позаказная сборка (manually) -> Завершаем процесс сборки
        Route::screen('soam/{soaId}/{orderId}/finish', \App\Orchid\Screens\terminal\SOAM\FinishSOAMScreen::class)
            ->name('.soam.finish')
            ->breadcrumbs(function (Trail $trail, $soaId, $orderId) {
                return $trail
                    ->parent('platform.terminal.soam.select')
                    ->push(CustomTranslator::get('Сборка'), route('platform.terminal.soam.finish', [$soaId, $orderId]));
            });

        // Териминал -> Позаказная сборка (manually) -> Сборка закончена
        Route::screen('soam/{soaId}/{orderId}/end', \App\Orchid\Screens\terminal\SOAM\EndSOAMScreen::class)
            ->name('.soam.end')
            ->breadcrumbs(function (Trail $trail, $soaId, $orderId) {
                return $trail
                    ->parent('platform.terminal.soam.select')
                    ->push(CustomTranslator::get('Сборка'), route('platform.terminal.soam.end', [$soaId, $orderId]));
            });

        // Териминал -> Позаказная сборка -> Выбираем очередь
        Route::screen('soa/select', \App\Orchid\Screens\terminal\SOA\SelectSOAScreen::class)
            ->name('.soa.select')
            ->breadcrumbs(function (Trail $trail) {
                return $trail
                    ->parent('platform.terminal.main')
                    ->push(CustomTranslator::get('Очередь'), route('platform.terminal.soa.select'));
            });

        // Териминал -> Позаказная сборка -> Определяем местоположение кладовщика
        Route::screen('soa/{soaId}/{orderId}/location', \App\Orchid\Screens\terminal\SOA\FindUserLocationSOAScreen::class)
            ->name('.soa.location')
            ->breadcrumbs(function (Trail $trail, $soaId, $orderId) {
                return $trail
                    ->parent('platform.terminal.soa.select')
                    ->push(CustomTranslator::get('Сборка'), route('platform.terminal.soa.location', [$soaId, $orderId]));
            });

        // Териминал -> Позаказная сборка -> Сканируем место расположения товара
        Route::screen('soa/{soaId}/{orderId}/scan-place', \App\Orchid\Screens\terminal\SOA\ScanPlaceSOAScreen::class)
            ->name('.soa.scan.place')
            ->breadcrumbs(function (Trail $trail, $soaId, $orderId) {
                return $trail
                    ->parent('platform.terminal.soa.select')
                    ->push(CustomTranslator::get('Сборка'), route('platform.terminal.soa.scan.place', [$soaId, $orderId]));
            });

        // Териминал -> Позаказная сборка -> Сканируем товар
        Route::screen('soa/{soaId}/{orderId}/scan-offer', \App\Orchid\Screens\terminal\SOA\ScanOfferSOAScreen::class)
            ->name('.soa.scan.offer')
            ->breadcrumbs(function (Trail $trail, $soaId, $orderId) {
                return $trail
                    ->parent('platform.terminal.soa.select')
                    ->push(CustomTranslator::get('Сборка'), route('platform.terminal.soa.scan.offer', [$soaId, $orderId]));
            });

        // Териминал -> Позаказная сборка -> Берем товар с полки
        Route::screen('soa/{soaId}/{orderId}/get-offer', \App\Orchid\Screens\terminal\SOA\GetOfferSOAScreen::class)
            ->name('.soa.get.offer')
            ->breadcrumbs(function (Trail $trail, $soaId, $orderId) {
                return $trail
                    ->parent('platform.terminal.soa.select')
                    ->push(CustomTranslator::get('Сборка'), route('platform.terminal.soa.get.offer', [$soaId, $orderId]));
            });

        // Териминал -> Позаказная сборка
        Route::screen('soa/{soaId}/{orderId}/scan', \App\Orchid\Screens\terminal\SOA\ScanSOAScreen::class)
            ->name('.soa.scan')
            ->breadcrumbs(function (Trail $trail, $soaId, $orderId) {
                return $trail
                    ->parent('platform.terminal.soa.select')
                    ->push(CustomTranslator::get('Сборка'), route('platform.terminal.soa.scan', [$soaId, $orderId]));
            });

    });

// **************************************************
// *** Терминал
// **************************************************

Route::prefix('tables')
    ->name('platform.tables')
    ->group(function () {

        // Териминал -> Приемка товара (выбор товара)
        Route::screen('packing/queue', \App\Orchid\Screens\WorkTables\Packing\SelectPackingQueueScreen::class)
            ->name('.queue.select')
            ->breadcrumbs(function (Trail $trail) {
                return $trail
                    ->parent('platform.index')
                    ->push(CustomTranslator::get('Выбор очереди упаковки'), route('platform.tables.queue.select'));
            });

        // Териминал -> Приемка товара (выбор товара)
        Route::screen('packing/select/{queueId}', \App\Orchid\Screens\WorkTables\Packing\SelectPackingTableScreen::class)
            ->name('.packing.select')
            ->breadcrumbs(function (Trail $trail, $queueId) {
                return $trail
                    ->parent('platform.index')
                    ->push(CustomTranslator::get('Выбор стола упаковки'), route('platform.tables.packing.select', $queueId));
            });

        // Териминал -> Приемка товара (выбор товара)
        Route::screen('packing/scan/{queueId}/{tableId}/{orderId}', \App\Orchid\Screens\WorkTables\Packing\PackingScreen::class)
            ->name('.packing.scan')
            ->breadcrumbs(function (Trail $trail, $queueId, $tableId, $orderId) {
                return $trail
                    ->parent('platform.tables.packing.select', $queueId)
                    ->push(CustomTranslator::get('Упаковка заказов'), route('platform.tables.packing.scan', [$queueId, $tableId, $orderId]));
            });

    });

// ******************************************************
// *** Список товаров
// *** Platform > offers
// ******************************************************

Route::screen('offers', \App\Orchid\Screens\Offers\OffersScreen::class)
    ->name('platform.offers.index')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(CustomTranslator::get('Список товаров'), route('platform.offers.index')));

Route::screen('offers/create', \App\Orchid\Screens\Offers\OffersCreateScreen::class)
    ->name('platform.offers.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.offers.index')
        ->push(CustomTranslator::get('Создание нового товара'), route('platform.offers.create')));

Route::screen('offers/{offerId}/edit', \App\Orchid\Screens\Offers\OfferEditScreen::class)
    ->name('platform.offers.edit')
    ->breadcrumbs(fn(Trail $trail, $offerId) => $trail
        ->parent('platform.offers.index')
        ->push(CustomTranslator::get('Создание нового товара'), route('platform.offers.edit', $offerId)));

Route::screen('offers/{whId}/{offerId}/turnover', \App\Orchid\Screens\Offers\TurnoverScreen::class)
    ->name('platform.offers.turnover')
    ->breadcrumbs(fn(Trail $trail, $whId, $offerId) => $trail
        ->parent('platform.offers.index')
        ->push(CustomTranslator::get('Движение товара'), route('platform.offers.turnover', ['whId' => $whId, 'offerId' => $offerId])));



// ******************************************************
// *** Список товаров
// *** Platform > orders
// ******************************************************

Route::screen('orders', \App\Orchid\Screens\Orders\OrdersScreen::class)
    ->name('platform.orders.index')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(CustomTranslator::get('Список заказов'), route('platform.orders.index')));

Route::screen('orders/create', \App\Orchid\Screens\Orders\OrderCreateScreen::class)
    ->name('platform.orders.create.index')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.orders.index')
        ->push(CustomTranslator::get('Создание нового заказа'), route('platform.orders.create.index')));

Route::screen('orders/{orderId}/edit', \App\Orchid\Screens\Orders\OrderEditScreen::class)
    ->name('platform.orders.edit')
    ->breadcrumbs(fn(Trail $trail, $orderId) => $trail
        ->parent('platform.orders.index')
        ->push(CustomTranslator::get('Создание нового заказа'), route('platform.orders.edit', $orderId)));

// ******************************************************
// *** Список магазинов
// *** Platform > shops
// ******************************************************

Route::screen('shops', \App\Orchid\Screens\Shops\ShopsScreen::class)
    ->name('platform.shops.index')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(CustomTranslator::get('Магазины'), route('platform.shops.index')));

Route::screen('shops/create', \App\Orchid\Screens\Shops\ShopsCreateScreen::class)
    ->name('platform.shops.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(CustomTranslator::get('Магазины'), route('platform.shops.create')));

Route::screen('shops/{shopId}/edit', \App\Orchid\Screens\Shops\ShopsCreateScreen::class)
    ->name('platform.shops.edit')
    ->breadcrumbs(fn(Trail $trail, $shopId) => $trail
        ->parent('platform.shops.index')
        ->push(CustomTranslator::get('Магазины'), route('platform.shops.edit', $shopId)));

// ******************************************************
// *** Список складов
// *** Platform > warehouses
// ******************************************************

Route::screen('whmanagement/wave-assembly', \App\Orchid\Screens\WhManagement\WaveAssembly\WAManagementScreen::class)
    ->name('platform.whmanagement.wave-assembly.index')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(CustomTranslator::get('Волновая сборка'), route('platform.whmanagement.wave-assembly.index')));

// Настройка очереди позаказной сборки (manually)
Route::screen('whmanagement/single-order-assembly', \App\Orchid\Screens\WhManagement\SingleOrderAssembly\SOAManagementScreen::class)
    ->name('platform.whmanagement.single-order-assembly.index')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(CustomTranslator::get('Позаказаная сборка'), route('platform.whmanagement.single-order-assembly.index')));

Route::screen('whmanagement/single-order-assembly/{soaId}/edit', \App\Orchid\Screens\WhManagement\SingleOrderAssembly\SOAManagementEditScreen::class)
    ->name('platform.whmanagement.single-order-assembly.edit')
    ->breadcrumbs(fn(Trail $trail, $soaId) => $trail
        ->parent('platform.whmanagement.single-order-assembly.index')
        ->push(CustomTranslator::get('Редактирование позаказной сборки'), route('platform.whmanagement.single-order-assembly.edit', $soaId)));

// Настройки упаковки
Route::screen('whmanagement/packing-process-settings', \App\Orchid\Screens\WhManagement\PackingProcessSettings\PPManagementScreen::class)
    ->name('platform.whmanagement.packing-process-settings.index')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(CustomTranslator::get('Настройка упаковки'), route('platform.whmanagement.packing-process-settings.index')));

Route::screen('whmanagement/packing-process-settings/{ppId}/edit', \App\Orchid\Screens\WhManagement\PackingProcessSettings\PPManagementEditScreen::class)
    ->name('platform.whmanagement.packing-process-settings.edit')
    ->breadcrumbs(fn(Trail $trail, $ppId) => $trail
        ->parent('platform.whmanagement.packing-process-settings.index')
        ->push(CustomTranslator::get('Редактирование настроек упаковки'), route('platform.whmanagement.packing-process-settings.edit', $ppId)));


// ******************************************************
// *** Список складов
// *** Platform > warehouses
// ******************************************************

Route::screen('warehouses', \App\Orchid\Screens\Warehouses\WarehouseScreen::class)
    ->name('platform.warehouses.index')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(CustomTranslator::get('Список складов'), route('platform.warehouses.index')));

Route::screen('warehouses/create', \App\Orchid\Screens\Warehouses\WarehouseCreateScreen::class)
    ->name('platform.warehouses.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.warehouses.index')
        ->push(CustomTranslator::get('Список складов'), route('platform.warehouses.create')));

Route::screen('warehouses/{whId}/edit', \App\Orchid\Screens\Warehouses\WarehouseCreateScreen::class)
    ->name('platform.warehouses.edit')
    ->breadcrumbs(fn(Trail $trail, $whId) => $trail
        ->parent('platform.warehouses.index')
        ->push(CustomTranslator::get('Список складов'), route('platform.warehouses.edit', $whId)));

Route::screen('warehouses/places', \App\Orchid\Screens\Warehouses\Places\PlacesScreen::class)
    ->name('platform.warehouses.places.index')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.warehouses.index')
        ->push(CustomTranslator::get('Места хранения'), route('platform.warehouses.places.index')));

Route::screen('warehouses/places/print/labels', \App\Orchid\Screens\Warehouses\Places\LabelPrintScreen::class)
    ->name('platform.warehouses.print.labels.index')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.warehouses.places.index')
        ->push(CustomTranslator::get('Печать этикеток'), route('platform.warehouses.print.labels.index')));

// ******************************************************
// *** Список складов
// *** Platform > acceptances
// ******************************************************

Route::screen('acceptances', \App\Orchid\Screens\Acceptances\AcceptancesScreen::class)
    ->name('platform.acceptances.index')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(CustomTranslator::get('Список накладных'), route('platform.acceptances.index')));

Route::screen('acceptances/create', \App\Orchid\Screens\Acceptances\AcceptanceCreateScreen::class)
    ->name('platform.acceptances.create')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.acceptances.index')
        ->push(CustomTranslator::get('Список накладных'), route('platform.acceptances.create')));

Route::screen('acceptances/{acceptId}/edit', \App\Orchid\Screens\Acceptances\AcceptanceCreateScreen::class)
    ->name('platform.acceptances.edit')
    ->breadcrumbs(fn(Trail $trail, $acceptId) => $trail
        ->parent('platform.acceptances.index')
        ->push(CustomTranslator::get('Список накладных'), route('platform.acceptances.edit', $acceptId)));

Route::screen('acceptances/{acceptId}/offers', \App\Orchid\Screens\Acceptances\AcceptancesOffersScreen::class)
    ->name('platform.acceptances.offers')
    ->breadcrumbs(fn(Trail $trail, $acceptId) => $trail
        ->parent('platform.acceptances.index')
        ->push(CustomTranslator::get('Список накладных'), route('platform.acceptances.offers', $acceptId)));

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
        ->push(CustomTranslator::get('Настройки'), route('platform.settings.index')));

Route::prefix('settings')
    ->name('platform.settings')
    ->group(function () {

        // Страны
        Route::screen('countries', LibCountryScreen::class)
            ->name('.countries.index')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.index')
                ->push(CustomTranslator::get('Страны'), route('platform.settings.countries.index')));

        Route::screen('countries/create', LibCountryCreateScreen::class)
            ->name('.countries.create')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.countries.index')
                ->push(CustomTranslator::get('Создание новой страны'), route('platform.settings.countries.create')));

        Route::screen('countries/{countryId}/edit', LibCountryCreateScreen::class)
            ->name('.countries.edit')
            ->breadcrumbs(fn(Trail $trail, $countryId) => $trail
                ->parent('platform.settings.countries.index')
                ->push(CustomTranslator::get('Редактирование страны'), route('platform.settings.countries.edit', $countryId)));

        // Города
        Route::screen('cities', LibCityScreen::class)
            ->name('.cities.index')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.index')
                ->push(CustomTranslator::get('Города'), route('platform.settings.cities.index')));

        Route::screen('cities/create', LibCityCreateScreen::class)
            ->name('.cities.create')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.cities.index')
                ->push(CustomTranslator::get('Создание нового города'), route('platform.settings.cities.create')));

        Route::screen('cities/{cityId}/edit', LibCityCreateScreen::class)
            ->name('.cities.edit')
            ->breadcrumbs(fn(Trail $trail, $cityId) => $trail
                ->parent('platform.settings.cities.index')
                ->push(CustomTranslator::get('Редактирование города'), route('platform.settings.cities.edit', $cityId)));

        // Валюты
        Route::screen('currencies', LibCurrencyScreen::class)
            ->name('.currencies.index')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.index')
                ->push(CustomTranslator::get('Валюта'), route('platform.settings.currencies.index')));

        Route::screen('currencies/create', LibCurrencyCreateScreen::class)
            ->name('.currencies.create')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.currencies.index')
                ->push(CustomTranslator::get('Создание новой валюты'), route('platform.settings.currencies.create')));

        Route::screen('currencies/{curId}/edit', LibCurrencyCreateScreen::class)
            ->name('.currencies.edit')
            ->breadcrumbs(fn(Trail $trail, $langId) => $trail
                ->parent('platform.settings.currencies.index')
                ->push(CustomTranslator::get('Редактирование валюты'), route('platform.settings.currencies.edit', $langId)));

        // Языки
        Route::screen('languages', LibLanguageScreen::class)
            ->name('.languages.index')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.index')
                ->push(CustomTranslator::get('Языки'), route('platform.settings.languages.index')));

        Route::screen('languages/create', LibLanguageCreateScreen::class)
            ->name('.languages.create')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.languages.index')
                ->push(CustomTranslator::get('Создание новой единицы веса'), route('platform.settings.languages.create')));

        Route::screen('languages/{langId}/edit', LibLanguageCreateScreen::class)
            ->name('.languages.edit')
            ->breadcrumbs(fn(Trail $trail, $langId) => $trail
                ->parent('platform.settings.languages.index')
                ->push(CustomTranslator::get('Редактирование единицы веса'), route('platform.settings.languages.edit', $langId)));

        // Единицы размера
        Route::screen('weight', LibWeightScreen::class)
            ->name('.weight.index')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.index')
                ->push(CustomTranslator::get('Веса'), route('platform.settings.weight.index')));

        Route::screen('weight/create', LibWeightCreateScreen::class)
            ->name('.weight.create')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.weight.index')
                ->push(CustomTranslator::get('Создание новой единицы веса'), route('platform.settings.weight.create')));

        Route::screen('weight/{whId}/edit', LibWeightCreateScreen::class)
            ->name('.weight.edit')
            ->breadcrumbs(fn(Trail $trail, $whId) => $trail
                ->parent('platform.settings.weight.index')
                ->push(CustomTranslator::get('Редактирование единицы веса'), route('platform.settings.weight.edit', $whId)));

        // Единицы размера
        Route::screen('length', LibLengthScreen::class)
            ->name('.length.index')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.index')
                ->push(CustomTranslator::get('Размеры'), route('platform.settings.length.index')));

        Route::screen('length/create', LibLengthCreateScreen::class)
            ->name('.length.create')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.settings.length.index')
                ->push(CustomTranslator::get('Создание новой единицы измерения'), route('platform.settings.length.create')));

        Route::screen('length/{libId}/edit', LibLengthCreateScreen::class)
            ->name('.length.edit')
            ->breadcrumbs(fn(Trail $trail, $lenId) => $trail
                ->parent('platform.settings.length.index')
                ->push(CustomTranslator::get('Редактирование единицы измерения'), route('platform.settings.length.edit', $lenId)));

    });

Route::prefix('delivery-services')
    ->name('platform.delivery-services')
    ->group(function () {

        // Страны
        Route::screen('list', \App\Orchid\Screens\DeliveryServices\DeliveryServicesScreen::class)
            ->name('.list')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.index')
                ->push(CustomTranslator::get('Службы доставки')),
            );

        Route::screen('create', \App\Orchid\Screens\DeliveryServices\DeliveryServicesCreateScreen::class)
            ->name('.create')
            ->breadcrumbs(fn(Trail $trail) => $trail
                ->parent('platform.delivery-services.list')
                ->push(CustomTranslator::get('Создание службы доставки'), route('platform.delivery-services.create')));

        Route::screen('{dsId}/edit', \App\Orchid\Screens\DeliveryServices\DeliveryServicesCreateScreen::class)
            ->name('.edit')
            ->breadcrumbs(fn(Trail $trail, $dsId) => $trail
                ->parent('platform.delivery-services.list')
                ->push(CustomTranslator::get('Редактирование службы доставки'), route('platform.delivery-services.edit', $dsId)));

    });

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(CustomTranslator::get('Профиль'), route('platform.profile')));

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
        ->push(CustomTranslator::get('Создать'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(CustomTranslator::get('Пользователи'), route('platform.systems.users')));

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
        ->push(CustomTranslator::get('Создать'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn(Trail $trail) => $trail
        ->parent('platform.index')
        ->push(CustomTranslator::get('Роли'), route('platform.systems.roles')));

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
