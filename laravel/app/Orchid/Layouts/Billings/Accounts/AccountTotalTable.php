<?php

namespace App\Orchid\Layouts\Billings\Accounts;

use App\Models\rwBillingTransactions;
use App\Services\CustomTranslator;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class AccountTotalTable extends Table
{
    protected $target = 'totalTransList';

    protected function columns(): iterable
    {
        return [
            TD::make('bt_service_id', CustomTranslator::get('Услуга'))
                ->render(function ($tx) {
                    return $tx->actionType->lat_name ?? '-';
                }),

            TD::make('total_sum', CustomTranslator::get('Сумма'))
                ->align(TD::ALIGN_CENTER)
                ->render(function ($tx) {
                    return number_format($tx->total_sum, 2, '.', ' ');
                }),
        ];
    }
}