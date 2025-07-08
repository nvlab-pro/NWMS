<?php

namespace App\Orchid\Layouts\Billings\Accounts;

use App\Http\Middleware\RoleMiddleware;
use App\Models\rwInvoce;
use App\Services\CustomTranslator;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Carbon\Carbon;

class InvoceTable extends Table
{
    protected $target = 'invoicesList';

    protected function columns(): iterable
    {
        return [
            TD::make('in_date', CustomTranslator::get('Дата'))
                ->align(TD::ALIGN_CENTER)
                ->render(fn(rwInvoce $in) => Carbon::parse($in->in_date)->format('d.m.Y')),

            TD::make('in_status', CustomTranslator::get('Статус'))
                ->align(TD::ALIGN_CENTER)
                ->render(fn(rwInvoce $in) => match ($in->in_status) {
                    0 => '<div style="background-color: #405ced; color: #FFFFFF; padding: 5px; border-radius: 10px;"><b>' . CustomTranslator::get('Ожидает оплаты') . '</b></div>',
                    1 => '<div style="background-color: #00cc25; color: #FFFFFF; padding: 5px; border-radius: 10px;"><b>' . CustomTranslator::get('Оплачен') . '</b></div>',
                    2 => CustomTranslator::get('Удален'),
                    default => '-'
                }),

            TD::make('getCustomerCompany.co_name', CustomTranslator::get('Заказчик'))
                ->align(TD::ALIGN_CENTER),

            TD::make('getExecutorCompany.co_name', CustomTranslator::get('Исполнитель'))
                ->align(TD::ALIGN_CENTER),

            TD::make('in_sum', CustomTranslator::get('Сумма'))
                ->align(TD::ALIGN_CENTER),

            TD::make(CustomTranslator::get('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('160px')
                ->render(function (rwInvoce $in) {
                    return DropDown::make()
                        ->icon('bs.three-dots')
                        ->list([
                            Link::make(CustomTranslator::get('Печать'))
                                ->route('platform.billing.accounts.edit.invoices', $in->in_id)
                                ->icon('bs.printer'),

                            Button::make(CustomTranslator::get('Оплачен'))
                                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                                ->icon('bs.check2-circle')
                                ->confirm(CustomTranslator::get('Подтвердить оплату?'))
                                ->method('markInvoicePaid', ['id' => $in->in_id])
                                ->canSee(in_array($in->in_status, [0, 2])),

                            Link::make(CustomTranslator::get('Редактировать'))
                                ->route('platform.billing.accounts.edit.invoices', $in->in_id)
                                ->icon('bs.pencil')
                                ->canSee(in_array($in->in_status, [0, 2])),

                            Button::make(CustomTranslator::get('Удалить'))
                                ->icon('bs.trash')
                                ->confirm(CustomTranslator::get('Вы уверены что хотите удалить счёт?'))
                                ->method('deleteInvoice', ['id' => $in->in_id])
                                ->canSee(in_array($in->in_status, [0])),
                        ]);
                }),
        ];
    }
}
