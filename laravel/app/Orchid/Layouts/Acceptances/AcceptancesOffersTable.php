<?php

namespace App\Orchid\Layouts\Acceptances;

use App\Http\Middleware\RoleMiddleware;
use App\Models\rwAcceptanceOffer;
use App\Services\CustomTranslator;
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

    protected function columns(array $params = []): iterable
    {
        $isProductionDate = (bool) $this->query->get('isProductionDate');
        $isExpirationDate = (bool) $this->query->get('isExpirationDate');
        $isBatch = (bool) $this->query->get('isBatch');

        return [

            TD::make('ao_id', 'ID')
                ->sort()
                ->align('center')
                ->render(function (rwAcceptanceOffer $modelName) {
                    return $modelName->ao_id . '<input type="hidden" name="docOfferId[' . $modelName->ao_id . ']" value="' . e($modelName->ao_offer_id) . '" >
                    <input type="hidden" name="docOfferPlaced[' . $modelName->ao_id . ']" value="' . e($modelName->ao_placed) . '" >';
                }),

            TD::make(CustomTranslator::get('Изображение'))
                ->align('center')
                ->render(function ($model) {
                    if ($model->ao_img == '')
                        return "<img src='/img/no_image.png' alt='Image' width='75' height='75'>";
                    else
                        return "<img src='{$model->ao_img}' alt='Image' width='75' height='75'>";
                })
                ->width('100px'),

            TD::make('ao_article', CustomTranslator::get('Артикул'))
                ->sort(),

            TD::make('ao_name', CustomTranslator::get('Товар'))
                ->sort(),

            TD::make('ao_dimension', CustomTranslator::get('Размеры'))
                ->sort()
                ->render(function (rwAcceptanceOffer $modelName) {
                    $currentUser = Auth::user(); // Получение текущего пользователя

                    if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {
                        // Если пользователь имеет нужную роль, показываем модальное окно
                        return ModalToggle::make(
                            $modelName->ao_dimension,
                        )
                            ->modal('editDimensions') // Имя модального окна
                            ->modalTitle(CustomTranslator::get('Редактировать размеры'))
                            ->method('saveDimensions') // Метод для обработки данных
                            ->parameters([
                                'offerId' => $modelName->ao_id,
                            ]);
                    } else {
                        // Если пользователь не имеет нужной роли, отображаем просто текст
                        return e($modelName->ao_dimension);
                    }
                }),

            TD::make('ao_batch', CustomTranslator::get('Партия'))
                ->sort()
                ->canSee($isBatch)
                ->render(function (rwAcceptanceOffer $modelName) {
                    $readonly = '';
                    if ($modelName->ao_placed > 0 || $modelName->oa_status > 3) $readonly = 'readonly';
                    return '<input type="text" name="docOfferBatch[' . $modelName->ao_id . ']" value="' . e($modelName->ao_batch) . '" class="form-control" size=10 placeHolder="'.CustomTranslator::get('Батч').'" ' . $readonly . '>';
                }),

            TD::make('ao_production_date', CustomTranslator::get('Дата производства'))
                ->sort()
                ->canSee($isProductionDate)
                ->render(function (rwAcceptanceOffer $modelName) {

                    $readonly = '';
                    if ($modelName->ao_placed > 0) $readonly = 'readonly';

                    $input = Input::make('docOfferProdDate[' . $modelName->ao_id . ']')
                        ->type('text')
                        ->value($modelName->ao_production_date)
                        ->mask([
                            'alias' => 'datetime',
                            'inputFormat' => 'dd.mm.yyyy',
                            'placeholder' => CustomTranslator::get('дд.мм.гггг'),
                        ])
                        ->class('form-control');

                    if ($modelName->ao_placed > 0 || $modelName->oa_status > 3) {
                        $input->readonly(); // Применяем readonly только если $readonly === true
                    }

                    return $input;
                }),

            TD::make('ao_expiration_date', CustomTranslator::get('Срок годности'))
                ->sort()
                ->canSee($isExpirationDate)
                ->render(function (rwAcceptanceOffer $modelName) {

                    $readonly = '';
                    if ($modelName->ao_placed > 0) $readonly = 'readonly';

                    $input = Input::make('docOfferExpDate[' . $modelName->ao_id . ']')
                        ->type('text')
                        ->value($modelName->ao_expiration_date)
                        ->mask([
                            'alias' => 'datetime',
                            'inputFormat' => 'dd.mm.yyyy',
                            'placeholder' => CustomTranslator::get('дд.мм.гггг'),
                        ])
                        ->class('form-control');

                    if ($modelName->ao_placed > 0 || $modelName->oa_status > 3) {
                        $input->readonly(); // Применяем readonly только если $readonly === true
                    }

                    return $input;
                }),

            TD::make('ao_barcode', CustomTranslator::get('Штрих-код'))
                ->sort()
                ->render(function (rwAcceptanceOffer $modelName) {
                    $bgColor = '';
                    if ($modelName->ao_barcode == '') $bgColor = 'style="background-color: #ffbdbf;"';
                    $readonly = '';
                    if ($modelName->ao_placed > 0 || $modelName->oa_status > 3) $readonly = 'readonly';
                    return '<input type="text" name="docOfferBarcode[' . $modelName->ao_id . ']" value="' . e($modelName->ao_barcode) . '" class="form-control" size=15 placeHolder="'.CustomTranslator::get('Штрих-код').'" ' . $bgColor . ' ' . $readonly . '>';
                }),

            TD::make('ao_expected', CustomTranslator::get('Ожидается'))
                ->sort()
                ->render(function (rwAcceptanceOffer $modelName) {
                    $bgColor = '';
                    if ($modelName->ao_expected == 0) $bgColor = 'style="background-color: #ffbdbf;"';
                    $readonly = '';
                    if ($modelName->ao_placed > 0 || $modelName->oa_status > 2) $readonly = 'readonly';
                    return '<input type="text" name="docOfferExept[' . $modelName->ao_id . ']" value="' . e($modelName->ao_expected) . '" class="form-control" size=6 placeHolder="'.CustomTranslator::get('Ожидается').'" ' . $bgColor . ' ' . $readonly . '>';
                }),

            TD::make('ao_accepted', CustomTranslator::get('Принято'))
                ->sort()
                ->render(function (rwAcceptanceOffer $modelName) {
                    $isEditable = RoleMiddleware::checkUserPermission('admin,warehouse_manager'); // Проверка роли
                    $readonly = $isEditable ? '' : 'readonly'; // Установка атрибута readonly для пользователей без прав
                    if ($modelName->ao_placed > 0 || $modelName->oa_status == 1 || $modelName->oa_status > 3) $readonly = 'readonly';
                    return '<input type="text" name="docOfferAccept[' . $modelName->ao_id . ']" value="' . e($modelName->ao_accepted) . '" class="form-control" size=6 placeHolder="Принято" ' . $readonly . '>';
                }),

            TD::make('ao_placed', CustomTranslator::get('Размещено'))
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->render(function (rwAcceptanceOffer $modelName) {
                    if ($modelName->ao_placed > 0)
                        return $modelName->ao_placed;
                    else
                        return '-';
                }),

            TD::make('ao_price', CustomTranslator::get('Закупочная цена'))
                ->sort()
                ->render(function (rwAcceptanceOffer $modelName) {
                    $readonly = '';
                    if ($modelName->ao_placed > 0 || $modelName->oa_status > 3) $readonly = 'readonly';
                    return '<input type="text" name="docOfferPrice[' . $modelName->ao_id . ']" value="' . e($modelName->ao_price) . '" class="form-control" size=6 placeHolder="'.CustomTranslator::get('Цена').'" ' . $readonly . '>';
                }),

            TD::make(CustomTranslator::get('Действия'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn(rwAcceptanceOffer $modelName) =>
                (($modelName->ao_placed === null || $modelName->ao_placed == 0) && $modelName->oa_status == 1)
                    ? Button::make(' Удалить')
                    ->icon('bs.trash')
                    ->method('deleteItem')
                    ->parameters([
                        'acceptId' => $modelName->ao_acceptance_id,
                        'offerId' => $modelName->ao_id,
                        'docType' => 1,
                        '_token' => csrf_token(), // Добавляем CSRF-токен вручную
                    ])
                    ->confirm(CustomTranslator::get('Вы уверены, что хотите удалить этот товар из накладной?'))
                    ->style('color: red;')
                    : null
                ),

        ];
    }

    protected function toolbar(): array
    {
        return [
            Button::make(CustomTranslator::get('Сохранить изменения'))
                ->method('saveChanges')
                ->class('btn btn-primary'),
        ];
    }
}
