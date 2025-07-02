<?php

declare(strict_types=1);

namespace App\Orchid;

use App\Http\Middleware\RoleMiddleware;
use App\Services\CustomTranslator;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            Menu::make(CustomTranslator::get('Main'))
                ->icon('bs.bank2')
                ->route(config('platform.index')),

            // Терминал
            Menu::make(CustomTranslator::get('Терминал'))
                ->icon('bs.tablet')
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager,warehouse_worker'))
                ->route('platform.terminal.main'),

            Menu::make(CustomTranslator::get('Рабочие столы'))
                ->icon('bs.person-workspace')
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager,warehouse_worker'))
                ->list([

                    Menu::make(CustomTranslator::get('Столы упаковки'))
                        ->icon('bs.person-workspace')
                        ->route('platform.tables.queue.select')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager,warehouse_worker')),

                    Menu::make(CustomTranslator::get('Столы маркировки'))
                        ->icon('bs.envelope')
                        ->route('platform.tables.marking.queue.select')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager,warehouse_worker')),

                ]),

            Menu::make(CustomTranslator::get('Приемка товара'))
                ->icon('bs.building-add')
                ->route('platform.acceptances.index'),

            Menu::make(CustomTranslator::get('Список товаров'))
                ->icon('bs.stack')
                ->route('platform.offers.index'),

            Menu::make(CustomTranslator::get('Заказы'))
                ->icon('bs.box-seam-fill')
                ->route('platform.orders.index'),

            Menu::make(CustomTranslator::get('Библиотеки'))
                ->icon('bs.list-check')
                ->list([
                    Menu::make(CustomTranslator::get('Честный знак'))
                        ->icon('bs.qr-code-scan')
                        ->route('platform.lib.datamatrix.index')
                        ->canSee(RoleMiddleware::checkUserCountry('1')),

                    Menu::make(CustomTranslator::get('Домены'))
                        ->icon('bs.yin-yang')
                        ->route('platform.settings.domains')
                        ->canSee(RoleMiddleware::checkUserPermission('admin')),

                    Menu::make(CustomTranslator::get('Страны'))
                        ->icon('bs.globe-central-south-asia')
                        ->route('platform.settings.countries.index')
                        ->canSee(RoleMiddleware::checkUserPermission('admin')),

                    Menu::make(CustomTranslator::get('Города'))
                        ->icon('bs.map')
                        ->route('platform.settings.cities.index')
                        ->canSee(RoleMiddleware::checkUserPermission('admin')),

                    Menu::make(CustomTranslator::get('Валюты'))
                        ->icon('bs.currency-exchange')
                        ->route('platform.settings.currencies.index')
                        ->canSee(RoleMiddleware::checkUserPermission('admin')),

                    Menu::make(CustomTranslator::get('Языки'))
                        ->icon('bs.translate')
                        ->route('platform.settings.languages.index')
                        ->canSee(RoleMiddleware::checkUserPermission('admin')),
                ]),

            // *******************************
            // *    Настройки склада         *
            // *******************************

            Menu::make(CustomTranslator::get('Управление складом'))
                ->icon('bs.display')
                ->list([

                    Menu::make(CustomTranslator::get('Волновая сборка'))
                        ->icon('bs.cart3')
                        ->route('platform.whmanagement.wave-assembly.index')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager')),

                    Menu::make(CustomTranslator::get('Позаказная сборка'))
                        ->icon('bs.basket3')
                        ->route('platform.whmanagement.single-order-assembly.index')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager')),

                    Menu::make(CustomTranslator::get('Настройка упаковки'))
                        ->icon('bs.dropbox')
                        ->route('platform.whmanagement.packing-process-settings.index')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager')),

                    Menu::make(CustomTranslator::get('Настройка маркировки'))
                        ->icon('bs.envelope')
                        ->route('platform.whmanagement.marking-settings.index')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager')),

                    Menu::make(CustomTranslator::get('Управление импортами'))
                        ->icon('bs.cloud-upload')
                        ->route('platform.whmanagement.imports.index'),

                ]),

            // *******************************
            // *    Настройки склада         *
            // *******************************

            Menu::make(CustomTranslator::get('Настройки склада'))
                ->icon('bs.buildings-fill')
                ->list([

                    Menu::make(CustomTranslator::get('Склады'))
                        ->icon('bs.building')
                        ->route('platform.warehouses.index'),

                    Menu::make(CustomTranslator::get('Места хранения'))
                        ->icon('bs.inboxes')
                        ->route('platform.warehouses.places.index')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager')),

                ]),

            // ************************
            // *    Настройка      *
            // ************************

            Menu::make(CustomTranslator::get('Настройки'))
                ->icon('bs.gear-fill')
                ->list([
                    Menu::make(CustomTranslator::get('Магазины'))
                        ->icon('bs.shop')
                        ->route('platform.shops.index'),

                    Menu::make(CustomTranslator::get('Переводы'))
                        ->icon('bs.translate')
                        ->route('platform.settings.lang.editor')
                        ->canSee(RoleMiddleware::checkUserPermission('admin')),

                ]),

            // ************************
            // *    Службы доставки      *
            // ************************

            Menu::make(CustomTranslator::get('Доставка заказов'))
                ->icon('bs.truck-front-fill')
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                ->list([

                    Menu::make(CustomTranslator::get('Интеграции'))
                        ->icon('bs.truck')
                        ->route('platform.delivery-services.integrations.list'),

                    Menu::make(CustomTranslator::get('Службы доставки'))
                        ->icon('bs.truck')
                        ->route('platform.delivery-services.list'),

                ]),

            // ************************
            // *    Статистика      *
            // ************************

            Menu::make(CustomTranslator::get('Статистика'))
                ->icon('bs.graph-up')
                ->active('platform.statistics.*')
                ->list([
                    Menu::make(CustomTranslator::get('Работники'))
                        ->icon('bs.person-lines-fill')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                        ->route('platform.statistics.workers')
                        ->active('platform.statistics.*'),
                ]),

            // ************************
            // *    Биллинг      *
            // ************************

            Menu::make(CustomTranslator::get('Биллинг'))
                ->icon('bs.credit-card-fill')
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                ->list([
                    Menu::make(CustomTranslator::get('Биллинг'))
                        ->icon('bs.cash-coin')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                        ->route('platform.billing.billing.list'),

                    Menu::make(CustomTranslator::get('Компании'))
                        ->icon('bs.person-rolodex')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                        ->route('platform.billing.companies.list'),

                ]),

            // ************************
            // *    Фиксация входа      *
            // ************************

            Menu::make(CustomTranslator::get('Посещаемость'))
                ->icon('bs.person-walking')
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                ->list([
                    Menu::make(CustomTranslator::get('Фиксация входа'))
                        ->icon('bs.file-person')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                        ->route('platform.ea.main'),

                    Menu::make(CustomTranslator::get('Статистика'))
                        ->icon('bs.reception-3')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                        ->route('platform.ea.users'),

                    Menu::make(CustomTranslator::get('Посещаемость'))
                        ->icon('bs.person-walking')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                        ->route('platform.ea.attendance'),

                    Menu::make(CustomTranslator::get('Пропуски'))
                        ->icon('bs.person-arms-up')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                        ->route('platform.ea.rests'),

                ]),

            // ************************
            // *     Пользователи      *
            // ************************

            Menu::make(CustomTranslator::get('Пользователи'))
                ->icon('bs.people')
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                ->list([
                    Menu::make('Пользователи')
                        ->icon('bs.people')
                        ->route('platform.systems.users')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager')),

                    Menu::make('Роли')
                        ->icon('bs.book')
                        ->canSee(RoleMiddleware::checkUserPermission('admin'))
                        ->route('platform.systems.roles'),
                ]),


        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(CustomTranslator::get('System'))
                ->addPermission('platform.systems.roles', CustomTranslator::get('Roles'))
                ->addPermission('platform.systems.users', CustomTranslator::get('Users')),
        ];
    }
}
