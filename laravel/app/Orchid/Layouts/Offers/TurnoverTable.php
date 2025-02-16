<?php

namespace App\Orchid\Layouts\Offers;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class TurnoverTable extends Table
{
    protected $target = 'dbOffersTurnover';

    protected function columns(): iterable
    {
        return [
            TD::make('whci_id', 'ID')
                ->sort()
                ->align('center'),

////            TD::make('whci_date', 'Дата')
////                ->sort()
////                ->render(fn(WarehouseChangeItem $model) => $model->whci_date->format('Y-m-d H:i:s')),
//
//            TD::make('whci_status', 'Статус')
//                ->sort()
//                ->align('center'),
//
//            TD::make('whci_offer_id', 'ID товара')
//                ->sort()
//                ->align('center'),
//
//            TD::make('whci_count', 'Количество')
//                ->sort()
//                ->align('center'),
//
//            TD::make('whci_price', 'Цена')
//                ->sort()
//                ->align('center'),
//
////            TD::make('', 'Действия')
////                ->align(TD::ALIGN_CENTER)
////                ->width('100px')
////                ->render(fn(WarehouseChangeItem \$model) =>
////                Button::make('Удалить')
////                    ->icon('bs.trash')
////                    ->method('deleteWarehouseItem')
////                    ->style('color: red;')
////                    ->parameters([
////                        'itemId' => \$model->whci_id,
////                            '_token' => csrf_token(),
////                        ])
////                        ->confirm('Вы уверены, что хотите удалить этот элемент?')
////                ),
        ];
    }
}
