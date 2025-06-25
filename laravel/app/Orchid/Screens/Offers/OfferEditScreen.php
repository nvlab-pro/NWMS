<?php

namespace App\Orchid\Screens\Offers;

use App\Http\Middleware\Offers\OffersMiddleware;
use App\Models\rwBarcode;
use App\Models\rwLibStatus;
use App\Models\rwOffer;
use App\Models\rwOrder;
use App\Models\rwShop;
use App\Models\rwWarehouse;
use App\Models\WhcRest;
use App\Services\CustomTranslator;
use App\WhPlaces\WhPlaces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class OfferEditScreen extends Screen
{
    public $offerId = 0, $offerName = '', $shopName = '', $dbOffer;

    public function query(Request $request, $offerId = 0): iterable
    {
        $currentUser = Auth::user();

        $this->offerId = $offerId;

        $arRests = [];
        $arWhRests = [];

        $dbOffer = rwOffer::with('getShop')->where('of_id', $offerId)->first();

        if ($offerId == 0) {
            return [];
        } else {

            // Удаляем штрих-код
            if (isset($request->action) && $request->action == 'delete') {

                rwBarcode::where('br_id', $request->barcodeId)->where('br_offer_id', $offerId)->delete();
                Alert::error(CustomTranslator::get('Штрих-код был удален!'));

            }

            // Проставляем основной штрих-код
            if (isset($request->action) && $request->action == 'selectMainBarcode') {

                rwBarcode::where('br_offer_id', $offerId)
                    ->update([
                    'br_main'   => 0,
                ]);
                rwBarcode::where('br_id', $request->barcodeId)
                    ->where('br_offer_id', $offerId)
                    ->update([
                    'br_main'   => 1,
                ]);

            }

            $this->offerName = $dbOffer->of_name;
            $this->shopName = $dbOffer->getShop->sh_name;

            $this->dbOffer = $dbOffer;

            $arWhRests = WhPlaces::getPlacesList($offerId);

            $dbBarcodes = rwBarcode::where('br_offer_id', $offerId)->get();

            return [
                'rwOffer' => $dbOffer,
                'rests' => $arWhRests['arRests'] ?? null,
                'whRests' => $arWhRests['arWhRests'] ?? null,
                'offerId' => $offerId,
                'barcodesList' => $dbBarcodes,
            ];

        }
    }

    public function name(): ?string
    {
        return $this->offerId > 0 ? $this->offerName : CustomTranslator::get('Создание нового товара');
    }

    public function description(): ?string
    {
        return CustomTranslator::get('Магазин: ') . $this->shopName;
    }

    public function commandBar(): iterable
    {
        return [

            Button::make(' ' . CustomTranslator::get('Удалить'))
                ->icon('bs.trash3-fill')
                ->style('border: 1px solid #FF0000; background-color: #FF0000; color: #FFFFFF; border-radius: 10px;')
                ->confirm(CustomTranslator::get('Вы уверены, что хотите удалить этот товар?'))
                ->method('offerDelete')
                ->canSee(in_array($this->dbOffer->of_status, [3]))
                ->parameters([
                    '_token' => csrf_token(),
                    'offerId' => $this->dbOffer->of_id,
                    'status' => $this->dbOffer->of_status,
                ]),

        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        $currentUser = Auth::user();
        $dbShopsList = rwShop::where('sh_domain_id', $currentUser->domain_id);

//        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {
//
//
//        } else {
//
//            $dbShopsList = $dbShopsList->where('sh_user_id', $currentUser->id);
//
//        }
        $statuses = rwLibStatus::all()->pluck('ls_name', 'ls_id')->toArray();

        foreach ($statuses as $id => $name) {
            $statuses[$id] = CustomTranslator::get($name);
        }

        return [
            Layout::tabs([
                CustomTranslator::get('Основная') => [
                    Layout::rows([

                        Input::make('rwOffer.of_id')
                            ->type('hidden')
                            ->width(50),

                        Group::make([

                            Input::make('rwOffer.of_name')
                                ->width(50)
                                ->title(CustomTranslator::get('Название')),

                            Select::make('rwOffer.of_status')
                                ->title(CustomTranslator::get('Статус'))
                                ->width('100px')
                                ->options($statuses) // Указываем переведенные статусы
                                ->required(0),
                        ]),

                        Group::make([

//                            Select::make('rwOffer.of_shop_id')
//                                ->title(CustomTranslator::get('Магазин'))
//                                ->width('100px')
//                                ->fromModel($dbShopsList->get(), 'sh_name', 'sh_id')
//                                ->disabled(OffersMiddleware::checkRule4SelectShop($this->offerId, 'admin,warehouse_manager')),

                            Select::make('rwOffer.of_shop_id')
                                ->title(CustomTranslator::get('Магазин'))
                                ->width('100px')
                                ->fromModel(
                                    $currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')
                                        ? $dbShopsList->get()
                                        : $dbShopsList->whereIn('sh_user_id', [$currentUser->id, $currentUser->parent_id])->get(),
                                    'sh_name',
                                    'sh_id'
                                )
                                ->disabled(OffersMiddleware::checkRule4SelectShop($this->offerId, 'admin,warehouse_manager')),

                            Input::make('rwOffer.of_img')
                                ->width(50)
                                ->title(CustomTranslator::get('URL изображения')),

                        ]),
                        Group::make([

                            Input::make('rwOffer.of_article')
                                ->width(50)
                                ->title(CustomTranslator::get('Артикул')),

                            Input::make('rwOffer.of_weight')
                                ->width(10)
                                ->title(CustomTranslator::get('Вес (гр)')),

                        ]),
                        Group::make([

                            Input::make('rwOffer.of_sku')
                                ->width(50)
                                ->title(CustomTranslator::get('SKU')),

                            Input::make('rwOffer.of_dimension_x')
                                ->width(10)
                                ->title(CustomTranslator::get('Длина (мм)')),

                        ]),
                        Group::make([

                            Input::make('rwOffer.of_price')
                                ->width(50)
                                ->title(CustomTranslator::get('Стоимость')),

                            Input::make('rwOffer.of_dimension_y')
                                ->width(10)
                                ->title(CustomTranslator::get('Ширина (мм)')),

                        ]),
                        Group::make([

                            Input::make('rwOffer.of_estimated_price')
                                ->width(50)
                                ->title(CustomTranslator::get('Оценочная стоимость')),

                            Input::make('rwOffer.of_dimension_z')
                                ->width(10)
                                ->title(CustomTranslator::get('Высота (мм)')),

                        ]),
                        Group::make([

                            TextArea::make('rwOffer.of_comment')
                                ->width(50)
                                ->title(CustomTranslator::get('Комментарий')),

                            CheckBox::make('rwOffer.of_datamatrix')
                                ->sendTrueOrFalse() // Orchid будет отправлять 1 или 0
                                ->title(CustomTranslator::get('Честный знак')),

                        ]),

                        Button::make(CustomTranslator::get('Сохранить'))
                            ->type(Color::DARK)
                            ->style('margin-bottom: 20px;')
                            ->parameters([
                                '_token' => csrf_token(),
                            ])
                            ->method('saveOffer'),
                    ]),

                ],
                CustomTranslator::get('Штрих-кода') => [
                    Layout::view('Offers.barcodesList'),
                ],
                CustomTranslator::get('Остатки') => [
                    Layout::view('Offers.restsOffer'),
                ],
                CustomTranslator::get('Места размещения') => [
                    Layout::view('Offers.placesOffer'),
                ],
                CustomTranslator::get('История') => [
                ],
            ]),
        ];
    }

    public function offerDelete(Request $request)
    {
        $validatedData = $request->validate([
            'offerId' => 'nullable|numeric|min:0',
            'status' => 'nullable|numeric|min:0',
        ]);

        if ($validatedData['status'] == 3) {

            rwOffer::where('of_id', $validatedData['offerId'])
                ->delete();

            Alert::error(CustomTranslator::get('Товар') . ' ' . $validatedData['offerId'] . ' ' . CustomTranslator::get('был удален!'));

        } else {

            Alert::error(CustomTranslator::get('Товар') . ' ' . $validatedData['offerId'] . ' ' . CustomTranslator::get('не может быть удален!'));

        }

        return redirect()->route('platform.offers.index');
    }

    function saveOffer(Request $request)
    {
        $currentUser = Auth::user();

        $request->validate([
            'rwOffer.of_id' => 'nullable|integer',
            'rwOffer.of_name' => 'required|string|max:150',
            'rwOffer.of_img' => 'nullable|string|max:255',
            'rwOffer.of_shop_id' => 'nullable|integer',
            'rwOffer.of_status' => 'required|integer',
            'rwOffer.of_article' => 'nullable|string|max:25',
            'rwOffer.of_sku' => 'nullable|string|max:25',
            'rwOffer.of_weight' => 'nullable|integer',
            'rwOffer.of_dimension_x' => 'nullable|numeric',
            'rwOffer.of_dimension_y' => 'nullable|numeric',
            'rwOffer.of_dimension_z' => 'nullable|numeric',
            'rwOffer.of_price' => 'nullable|numeric',
            'rwOffer.of_estimated_price' => 'nullable|numeric',
            'rwOffer.of_datamatrix' => 'nullable|numeric',
            'rwOffer.of_comment' => 'nullable|string|max:255',
        ]);

        if (isset($request->rwOffer['of_id']) && $request->rwOffer['of_id'] > 0) {

            // Найдём и обновим через Eloquent
            $offer = rwOffer::find($request->rwOffer['of_id']);

            if ($offer) {
                $offer->fill([
                    'of_name' => $request->rwOffer['of_name'],
                    'of_img' => $request->rwOffer['of_img'],
                    'of_status' => $request->rwOffer['of_status'],
                    'of_article' => $request->rwOffer['of_article'],
                    'of_sku' => $request->rwOffer['of_sku'],
                    'of_weight' => $request->rwOffer['of_weight'],
                    'of_dimension_x' => $request->rwOffer['of_dimension_x'],
                    'of_dimension_y' => $request->rwOffer['of_dimension_y'],
                    'of_dimension_z' => $request->rwOffer['of_dimension_z'],
                    'of_price' => $request->rwOffer['of_price'],
                    'of_estimated_price' => $request->rwOffer['of_estimated_price'],
                    'of_datamatrix' => $request->rwOffer['of_datamatrix'],
                    'of_comment' => $request->rwOffer['of_comment'],
                ])->save();

                Alert::success(CustomTranslator::get('Данные успешно отредактированы!'));
            }

        } else {
            // Создаём новую модель через Eloquent
            $offer = new rwOffer([
                'of_name' => $request->rwOffer['of_name'],
                'of_img' => $request->rwOffer['of_img'],
                'of_shop_id' => $request->rwOffer['of_shop_id'],
                'of_status' => $request->rwOffer['of_status'],
                'of_article' => $request->rwOffer['of_article'],
                'of_sku' => $request->rwOffer['of_sku'],
                'of_weight' => $request->rwOffer['of_weight'],
                'of_dimension_x' => $request->rwOffer['of_dimension_x'],
                'of_dimension_y' => $request->rwOffer['of_dimension_y'],
                'of_dimension_z' => $request->rwOffer['of_dimension_z'],
                'of_price' => $request->rwOffer['of_price'],
                'of_estimated_price' => $request->rwOffer['of_estimated_price'],
                'of_domain_id' => $currentUser->domain_id,
                'of_datamatrix' => $currentUser->of_datamatrix,
                'of_comment' => $request->rwOffer['of_comment'],
            ]);

            $offer->save(); // создаёт и вызывает аудит

            Alert::success(CustomTranslator::get('Данные успешно добавлены!'));
        }


        return redirect()->route('platform.offers.edit', $offer->of_id);
    }

    function saveBarcode(Request $request)
    {
        $validatedData = $request->validate([
            'br_id' => 'required|numeric',
            'offerId' => 'required|numeric',
            'br_barcode' => 'required|string',
        ]);

        rwBarcode::where('br_id', $validatedData['br_id'])
            ->where('br_offer_id', $validatedData['offerId'])
            ->update([
                'br_barcode' => $validatedData['br_barcode'],
            ]);

        Alert::success(CustomTranslator::get('Штрих-код обновлен!'));

    }

    function addBarcode(Request $request)
    {
        $validatedData = $request->validate([
            'offerId' => 'required|numeric',
            'barcode' => 'required|string',
        ]);

        $dbOffer = rwOffer::where('of_id', $validatedData['offerId'])->first();

        $barcode = new rwBarcode([
            'br_offer_id' => $validatedData['offerId'],
            'br_shop_id' => $dbOffer->of_shop_id,
            'br_barcode' => $validatedData['barcode'],
        ]);

        $barcode->save(); // создаёт и вызывает аудит

        Alert::success(CustomTranslator::get('Штрих-код добавлен!'));

    }
}