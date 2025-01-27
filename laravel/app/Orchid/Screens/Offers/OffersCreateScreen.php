<?php

namespace App\Orchid\Screens\Offers;

use App\Http\Middleware\Offers\OffersMiddleware;
use App\Models\rwLibStatus;
use App\Models\rwOffer;
use App\Models\rwShop;
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

class OffersCreateScreen extends Screen
{
    public $offerId = 0;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query($offerId = 0): iterable
    {

        $this->offerId = $offerId;

        if ($offerId == 0) {
            return [];
        } else {
            return [
                'rwOffer' => rwOffer::where('of_id', $offerId)->first(),
            ];
        }
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->offerId > 0 ? __('Редактирование товара') : __('Создание нового товара');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
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

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {


        } else {

            $dbShopsList = $dbShopsList->where('sh_user_id', $currentUser->id);

        }

        return [
            Layout::tabs([
                'Основная' => [
                    Layout::rows([

                        Input::make('rwOffer.of_id')
                            ->type('hidden')
                            ->width(50),

                        Group::make([

                            Input::make('rwOffer.of_name')
                                ->width(50)
                                ->title(__('Название')),

                            Select::make('rwOffer.of_status')
                                ->title(__('Статус'))
                                ->width('100px')
                                ->fromModel(rwLibStatus::class, 'ls_name', 'ls_id')
                                ->required(0),
                        ]),

                        Group::make([

                            Select::make('rwOffer.of_shop_id')
                                ->title(__('Магазин'))
                                ->width('100px')
                                ->fromModel($dbShopsList->get(), 'sh_name', 'sh_id')
                                ->disabled(OffersMiddleware::checkRule4SelectShop($this->offerId, 'admin,warehouse_manager')),

                            Input::make('rwOffer.of_img')
                                ->width(50)
                                ->title(__('URL изображения')),

                        ]),
                        Group::make([

                            Input::make('rwOffer.of_article')
                                ->width(50)
                                ->title(__('Артикул')),

                            Input::make('rwOffer.of_weight')
                                ->width(10)
                                ->title(__('Вес (гр)')),

                        ]),
                        Group::make([

                            Input::make('rwOffer.of_sku')
                                ->width(50)
                                ->title(__('SKU')),

                            Input::make('rwOffer.of_dimension_x')
                                ->width(10)
                                ->title(__('Длина (мм)')),

                        ]),
                        Group::make([

                            Input::make('rwOffer.of_price')
                                ->width(50)
                                ->title(__('Стоимость')),

                            Input::make('rwOffer.of_dimension_y')
                                ->width(10)
                                ->title(__('Ширина (мм)')),

                        ]),
                        Group::make([

                            Input::make('rwOffer.of_estimated_price')
                                ->width(50)
                                ->title(__('Оценочная стоимость')),

                            Input::make('rwOffer.of_dimension_z')
                                ->width(10)
                                ->title(__('Высота (мм)')),

                        ]),
                        Group::make([

                            TextArea::make('rwOffer.of_comment')
                                ->width(50)
                                ->title(__('Комментарий')),

                        ]),

                        Button::make(__('Сохранить'))
                            ->type(Color::DARK)
                            ->style('margin-bottom: 20px;')
                            ->method('saveOffer'),
                    ]),

                ],
                'Остатки' => [
                    Layout::rows([
                    ]),
                ],
                'Движение товара' => [
                    Layout::rows([
                    ]),
                ],
                'История' => [
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
                'of_name'               => $request->rwOffer['of_name'],
                'of_img'                => $request->rwOffer['of_img'],
                'of_status'             => $request->rwOffer['of_status'],
                'of_article'            => $request->rwOffer['of_article'],
                'of_sku'                => $request->rwOffer['of_sku'],
                'of_weight'             => $request->rwOffer['of_weight'],
                'of_dimension_x'        => $request->rwOffer['of_dimension_x'],
                'of_dimension_y'        => $request->rwOffer['of_dimension_y'],
                'of_dimension_z'        => $request->rwOffer['of_dimension_z'],
                'of_price'              => $request->rwOffer['of_price'],
                'of_estimated_price'    => $request->rwOffer['of_estimated_price'],
                'of_comment'            => $request->rwOffer['of_comment'],
            ]);

            Alert::success(__('Данные успешно отредактированы!'));

        } else {

            rwOffer::insert([
                'of_name'               => $request->rwOffer['of_name'],
                'of_img'                => $request->rwOffer['of_img'],
                'of_shop_id'            => $request->rwOffer['of_shop_id'],
                'of_status'             => $request->rwOffer['of_status'],
                'of_article'            => $request->rwOffer['of_article'],
                'of_sku'                => $request->rwOffer['of_sku'],
                'of_weight'             => $request->rwOffer['of_weight'],
                'of_dimension_x'        => $request->rwOffer['of_dimension_x'],
                'of_dimension_y'        => $request->rwOffer['of_dimension_y'],
                'of_dimension_z'        => $request->rwOffer['of_dimension_z'],
                'of_price'              => $request->rwOffer['of_price'],
                'of_estimated_price'    => $request->rwOffer['of_estimated_price'],
                'of_domain_id'          => $currentUser->domain_id,
                'of_comment'            => $request->rwOffer['of_comment'],
            ]);

            Alert::success(__('Данные успешно добавлены!'));

        }


        return redirect()->route('platform.offers.index');
    }

}
