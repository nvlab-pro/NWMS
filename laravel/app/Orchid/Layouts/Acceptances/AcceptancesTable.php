<?php

namespace App\Orchid\Layouts\Acceptances;

use App\Models\rwAcceptance;
use App\Models\rwLibAcceptStatus;
use App\Models\rwShop;
use App\Models\rwWarehouse;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class AcceptancesTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'acceptList';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('acc_id', 'ID')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwAcceptance $modelName) {
                    return Link::make($modelName->acc_id)
                        ->route('platform.acceptances.offers',$modelName->acc_id);
                }),

            TD::make('acc_status', 'Статус накладной')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwAcceptance $modelName) {
                    return Link::make($modelName->getAccStatus->las_name)
                        ->route('platform.acceptances.offers', $modelName->acc_id);
                }),

            TD::make('acc_ext_id', 'Внешний ID')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwAcceptance $modelName) {
                    return Link::make($modelName->acc_ext_id)
                        ->route('platform.acceptances.offers',$modelName->acc_id);
                }),

            TD::make('acc_date', 'Дата')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwAcceptance $modelName) {
                    return Link::make($modelName->acc_date)
                        ->route('platform.acceptances.offers',$modelName->acc_id);
                }),

            TD::make('acc_wh_id', 'Склад')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwAcceptance $modelName) {
                    return Link::make($modelName->getWarehouse->wh_name)
                        ->route('platform.acceptances.offers', $modelName->acc_id);
                }),

            TD::make('acc_comment', 'Комментарий')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwAcceptance $modelName) {
                    return Link::make($modelName->acc_comment)
                        ->route('platform.acceptances.offers',$modelName->acc_id);
                }),

            TD::make(__('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (rwAcceptance $modelName) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(__('Ред.'))
                            ->route('platform.acceptances.edit', $modelName->acc_id)
                            ->icon('bs.pencil'),
                        Link::make(__('Товары'))
                            ->route('platform.acceptances.offers', $modelName->acc_id)
                            ->icon('bs.pencil'),
                    ])),

        ];
    }
}
