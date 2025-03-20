<?php

namespace App\Orchid\Screens\Offers;

use App\Http\Middleware\Offers\OffersMiddleware;
use App\Models\rwBarcode;
use App\Models\rwLibStatus;
use App\Models\rwOffer;
use App\Models\rwShop;
use App\Models\rwWarehouse;
use App\Models\WhcRest;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
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
    public $offerId = 0, $offerName = '', $shopName = '';

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query($offerId = 0): iterable
    {
        $currentUser = Auth::user();

        $this->offerId = $offerId;

        $arRests = [];
        $arWhRests = [];

        $dbOffer = rwOffer::with('getShop')->where('of_id', $offerId)->first();

        if ($offerId == 0) {
            return [];
        } else {

            $this->offerName = $dbOffer->of_name;
            $this->shopName = $dbOffer->getShop->sh_name;

            $dbRests = WhcRest::where('whcr_offer_id', $offerId)
                ->with('getPlace')
                ->orderBy('whcr_wh_id', 'ASC')
                ->orderBy('whcr_count', 'DESC')
                ->get();

            foreach ($dbRests as $rest) {

                $dbWh = rwWarehouse::where('wh_id', $rest->whcr_wh_id)->first();

                $placeStr = '-';
                if (isset($rest->getPlace->pl_id)) {
                    if ($rest->getPlace->pl_room) $placeStr = $rest->getPlace->pl_room;
                    if ($rest->getPlace->pl_floor) $placeStr .= '-' . $rest->getPlace->pl_floor;
                    if ($rest->getPlace->pl_section) $placeStr .= '-' . $rest->getPlace->pl_section;
                    if ($rest->getPlace->pl_row) $placeStr .= '-' . $rest->getPlace->pl_row;
                    if ($rest->getPlace->pl_rack) $placeStr .= '-' . $rest->getPlace->pl_rack;
                    if ($rest->getPlace->pl_shelf) $placeStr .= '-' . $rest->getPlace->pl_shelf;
                }

                $arRests[] = [
                    'whName'    => $dbWh->wh_name,
                    'whId'      => $rest->whcr_wh_id,
                    'placeId'   => $rest->whcr_place_id,
                    'placeName' => $placeStr,
                    'count'     => $rest->whcr_count,
                ];

                if (isset($arWhRests[$rest->whcr_wh_id])) {
                    $arWhRests[$rest->whcr_wh_id] = [
                        'whName'    => $dbWh->wh_name,
                        'count'     => $arWhRests[$rest->whcr_wh_id]['count'] + $rest->whcr_count,
                    ];
                } else {
                    $arWhRests[$rest->whcr_wh_id] = [
                        'whName'    => $dbWh->wh_name,
                        'count'     => $rest->whcr_count,
                    ];
                }
            }

            $dbBarcodes = rwBarcode::where('br_offer_id', $offerId)->get();

            return [
                'rwOffer'       => $dbOffer,
                'rests'         => $arRests,
                'whRests'         => $arWhRests,
                'offerId'       => $offerId,
                'barcodesList'  => $dbBarcodes,
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
        return [];
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

                        ]),

                        Button::make(CustomTranslator::get('Сохранить'))
                            ->type(Color::DARK)
                            ->style('margin-bottom: 20px;')
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


    function saveOffer(Request $request)
    {
        $currentUser = Auth::user();

        $request->validate([
            'rwOffer.of_id' => 'nullable|integer',
            'rwOffer.of_name' => 'required|string|max:150',
            'rwOffer.of_img' => 'nullable|string|max:255',
            'rwOffer.of_shop_id' => 'nullable|integer',
            'rwOffer.of_status' => 'required|integer',
            'rwOffer.of_article' => 'required|string|max:25',
            'rwOffer.of_sku' => 'nullable|string|max:25',
            'rwOffer.of_weight' => 'nullable|integer',
            'rwOffer.of_dimension_x' => 'nullable|numeric',
            'rwOffer.of_dimension_y' => 'nullable|numeric',
            'rwOffer.of_dimension_z' => 'nullable|numeric',
            'rwOffer.of_price' => 'nullable|numeric',
            'rwOffer.of_estimated_price' => 'nullable|numeric',
            'rwOffer.of_comment' => 'nullable|string|max:255',
        ]);

        if (isset($request->rwOffer['of_id']) && $request->rwOffer['of_id'] > 0) {

            rwOffer::where('of_id', $request->rwOffer['of_id'])->update([
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
                'of_comment' => $request->rwOffer['of_comment'],
            ]);

            Alert::success(CustomTranslator::get('Данные успешно отредактированы!'));

        } else {

            rwOffer::insert([
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
                'of_comment' => $request->rwOffer['of_comment'],
            ]);

            Alert::success(CustomTranslator::get('Данные успешно добавлены!'));

        }


        return redirect()->route('platform.offers.index');
    }

}
