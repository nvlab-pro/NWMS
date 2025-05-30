<?php

namespace App\Orchid\Screens\terminal;

use App\Http\Middleware\RoleMiddleware;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Screen;

class mainScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return CustomTranslator::get('Терминал сбора данных');
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
        $currentUser = Auth::user();

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager') || $currentUser->hasRole('warehouse_worker')) {

            return [
                Layout::rows([

                    Link::make(CustomTranslator::get('Информация'))
                        ->route('platform.acceptances.index')
                        ->canSee(RoleMiddleware::checkUserPermission('admin'))
                        ->icon('bs.info-circle')
                        ->style('border-bottom: 2px solid #999999; 
                                    border-right: 2px solid #999999; 
                                    border-top: 1px solid #DDDDDD; 
                                    border-left: 1px solid #DDDDDD; 
                                    width: 100%; text-align: left; 
                                    padding: 15px 15px 15px 15px; 
                                    font-size: 20px; 
                                    background-color: #E2E3E5; 
                                    margin-bottom: 5px;'),

                    Link::make(CustomTranslator::get('Приемка товара'))
                        ->route('platform.terminal.acceptance.select')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager,warehouse_worker'))
                        ->icon('bs.book')
                        ->style('border-bottom: 2px solid #999999; 
                                    border-right: 2px solid #999999; 
                                    border-top: 1px solid #DDDDDD; 
                                    border-left: 1px solid #DDDDDD; 
                                    width: 100%; text-align: left; 
                                    padding: 15px 15px 15px 15px; 
                                    font-size: 20px; 
                                    background-color: #D1E7DD; 
                                    margin-bottom: 5px;'),

                    Link::make(CustomTranslator::get('Привязка товара'))
                        ->route('platform.terminal.places.select')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager,warehouse_worker'))
                        ->icon('bs.inboxes')
                        ->style('border-bottom: 2px solid #999999; 
                                    border-right: 2px solid #999999; 
                                    border-top: 1px solid #DDDDDD; 
                                    border-left: 1px solid #DDDDDD; 
                                    width: 100%; text-align: left; 
                                    padding: 15px 15px 15px 15px; 
                                    font-size: 20px; 
                                    background-color: #FFF3CD; 
                                    margin-bottom: 5px;'),

                    Link::make(CustomTranslator::get('Позаказная сборка заказов (вручную)'))
                        ->route('platform.terminal.soam.select')
                        ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager,warehouse_worker'))
                        ->icon('bs.box-seam')
                        ->style('border-bottom: 2px solid #999999; 
                                    border-right: 2px solid #999999; 
                                    border-top: 1px solid #DDDDDD; 
                                    border-left: 1px solid #DDDDDD; 
                                    width: 100%; text-align: left; 
                                    padding: 15px 15px 15px 15px; 
                                    font-size: 20px; 
                                    background-color: #CFE2FF; 
                                    margin-bottom: 5px;'),

                    Link::make(CustomTranslator::get('Сборка заказов (позаказно)'))
                        ->route('platform.terminal.soa.select')
                        ->canSee(RoleMiddleware::checkUserPermission('admin'))
                        ->icon('bs.box-seam')
                        ->style('border-bottom: 2px solid #999999; 
                                    border-right: 2px solid #999999; 
                                    border-top: 1px solid #DDDDDD; 
                                    border-left: 1px solid #DDDDDD; 
                                    width: 100%; text-align: left; 
                                    padding: 15px 15px 15px 15px; 
                                    font-size: 20px; 
                                    background-color: #CFE2FF; 
                                    margin-bottom: 5px;'),

                    Link::make(CustomTranslator::get('Сборка заказов (волной)'))
                        ->route('platform.acceptances.index')
                        ->canSee(RoleMiddleware::checkUserPermission('admin'))
                        ->icon('bs.cart3')
                        ->style('border-bottom: 2px solid #999999; 
                                    border-right: 2px solid #999999; 
                                    border-top: 1px solid #DDDDDD; 
                                    border-left: 1px solid #DDDDDD; 
                                    width: 100%; text-align: left; 
                                    padding: 15px 15px 15px 15px; 
                                    font-size: 20px; 
                                    background-color: #CFF4FC; 
                                    margin-bottom: 5px;'),

                    Link::make(CustomTranslator::get('Сортировка сборки (волной)'))
                        ->route('platform.acceptances.index')
                        ->canSee(RoleMiddleware::checkUserPermission('admin'))
                        ->icon('bs.shuffle')
                        ->style('border-bottom: 2px solid #999999; 
                                    border-right: 2px solid #999999; 
                                    border-top: 1px solid #DDDDDD; 
                                    border-left: 1px solid #DDDDDD; 
                                    width: 100%; text-align: left; 
                                    padding: 15px 15px 15px 15px; 
                                    font-size: 20px; 
                                    background-color: #BFE4EC; 
                                    margin-bottom: 5px;'),

                    Link::make(CustomTranslator::get('Инвентаризация'))
                        ->route('platform.acceptances.index')
                        ->canSee(RoleMiddleware::checkUserPermission('admin'))
                        ->icon('bs.calculator')
                        ->style('border-bottom: 2px solid #999999; 
                                    border-right: 2px solid #999999; 
                                    border-top: 1px solid #DDDDDD; 
                                    border-left: 1px solid #DDDDDD; 
                                    width: 100%; text-align: left; 
                                    padding: 15px 15px 15px 15px; 
                                    font-size: 20px; 
                                    background-color: #D3D3D4; 
                                    margin-bottom: 5px;'),

                    Button::make(CustomTranslator::get('Выход'))
                        ->icon('bs.box-arrow-left')
                        ->route('platform.logout')
                        ->style('border-bottom: 2px solid #999999; 
                                    border-right: 2px solid #999999; 
                                    border-top: 1px solid #DDDDDD; 
                                    border-left: 1px solid #DDDDDD; 
                                    width: 100%; text-align: left; 
                                    padding: 15px 15px 15px 15px; 
                                    font-size: 20px; 
                                    background-color: #ffc6c6; 
                                    margin-bottom: 5px;'),
                ]),
            ];
        }
    }
}
