<?php

declare(strict_types=1);

namespace App\Orchid;

use App\Http\Middleware\RoleMiddleware;
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
            Menu::make(__('Main'))
                ->icon('bs.bank2')
                ->route(config('platform.index')),

            // Терминал
            Menu::make(__('Терминал'))
                ->icon('bs.tablet')
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager,warehouse_worker'))
                ->route('platform.terminal.main'),

            Menu::make(__('Рабочие столы'))
                ->icon('bs.person-workspace')
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager,warehouse_worker'))
                ->list([

                    Menu::make(__('Столы упаковки'))
                        ->icon('bs.person-workspace')
                        ->route('platform.tables.queue.select')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager,warehouse_worker')),

                ]),

            Menu::make(__('Приемка товара'))
                ->icon('bs.building-add')
                ->route('platform.acceptances.index'),

            Menu::make(__('Список товаров'))
                ->icon('bs.stack')
                ->route('platform.offers.index'),

            Menu::make(__('Заказы'))
                ->icon('bs.box-seam-fill')
                ->route('platform.orders.index'),

            Menu::make(__('Библиотеки'))
                ->canSee(RoleMiddleware::checkUserPermission('admin'))
                ->icon('bs.list-check')
                ->list([
                    Menu::make(__('Страны'))
                        ->icon('bs.globe-central-south-asia')
                        ->route('platform.settings.countries.index'),

                    Menu::make(__('Города'))
                        ->icon('bs.map')
                        ->route('platform.settings.cities.index'),

                    Menu::make(__('Валюты'))
                        ->icon('bs.currency-exchange')
                        ->route('platform.settings.currencies.index'),

                    Menu::make(__('Языки'))
                        ->icon('bs.translate')
                        ->route('platform.settings.languages.index'),
                ]),

            // *******************************
            // *    Настройки склада         *
            // *******************************

            Menu::make(__('Управление складом'))
                ->icon('bs.display')
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                ->list([

                    Menu::make(__('Волновая сборка'))
                        ->icon('bs.cart3')
                        ->route('platform.whmanagement.wave-assembly.index')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager')),

                    Menu::make(__('Позаказная сборка'))
                        ->icon('bs.basket3')
                        ->route('platform.whmanagement.single-order-assembly.index')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager')),

                    Menu::make(__('Настройка упаковки'))
                        ->icon('bs.dropbox')
                        ->route('platform.whmanagement.packing-process-settings.index')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager')),

                ]),

            // *******************************
            // *    Настройки склада         *
            // *******************************

            Menu::make(__('Настройки склада'))
                ->icon('bs.buildings-fill')
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                ->list([

                    Menu::make(__('Склады'))
                        ->icon('bs.building')
                        ->route('platform.warehouses.index')
                        ->canSee(RoleMiddleware::checkUserPermission('admin')),

                    Menu::make(__('Места хранения'))
                        ->icon('bs.inboxes')
                        ->route('platform.warehouses.places.index')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager')),

                ]),

            // ************************
            // *    Настройка      *
            // ************************

            Menu::make(__('Настройки'))
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                ->icon('bs.gear-fill')
                ->list([
                    Menu::make(__('Магазины'))
                        ->icon('bs.shop')
                        ->route('platform.shops.index'),
                ]),

            // ************************
            // *    Службы доставки      *
            // ************************

            Menu::make(__('Доставка заказов'))
                ->icon('bs.truck-front-fill')
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                ->list([

                    Menu::make(__('Службы доставки'))
                        ->icon('bs.truck')
                        ->route('platform.delivery-services.list'),

                ]),

            // ************************
            // *     Пользователи      *
            // ************************

            Menu::make(__('Пользователи'))
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
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
        ];
    }
}
