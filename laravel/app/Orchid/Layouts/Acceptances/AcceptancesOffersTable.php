<?php

namespace App\Orchid\Layouts\Acceptances;

use App\Models\rwAcceptance;
use App\Models\rwAcceptanceOffer;
use App\Models\rwLibAcceptStatus;
use App\Models\rwShop;
use App\Models\rwWarehouse;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Repository;

class AcceptancesOffersTable extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'dbAcceptOffersList';

    protected function columns(): iterable
    {
        return [
            TD::make('ao_id', 'ID')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->align('center'),

            TD::make('Изображение')
                ->align('center')
                ->render(function ($model) {
                    if ($model->getOffers->of_img == '')
                        return "<img src='/img/no_image.png' alt='Image' width='75' height='75'>";
                    else
                        return "<img src='{$model->getOffers->of_img}' alt='Image' width='75' height='75'>";
                })
                ->width('100px'),

            TD::make('getOffers.of_name', 'Товар')
                ->sort()
                ->filter(TD::FILTER_TEXT),

            TD::make('getOffers.of_article', 'Артикул')
                ->sort()
                ->filter(TD::FILTER_TEXT),

            TD::make('ao_id', 'Размеры')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwAcceptanceOffer $modelName) {
                    return ModalToggle::make(
                        $modelName->getOffers->of_dimension_x . 'x' .
                        $modelName->getOffers->of_dimension_y . 'x' .
                        $modelName->getOffers->of_dimension_z . ' / ' .
                        $modelName->getOffers->of_weight . 'гр.'
                    )
                        ->modal('editDimensions') // Имя модального окна
                        ->modalTitle('Редактировать размеры')
                        ->method('saveDimensions') // Метод для обработки данных
                        ->parameters([
                            'offerId' => $modelName->ao_id,
                        ]);
                }),

            TD::make('ao_batch', 'Батч')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwAcceptanceOffer $modelName) {
                    return '<input type="text" name="docOfferBatch[' . $modelName->ao_id . ']" value="' . e($modelName->ao_batch) . '" class="form-control" size=10 placeHolder="Батч">';
                }),

            TD::make('ao_expiration_date', 'Срок годности')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwAcceptanceOffer $modelName) {
                    return '<input type="text" name="docOfferExpDate[' . $modelName->ao_id . ']" value="' . e($modelName->ao_expiration_date) . '" class="form-control" size=10 placeHolder="Срок годности">';
                }),

            TD::make('ao_barcode', 'Штрих-код')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwAcceptanceOffer $modelName) {
                    $bgColor = '';
                    if ($modelName->ao_barcode == '') $bgColor = 'style="background-color: #ffbdbf;"';
                    return '<input type="text" name="docOfferBarcode[' . $modelName->ao_id . ']" value="' . e($modelName->ao_barcode) . '" class="form-control" size=15 placeHolder="Штрих-код" ' . $bgColor . '>';
                }),

            TD::make('ao_expected', 'Ожидается')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwAcceptanceOffer $modelName) {
                    $bgColor = '';
                    if ($modelName->ao_expected == 0) $bgColor = 'style="background-color: #ffbdbf;"';
                    return '<input type="text" name="docOfferExept[' . $modelName->ao_id . ']" value="' . e($modelName->ao_expected) . '" class="form-control" size=6 placeHolder="Ожидается" ' . $bgColor . '>';
                }),

            TD::make('ao_accepted', 'Принято')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwAcceptanceOffer $modelName) {
                    return '<input type="text" name="docOfferAccept[' . $modelName->ao_id . ']" value="' . e($modelName->ao_accepted) . '" class="form-control" size=6 placeHolder="Принято">';
                }),

            TD::make('ao_price', 'Закупочная цена')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (rwAcceptanceOffer $modelName) {
                    return '<input type="text" name="docOfferPrice[' . $modelName->ao_id . ']" value="' . e($modelName->ao_price) . '" class="form-control" size=6 placeHolder="Цена">';
                }),

            TD::make(__('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn(rwAcceptanceOffer $modelName) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Button::make(__('Удалить'))
                            ->icon('bs.trash')
                            ->method('deleteOffer')
                            ->parameters([
                                'offerId' => $modelName->ao_id,
                                '_token' => csrf_token(), // Добавляем CSRF-токен вручную
                            ])
                            ->confirm(__('Вы уверены, что хотите удалить этот товар из накладной?')),
                    ])),

        ];
    }

    protected function toolbar(): array
    {
        return [
            Button::make('Сохранить изменения')
                ->method('saveChanges')
                ->class('btn btn-primary'),
        ];
    }
}
