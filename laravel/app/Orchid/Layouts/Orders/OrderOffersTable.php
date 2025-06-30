<?php

namespace App\Orchid\Layouts\Orders;

use App\Models\rwOrderOffer;
use App\Services\CustomTranslator;
use App\WhCore\WhCore;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class OrderOffersTable extends Table
{
    protected $target = 'dbOrderOffersList';

    protected function columns(): iterable
    {

        $whCore = new WhCore($this->query['order']['o_wh_id']);

        $status = $this->query['order']['o_status_id'];
        $whId = $this->query['order']['o_wh_id'];

        return [
            TD::make('num', 'ID')
                ->sort()
                ->align('center')
                ->render(function (rwOrderOffer $model) {
                    static $num = 0;
                    $num++;
                    return $num;
                }),

            TD::make(CustomTranslator::get('Изображение'))
                ->render(function ($model) {
                    if (isset($model->getOffer->of_img) && $model->getOffer->of_img != '')
                        return "<img src='{$model->getOffer->of_img}' alt='Image' width='75' height='75'>";
                    else
                        return "<img src='/img/no_image.png' alt='Image' width='75' height='75'>";
                })
                ->width('100px'),

            TD::make('of_article', CustomTranslator::get('Артикул'))
                ->sort()
                ->align('center')
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwOrderOffer $modelName) {
                    return Link::make(isset($modelName->getOffer->of_article) ? $modelName->getOffer->of_article : '-');
                }),

            TD::make('of_name', CustomTranslator::get('Название'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwOrderOffer $modelName) {
                    if (isset($modelName->getOffer->of_name))
                        return Link::make($modelName->getOffer->of_name);
                    else
                        return Link::make('-');
                }),

            TD::make('rest', CustomTranslator::get('Остаток'))
                ->sort()
                ->align('center')
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwOrderOffer $modelName) use ($whCore, $whId) {
                    $offerId = $modelName->oo_offer_id;
                    return Link::make($whCore->getRestOfOfferId($offerId))->route('platform.offers.turnover', [
                        'whId' => $whId,
                        'offerId' => $offerId,
                    ]);
                }),

            TD::make('reserv', CustomTranslator::get('Резерв'))
                ->sort()
                ->align('center')
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwOrderOffer $modelName) use ($whCore, $status) {
                    if ($status <= 10)
                        return '-';
                    if ($status == 15 )
                        return '<div style="background-color: #AAAAAA; color: #FFFFFF; border-radius: 10px;"><b>' . CustomTranslator::get('Готов к резерву') . '</b></div>';
                    if ($status == 20)
                        return '<div style="background-color: #999999; color: #FFFFFF; border-radius: 10px;"><b>' . CustomTranslator::get('Резервирую...') . '</b></div>';

                    if ($status > 20) {
                        if ($whCore->getReservedOffer($modelName->oo_order_id, $modelName->oo_offer_id))
                            return '<div style="background-color: #1ebc33; color: #FFFFFF; border-radius: 10px;"><b>' . CustomTranslator::get('В резерве') . '</b></div>';
                        else
                            return '<div style="background-color: #ef2828; color: #FFFFFF; border-radius: 10px;"><b>' . CustomTranslator::get('Нет товара') . '</b></div>';
                    }
                }),

            TD::make('oo_qty', CustomTranslator::get('Заказано'))
                ->sort()
                ->align('center')
                ->render(function (rwOrderOffer $model) use ($status) {

                    if ($status <= 10)
                        return '
                        <input type="hidden" name="orderOfferDocId[' . $model->oo_id . ']" value="' . e($model->oo_id) . '" >
                        <input type="hidden" name="orderOfferId[' . $model->oo_id . ']" value="' . e($model->oo_offer_id) . '" >
                        <input type="text" name="orderOfferQty[' . $model->oo_id . ']" value="' . e($model->oo_qty) . '" class="form-control" size=6>';
                    else
                        return $model->oo_qty;
                }),

            TD::make('qty_from_wh', CustomTranslator::get('Отгружено'))
                ->sort()
                ->align('center')
                ->render(function (rwOrderOffer $model) use ($whCore) {

                    $bgcolor = '#ef2828';
                    $offerQtySend = $whCore->getOfferRest($model->oo_order_id, 2, $model->oo_offer_id);

                    if ($offerQtySend == $model->oo_qty) $bgcolor = '#1ebc33';

                    return '<div style="background-color: '.$bgcolor.'; color: #FFFFFF; border-radius: 10px;"><b>' . $offerQtySend . '</b></div>';
                }),

            TD::make('oo_oc_price', CustomTranslator::get('Цена (оценка)'))
                ->sort()
                ->align('center')
                ->render(function (rwOrderOffer $model) use ($status) {
                    if ($status <= 10)
                        return '<input type="text" name="orderOfferOcPrice[' . $model->oo_id . ']" value="' . e($model->oo_oc_price) . '" class="form-control" size=6>';
                    else
                        return $model->oo_oc_price;
                }),

            TD::make('oo_price', CustomTranslator::get('Цена (наложка)'))
                ->sort()
                ->align('center')
                ->render(function (rwOrderOffer $model) use ($status) {
                    if ($status <= 10)
                        return '<input type="text" name="orderOfferPrice[' . $model->oo_id . ']" value="' . e($model->oo_price) . '" class="form-control" size=6>';
                    else
                        return $model->oo_price;

                }),

            TD::make(CustomTranslator::get(''))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->canSee($status <= 10)
                ->render(fn(rwOrderOffer $model) =>
                Button::make(CustomTranslator::get('Удалить'))
                    ->icon('bs.trash')
                    ->method('deleteOrderOffer')
                    ->style('color: red;')
                    ->parameters([
                        'offerDocId' => $model->oo_id,
                        '_token' => csrf_token(),
                    ])
                    ->confirm(CustomTranslator::get('Вы уверены, что хотите удалить это предложение?'))
                ),
        ];
    }

    protected function toolbar(): array
    {
        return [
            Button::make(CustomTranslator::get('Сохранить изменения'))
                ->method('saveOrderChanges')
                ->class('btn btn-primary'),
        ];
    }
}
