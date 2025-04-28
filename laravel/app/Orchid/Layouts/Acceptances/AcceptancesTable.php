<?php

namespace App\Orchid\Layouts\Acceptances;

use App\Models\rwAcceptance;
use App\Models\rwLibAcceptStatus;
use App\Models\rwLibAcceptType;
use App\Models\rwShop;
use App\Models\rwWarehouse;
use App\Services\CustomTranslator;
use Carbon\Carbon;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
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
            TD::make('acc_id', CustomTranslator::get('ID'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwAcceptance $modelName) {
                    return Link::make($modelName->acc_id)
                        ->route('platform.acceptances.offers', $modelName->acc_id);
                }),

            TD::make('acc_status', CustomTranslator::get('Статус'))
                ->align('center')
                ->sort()
                ->filter(
                    TD::FILTER_SELECT,
                    rwLibAcceptStatus::all()->mapWithKeys(function ($status) {
                        return [$status->las_id => CustomTranslator::get($status->las_name)]; // Применяем перевод
                    })->toArray()
                )
                ->align(TD::ALIGN_CENTER)
                ->render(function (rwAcceptance $modelName) {
                    return '<div onClick="window.location=\''.route('platform.acceptances.offers', $modelName->acc_id).'\'" style="color: '.$modelName->getAccStatus->las_color.';
                        background-color: '.$modelName->getAccStatus->las_bgcolor.';
                        padding: 5px;
                        border-radius: 5px;"><b>'.CustomTranslator::get($modelName->getAccStatus->las_name).'</b></div>';
                }),

            TD::make('acc_type', CustomTranslator::get('Тип'))
                ->align('center')
                ->sort()
                ->filter(
                    TD::FILTER_SELECT,
                    rwLibAcceptType::all()->mapWithKeys(function ($type) {
                        return [$type->lat_id => CustomTranslator::get($type->lat_name)]; // Применяем перевод
                    })->toArray()
                )
                ->align(TD::ALIGN_CENTER)
                ->render(function (rwAcceptance $modelName) {
                    return Link::make($modelName->getAccType->lat_name)
                        ->route('platform.acceptances.offers', $modelName->acc_id);
                }),

            TD::make('acc_ext_id', CustomTranslator::get('Внешний ID'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwAcceptance $modelName) {
                    return Link::make($modelName->acc_ext_id)
                        ->route('platform.acceptances.offers', $modelName->acc_id);
                }),

            TD::make('acc_date', CustomTranslator::get('Дата'))
                ->align('center')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwAcceptance $modelName) {
                    $formattedDate = Carbon::parse($modelName->acc_date)->format('d.m.Y');
                    return Link::make($formattedDate)
                        ->route('platform.acceptances.offers', $modelName->acc_id);
                }),

            TD::make('getUser.name', CustomTranslator::get('Владелец'))
                ->align('center')
                ->render(function (rwAcceptance $modelName) {
                    $name = isset($modelName->getUser->name) ? $modelName->getUser->name : '-';

                    return Link::make($name)
                        ->route('platform.acceptances.offers', $modelName->acc_id);
                }),

            TD::make('acc_wh_id', CustomTranslator::get('Склад'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwAcceptance $modelName) {
                    return Link::make($modelName->getWarehouse->wh_name)
                        ->route('platform.acceptances.offers', $modelName->acc_id);
                }),

            TD::make('acc_shop_id', CustomTranslator::get('Магазин'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwAcceptance $modelName) {
                    return Link::make($modelName->getShop->sh_name)
                        ->route('platform.acceptances.offers', $modelName->acc_id);
                }),

            TD::make('acc_count_expected', CustomTranslator::get('Ожидается'))
                ->sort()
                ->filter(TD::FILTER_TEXT),

            TD::make('acc_count_accepted', CustomTranslator::get('Принято'))
                ->sort()
                ->filter(TD::FILTER_TEXT),

            TD::make('acc_count_placed', CustomTranslator::get('Привязано'))
                ->sort()
                ->filter(TD::FILTER_TEXT),

            TD::make('acc_comment', CustomTranslator::get('Комментарий'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwAcceptance $modelName) {
                    return Link::make($modelName->acc_comment)
                        ->route('platform.acceptances.offers', $modelName->acc_id);
                }),

            TD::make(CustomTranslator::get('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn(rwAcceptance $modelName) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(CustomTranslator::get('Ред.'))
                            ->route('platform.acceptances.edit', $modelName->acc_id)
                            ->icon('bs.pencil'),
                        Link::make(CustomTranslator::get('Товары'))
                            ->route('platform.acceptances.offers', $modelName->acc_id)
                            ->icon('bs.pencil'),
                    ])),

        ];
    }
}
