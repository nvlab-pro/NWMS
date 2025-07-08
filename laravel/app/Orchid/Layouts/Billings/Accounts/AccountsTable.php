<?php

namespace App\Orchid\Layouts\Billings\Accounts;

use App\Http\Middleware\RoleMiddleware;
use App\Models\rwShop;
use App\Models\rwWarehouse;
use App\Services\CustomTranslator;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class AccountsTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'whList';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('wh_id', 'ID')
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->render(function (rwWarehouse $modelName) {
                    return Link::make($modelName->wh_id)
                        ->route('platform.billing.accounts.edit.transactions',$modelName->wh_id);
                }),

            TD::make('wh_type', CustomTranslator::get('Тип склада'))
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->filter(TD::FILTER_TEXT)
                ->render(fn ($whList) => $whList->getWhType ?
                    "<b style='background-color: {$whList->getWhType->lwt_bgcolor};
                        padding: 5px;
                        border-radius: 5px;'>
                        ".CustomTranslator::get($whList->getWhType->lwt_name)."
                        </b>"
                    : '-'),

            TD::make('sh_name', CustomTranslator::get('Склад'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwWarehouse $modelName) {
                    return Link::make($modelName->wh_name)
                        ->route('platform.billing.accounts.edit.transactions',$modelName->wh_id);
                }),

            TD::make('getCompany.co_name', CustomTranslator::get('Компания заказчика'))
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwWarehouse $modelName) {
                    if ($modelName->getCompany) {
                        return Link::make($modelName->getCompany->co_name)
                            ->route('platform.billing.accounts.edit.transactions',$modelName->wh_id);
                    } else {
                        return Link::make('-')
                            ->route('platform.billing.accounts.edit.transactions',$modelName->wh_id);
                    }
                }),

            TD::make('getParent.getCompany.co_name', CustomTranslator::get('Компания исполнителя'))
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->filter(TD::FILTER_TEXT)
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                ->render(function (rwWarehouse $modelName) {
                    if (isset($modelName->getParent->getCompany->co_name)) {
                        return Link::make($modelName->getParent->getCompany->co_name)
                            ->route('platform.billing.accounts.edit.transactions',$modelName->wh_id);
                    } else {
                        return Link::make('-')
                            ->route('platform.billing.accounts.edit.transactions',$modelName->wh_id);
                    }
                }),

            TD::make('getOwner.name', CustomTranslator::get('Владелец'))
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->filter(TD::FILTER_TEXT)
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                ->render(function (rwWarehouse $modelName) {
                    return Link::make($modelName->getOwner->name)
                        ->route('platform.billing.accounts.edit.transactions',$modelName->wh_id);
                }),

            TD::make('getParent.wh_name', CustomTranslator::get('Родитель'))
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->filter(TD::FILTER_TEXT)
                ->canSee(RoleMiddleware::checkUserPermission('admin'))
                ->render(function (rwWarehouse $modelName) {
                    if (isset($modelName->getParent->wh_name))
                        return Link::make($modelName->getParent->wh_name)
                            ->route('platform.billing.accounts.edit.transactions',$modelName->wh_id);
                    else
                        return Link::make('-')
                            ->route('platform.billing.accounts.edit.transactions',$modelName->wh_id);
                }),

            TD::make('getDomain.dm_name', CustomTranslator::get('Домен'))
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->filter(TD::FILTER_TEXT)
                ->canSee(RoleMiddleware::checkUserPermission('admin'))
                ->render(function (rwWarehouse $modelName) {
                    if ($modelName->getDomain) {
                        return Link::make($modelName->getDomain->dm_name)
                            ->route('platform.billing.accounts.edit.transactions',$modelName->wh_id);
                    } else {
                        return '-';
                    }
                }),

            TD::make('wh_billing_cost', CustomTranslator::get('Начислено'))
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->render(function (rwWarehouse $modelName) {
                    if ($modelName->wh_billing_cost) {
                        $bgColor = '#AAFFAA';
                        $cost = $modelName->wh_billing_cost;
                        if ($cost > 0) {
                            $bgColor = '#FFAAAA';
                            $cost = $cost * -1;
                        }
                        return '<div style="background-color: '.$bgColor.'">' . $cost . '</div>';
                    } else {
                        return '0';
                    }
                }),

            TD::make('wh_billing_received', CustomTranslator::get('Оплачено'))
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->render(function (rwWarehouse $modelName) {
                    if ($modelName->wh_billing_received) {
                        $bgColor = '#FFAAAA';
                        $received = $modelName->wh_billing_received;
                        if ($received > 0) {
                            $bgColor = '#AAFFAA';
                        }
                        return '<div style="background-color: '.$bgColor.'">' . $received . '</div>';
                    } else {
                        return '0';
                    }
                }),

            TD::make('wh_billing_sum', CustomTranslator::get('Баланс'))
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->render(function (rwWarehouse $modelName) {
                    if ($modelName->wh_billing_sum) {
                        $bgColor = '#FFAAAA';
                        $sum = $modelName->wh_billing_sum;
                        if ($sum > 0) {
                            $bgColor = '#AAFFAA';
                        }
                        return '<div style="background-color: '.$bgColor.'">' . $sum . '</div>';
                    } else {
                        return '0';
                    }
                }),

        ];
    }
}
