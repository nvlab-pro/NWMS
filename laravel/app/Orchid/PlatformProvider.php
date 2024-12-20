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

            Menu::make(__('Приемка товара'))
                ->icon('bs.building-add')
                ->route('platform.acceptances.index'),

            Menu::make(__('Список товаров'))
                ->icon('bs.stack')
                ->route('platform.offers.index'),

            Menu::make(__('Бибилотеки'))
                ->icon('bs.list-check')
                ->list([
                    Menu::make(__('Страны'))
                        ->icon('bs.list')
                        ->route('platform.settings.countries.index'),

                    Menu::make(__('Города'))
                        ->icon('bs.list')
                        ->route('platform.settings.cities.index'),

                    Menu::make(__('Валюты'))
                        ->icon('bs.list')
                        ->route('platform.settings.currencies.index'),

                    Menu::make(__('Языки'))
                        ->icon('bs.list')
                        ->route('platform.settings.languages.index'),
                ]),

            // ************************
            // *    Настройка      *
            // ************************

            Menu::make(__('Настройки'))
                ->icon('bs.gear-fill')
                ->list([
                    Menu::make(__('Магазины'))
                        ->icon('bs.shop')
                        ->route('platform.shops.index'),

                    Menu::make(__('Склады'))
                        ->icon('bs.buildings')
                        ->route('platform.warehouses.index'),
                ]),

            // ************************
            // *     Польщователи      *
            // ************************

            Menu::make(__('Пользователи'))
                ->icon('bs.people')
                ->list([
                    Menu::make('Пользователи')
                        ->icon('bs.people')
                        ->route('platform.systems.users')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager')),

                    Menu::make('Роли')
                        ->icon('bs.book')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
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
