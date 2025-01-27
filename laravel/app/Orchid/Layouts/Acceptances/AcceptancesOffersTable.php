<?php

namespace App\Orchid\Layouts\Acceptances;

use App\Http\Middleware\RoleMiddleware;
use App\Models\rwAcceptanceOffer;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Illuminate\Support\Facades\Auth;

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
                ->align('center'),

            TD::make('Изображение')
                ->align('center')
                ->render(function ($model) {
                    if ($model->ao_img == '')
                        return "<img src='/img/no_image.png' alt='Image' width='75' height='75'>";
                    else
                        return "<img src='{$model->ao_img}' alt='Image' width='75' height='75'>";
                })
                ->width('100px'),

            TD::make('ao_article', 'Артикул')
                ->sort(),

            TD::make('ao_name', 'Товар')
                ->sort(),

            TD::make('ao_dimension', 'Размеры')
                ->sort()
                ->render(function (rwAcceptanceOffer $modelName) {
                    $currentUser = Auth::user(); // Получение текущего пользователя

                    if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {
                        // Если пользователь имеет нужную роль, показываем модальное окно
                        return ModalToggle::make(
                            $modelName->ao_dimension,
                        )
                            ->modal('editDimensions') // Имя модального окна
                            ->modalTitle('Редактировать размеры')
                            ->method('saveDimensions') // Метод для обработки данных
                            ->parameters([
                                'offerId' => $modelName->ao_id,
                            ]);
                    } else {
                        // Если пользователь не имеет нужной роли, отображаем просто текст
                        return e($modelName->ao_dimension);
                    }
                }),

            TD::make('ao_batch', 'Батч')
                ->sort()
                ->render(function (rwAcceptanceOffer $modelName) {
                    $readonly = '';
                    if ($modelName->ao_placed > 0) $readonly = 'readonly';
                    return '<input type="hidden" name="docOfferId[' . $modelName->ao_id . ']" value="' . e($modelName->ao_offer_id) . '" >
                    <input type="hidden" name="docOfferPlaced[' . $modelName->ao_id . ']" value="' . e($modelName->ao_placed) . '" >
                    <input type="text" name="docOfferBatch[' . $modelName->ao_id . ']" value="' . e($modelName->ao_batch) . '" class="form-control" size=10 placeHolder="Батч" ' . $readonly . '>';
                }),

            TD::make('ao_expiration_date', 'Срок годности')
                ->sort()
                ->render(function (rwAcceptanceOffer $modelName) {

                    $readonly = '';
                    if ($modelName->ao_placed > 0) $readonly = 'readonly';

                    $input = Input::make('docOfferExpDate[' . $modelName->ao_id . ']')
                        ->type('text')
                        ->value($modelName->ao_expiration_date)
                        ->mask([
                            'alias' => 'datetime',
                            'inputFormat' => 'dd.mm.yyyy',
                            'placeholder' => __('дд.мм.гггг'),
                        ])
                        ->class('form-control');

                    if ($readonly == 'readonly') {
                        $input->readonly(); // Применяем readonly только если $readonly === true
                    }

                    return $input;
                }),

            TD::make('ao_barcode', 'Штрих-код')
                ->sort()
                ->render(function (rwAcceptanceOffer $modelName) {
                    $bgColor = '';
                    if ($modelName->ao_barcode == '') $bgColor = 'style="background-color: #ffbdbf;"';
                    $readonly = '';
                    if ($modelName->ao_placed > 0) $readonly = 'readonly';
                    return '<input type="text" name="docOfferBarcode[' . $modelName->ao_id . ']" value="' . e($modelName->ao_barcode) . '" class="form-control" size=15 placeHolder="Штрих-код" ' . $bgColor . ' ' . $readonly . '>';
                }),

            TD::make('ao_expected', 'Ожидается')
                ->sort()
                ->render(function (rwAcceptanceOffer $modelName) {
                    $bgColor = '';
                    if ($modelName->ao_expected == 0) $bgColor = 'style="background-color: #ffbdbf;"';
                    $readonly = '';
                    if ($modelName->ao_placed > 0) $readonly = 'readonly';
                    return '<input type="text" name="docOfferExept[' . $modelName->ao_id . ']" value="' . e($modelName->ao_expected) . '" class="form-control" size=6 placeHolder="Ожидается" ' . $bgColor . ' ' . $readonly . '>';
                }),

            TD::make('ao_accepted', 'Принято')
                ->sort()
                ->render(function (rwAcceptanceOffer $modelName) {
                    $isEditable = RoleMiddleware::checkUserPermission('admin,warehouse_manager'); // Проверка роли
                    $readonly = $isEditable ? '' : 'readonly'; // Установка атрибута readonly для пользователей без прав
                    if ($modelName->ao_placed > 0 || $modelName->oa_status == 1) $readonly = 'readonly';
                    return '<input type="text" name="docOfferAccept[' . $modelName->ao_id . ']" value="' . e($modelName->ao_accepted) . '" class="form-control" size=6 placeHolder="Принято" ' . $readonly . '>';
                }),

            TD::make('ao_placed', 'Размещено')
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->render(function (rwAcceptanceOffer $modelName) {
                    if ($modelName->ao_placed > 0)
                        return 'тут будет место';
                    else
                        return '-';
                }),

            TD::make('ao_price', 'Закупочная цена')
                ->sort()
                ->render(function (rwAcceptanceOffer $modelName) {
                    $readonly = '';
                    if ($modelName->ao_placed > 0) $readonly = 'readonly';
                    return '<input type="text" name="docOfferPrice[' . $modelName->ao_id . ']" value="' . e($modelName->ao_price) . '" class="form-control" size=6 placeHolder="Цена" ' . $readonly . '>';
                }),

            TD::make(__('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn(rwAcceptanceOffer $modelName) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list(array_filter([
                        ($modelName->ao_placed === null && $modelName->oa_status == 1)
                            ? Button::make(__('Удалить'))
                            ->icon('bs.trash')
                            ->method('deleteItem')
                            ->parameters([
                                'offerId' => $modelName->ao_id,
                                'docType' => 1,
                                '_token' => csrf_token(), // Добавляем CSRF-токен вручную
                            ])
                            ->confirm(__('Вы уверены, что хотите удалить этот товар из накладной?'))
                            : null, // Кнопка не добавляется, если условие не выполнено
                    ]))
                ),

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
