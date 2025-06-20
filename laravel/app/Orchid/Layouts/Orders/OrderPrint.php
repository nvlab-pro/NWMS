<?php

namespace App\Orchid\Layouts\Orders;

use App\Services\CustomTranslator;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class OrderPrint extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'printTemplates';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {

        $order = $this->query->get('order');

        return [
            TD::make('pt_name', CustomTranslator::get('Название')),

            TD::make('pt_type', CustomTranslator::get('Тип')),

            TD::make('created_at', CustomTranslator::get('Создан'))
                ->render(fn($template) => $template->created_at?->format('d.m.Y H:i')),

            TD::make(CustomTranslator::get('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn($template) => Link::make(CustomTranslator::get('Печать'))
                    ->icon('bs.printer')
                    ->route('platform.orders.print', [
                        'orderId' => $order->o_id,
                        'tmpId'   => $template->pt_id,
                    ])
                ),
        ];
    }
}
