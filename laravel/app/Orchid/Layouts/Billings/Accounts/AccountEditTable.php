<?php

namespace App\Orchid\Layouts\Billings\Accounts;

use App\Models\rwBillingTransactions;
use App\Models\rwWarehouse;
use App\Services\CustomTranslator;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class AccountEditTable extends Table
{
    /**
     * Название ключа, из которого будет извлекаться источник данных.
     *
     * @var string
     */
    protected $target = 'transactionsList';

    /**
     * Определение столбцов таблицы
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('bt_date', CustomTranslator::get('Дата'))
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->render(fn(rwBillingTransactions $tx) => date('d.m.Y H:i', strtotime($tx->bt_date))
                ),

            TD::make('actionType.lat_name', CustomTranslator::get('Услуга'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->align(TD::ALIGN_CENTER)
                ->render(function (rwBillingTransactions $modelName) {
                    return '<div style="
                        background-color: ' . $modelName->actionType->lat_bgcolor . ';
                        color: ' . $modelName->actionType->lat_color . ';
                        font-weight: bold;
                        text-align: center;
                        border-radius: 8px;
                        padding: 6px 10px;
                        display: inline-block;
                    ">
                        ' . $modelName->actionType->lat_name . '
                    </div>';
                }),

            TD::make('customerCompany.co_name', CustomTranslator::get('Заказчик'))
                ->align(TD::ALIGN_CENTER),

            TD::make('executorCompany.co_name', CustomTranslator::get('Исполнитель'))
                ->align(TD::ALIGN_CENTER),

            TD::make('bt_sum', CustomTranslator::get('Начислено'))
                ->align(TD::ALIGN_CENTER),

            TD::make('bt_tax', CustomTranslator::get('Налоги'))
                ->align(TD::ALIGN_CENTER),

            TD::make('bt_total_sum', CustomTranslator::get('Итого'))
                ->align(TD::ALIGN_CENTER),

            TD::make('bt_act_id', CustomTranslator::get('Акт'))
                ->align(TD::ALIGN_CENTER),

            TD::make('bt_desc', CustomTranslator::get('Транзакция')),


            TD::make(CustomTranslator::get('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn(rwBillingTransactions $tx) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        ModalToggle::make(CustomTranslator::get('Редактировать'))
                            ->canSee($tx->bt_act_id == 0)
                            ->modal('editTransactionModal')
                            ->asyncParameters([
                                'id' => $tx->bt_id,
                            ])
                            ->icon('bs.pencil'),

                        Button::make(CustomTranslator::get('Удалить'))
                            ->canSee($tx->bt_act_id == 0)
                            ->confirm(CustomTranslator::get('Вы уверены, что хотите удалить транзакцию?'))
                            ->method('deleteTransaction')
                            ->parameters(['id' => $tx->bt_id])
                            ->icon('bs.trash'),
                    ])),
        ];
    }
}
