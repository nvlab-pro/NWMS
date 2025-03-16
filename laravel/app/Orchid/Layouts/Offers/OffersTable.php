<?php

namespace App\Orchid\Layouts\Offers;

use App\Models\rwLibCity;
use App\Models\rwLibCountry;
use App\Models\rwLibStatus;
use App\Models\rwOffer;
use App\Models\rwShop;
use App\Services\CustomTranslator;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class OffersTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'offersList';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('of_id', CustomTranslator::get('ID'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->align('center')
                ->render(function (rwOffer $modelName) {
                    return Link::make($modelName->of_id)
                        ->route('platform.offers.edit', $modelName->of_id);
                }),

            TD::make(CustomTranslator::get('Изображение'))
                ->render(function ($model) {
                    if ($model->of_img == '')
                        return "<img src='/img/no_image.png' alt='Image' width='75' height='75'>";
                    else
                        return "<img src='{$model->of_img}' alt='Image' width='75' height='75'>";
                })
                ->width('100px'),

            TD::make('of_status', CustomTranslator::get('Статус'))
                ->sort()
                ->align('center')
                ->filter(
                    TD::FILTER_SELECT,
                    rwLibStatus::all()->mapWithKeys(function ($status) {
                        return [$status->ls_id => CustomTranslator::get($status->ls_name)]; // Применяем перевод
                    })->toArray()
                )
                ->render(function (rwOffer $modelName) {
                    return '<div onClick="window.location=\'' . route('platform.orders.edit', $modelName->of_id) . '\'" style="color: ' . $modelName->getStatus->ls_color . ';
                        background-color: ' . $modelName->getStatus->ls_bgcolor . ';
                        padding: 5px;
                        border-radius: 5px;"><b>' . CustomTranslator::get($modelName->getStatus->ls_name) . '</b></div>';
                }),

            TD::make('of_ext_id', CustomTranslator::get('Внешний ID'))
                ->sort()
                ->align('center')
                ->render(function (rwOffer $modelName) {
                    return Link::make(isset($modelName->of_ext_id) ? $modelName->of_ext_id : '-')
                        ->route('platform.offers.edit', $modelName->of_id);
                }),

            TD::make('of_article', CustomTranslator::get('Артикул'))
                ->sort()
                ->align('center')
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwOffer $modelName) {
                    return Link::make(isset($modelName->of_article) ? $modelName->of_article : '-')
                        ->route('platform.offers.edit', $modelName->of_id);
                }),

            TD::make('of_name', CustomTranslator::get('Название'))
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwOffer $modelName) {
                    return Link::make($modelName->of_name)
                        ->route('platform.offers.edit', $modelName->of_id);
                }),

            TD::make('of_sku', CustomTranslator::get('SKU'))
                ->sort()
                ->align('center')
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwOffer $modelName) {
                    return Link::make(isset($modelName->of_sku) ? $modelName->of_sku : '-')
                        ->route('platform.offers.edit', $modelName->of_id);
                }),

            TD::make('of_price', CustomTranslator::get('Cтоимость'))
                ->sort()
                ->align('center')
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwOffer $modelName) {
                    return Link::make(isset($modelName->of_price) ? $modelName->of_price : '-')
                        ->route('platform.offers.edit', $modelName->of_id);
                }),

            TD::make('of_estimated_price', CustomTranslator::get('Оц.стоимость'))
                ->sort()
                ->align('center')
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwOffer $modelName) {
                    return Link::make(isset($modelName->of_estimated_price) ? $modelName->of_estimated_price : '-')
                        ->route('platform.offers.edit', $modelName->of_id);
                }),

            TD::make('of_shop_id', CustomTranslator::get('Магазин'))
                ->sort()
                ->align('center')
                ->filter(
                    TD::FILTER_SELECT,
                    rwShop::pluck('sh_name', 'sh_id')->toArray() // Замените 'id' на ключевое поле вашей таблицы
                )
                ->render(function (rwOffer $modelName) {
                    return Link::make($modelName->getShop->sh_name)
                        ->route('platform.offers.edit', $modelName->of_id);
                }),

            TD::make(CustomTranslator::get('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn(rwOffer $modelName) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make(CustomTranslator::get('Ред.'))
                            ->route('platform.offers.edit', $modelName->of_id)
                            ->icon('bs.pencil'),
                    ])),

        ];
    }
}
