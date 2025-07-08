<?php

namespace App\Orchid\Layouts\Billings\Accounts;

use App\Models\rwBillingAct;
use App\Services\CustomTranslator;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Carbon\Carbon;

class AccountActsTable extends Table
{
    protected $target = 'actsList';

    protected function columns(): iterable
    {
        return [
            TD::make('ba_date_start', CustomTranslator::get('Начало периода'))
                ->align(TD::ALIGN_CENTER)
                ->render(fn(rwBillingAct $act) => Carbon::parse($act->ba_date_start)->format('d.m.Y')),

            TD::make('ba_date_end', CustomTranslator::get('Окончание периода'))
                ->align(TD::ALIGN_CENTER)
                ->render(fn(rwBillingAct $act) => Carbon::parse($act->ba_date_end)->format('d.m.Y')),

            TD::make('ba_status', CustomTranslator::get('Статус'))
                ->align(TD::ALIGN_CENTER)
                ->render(fn(rwBillingAct $act) => match ($act->ba_status) {
                    0 => '<div style="background-color: #f0ad4e; color: #FFFFFF; padding: 5px; border-radius: 10px;"><b>' . CustomTranslator::get('На согласовании') . '</b></div>',
                    1 => '<div style="background-color: #00cc25; color: #FFFFFF; padding: 5px; border-radius: 10px;"><b>' . CustomTranslator::get('Готов') . '</b></div>',
                    default => '-',
                }),

            TD::make('ba_sum', CustomTranslator::get('Сумма'))
                ->align(TD::ALIGN_CENTER),

            TD::make('ba_tax_sum', CustomTranslator::get('НДС'))
                ->align(TD::ALIGN_CENTER),

            TD::make('ba_sum_total', CustomTranslator::get('Итого'))
                ->align(TD::ALIGN_CENTER),

            TD::make(CustomTranslator::get('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('160px')
                ->render(function (rwBillingAct $act) {
                    return DropDown::make()
                        ->icon('bs.three-dots')
                        ->list([
                            Link::make(CustomTranslator::get('Печать'))
                                ->route('platform.billing.accounts.edit.acts', $act->ba_id)
                                ->icon('bs.printer'),

                            Button::make(CustomTranslator::get('Удалить'))
                                ->icon('bs.trash')
                                ->confirm(CustomTranslator::get('Вы уверены что хотите удалить акт?'))
                                ->method('deleteAct', ['id' => $act->ba_id])
                                ->canSee(in_array($act->ba_status, [0])),
                        ]);
                }),
        ];
    }
}
