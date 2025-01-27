<?php

namespace App\Orchid\Screens\Acceptances;

use App\Http\Middleware\Offers\OffersMiddleware;
use App\Models\rwAcceptance;
use App\Models\rwLibAcceptStatus;
use App\Models\rwLibAcceptType;
use App\Models\rwShop;
use App\Models\rwWarehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class AcceptanceCreateScreen extends Screen
{
    public $acceptId = 0;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query($acceptId = 0): iterable
    {

        $this->acceptId = $acceptId;

        if ($acceptId == 0) {
            return [];
        } else {
            return [
                'rwAcceptance' => rwAcceptance::where('acc_id', $acceptId)->first(),
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
        return $this->acceptId > 0 ? __('Редактирование накладной') : __('Создание новой накладной');
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
        $dbWhList = rwWarehouse::where('wh_domain_id', $currentUser->domain_id)->where('wh_type', 2);
        $dbShopList = rwShop::where('sh_domain_id', $currentUser->domain_id);

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {

        } else {

            $dbWhList = $dbWhList->where('wh_user_id', $currentUser->id);
            $dbShopList = $dbShopList->where('sh_user_id', $currentUser->id);

        }

        return [
            Layout::rows([

                Input::make('rwAcceptance.acc_id')
                    ->type('hidden')
                    ->width(50),

                Group::make([

                    Select::make('rwAcceptance.acc_wh_id')
                        ->title(__('Склад'))
                        ->width('100px')
                        ->fromModel($dbWhList->get(), 'wh_name', 'wh_id')
                        ->required()
                        ->disabled(OffersMiddleware::checkRule4SelectShop($this->acceptId, 'admin,warehouse_manager')),

                    Select::make('rwAcceptance.acc_shop_id')
                        ->title(__('Магазин'))
                        ->width('100px')
                        ->fromModel($dbShopList->get(), 'sh_name', 'sh_id')
                        ->required()
                        ->disabled(OffersMiddleware::checkRule4SelectShop($this->acceptId, 'admin,warehouse_manager')),

                ]),

                Group::make([

                    Input::make('rwAcceptance.acc_ext_id')
                        ->width(50)
                        ->title(__('Внешний ID')),

                    DateTimer::make('rwAcceptance.acc_date')
                        ->width(50)
                        ->title(__('Дата'))
                        ->required()
                        ->format('Y-m-d')
                        ->disabled(OffersMiddleware::checkRule4SelectShop($this->acceptId, 'admin,warehouse_manager'))
                        ->value(now()->format('Y-m-d')),

                ]),
                Group::make([

                    Select::make('rwAcceptance.acc_type')
                        ->fromModel(model: rwLibAcceptType::class, name: 'lat_name', key: 'lat_id')
                        ->disabled(OffersMiddleware::checkRule4SelectShop($this->acceptId, 'admin,warehouse_manager'))
                        ->value(1)
                        ->title(__('Тип накладной')),

                    TextArea::make('rwAcceptance.acc_comment')
                        ->width(50)
                        ->title(__('Комментарий')),

                ]),

                Button::make(__('Сохранить'))
                    ->type(Color::DARK)
                    ->style('margin-bottom: 20px;')
                    ->method('saveAcceptance'),
            ])
        ];
    }


    function saveAcceptance(Request $request)
    {
        $currentUser = Auth::user();

        $request->validate([
            'rwAcceptance.acc_id' => 'nullable|integer',
            'rwAcceptance.acc_ext_id' => 'nullable|integer',
            'rwAcceptance.acc_wh_id' => 'required|integer',
            'rwAcceptance.acc_shop_id' => 'required|integer',
            'rwAcceptance.acc_date' => 'required|string|max:10',
            'rwAcceptance.acc_type' => 'required|integer',
            'rwAcceptance.acc_comment' => 'nullable|string|max:255',
        ]);

        if (isset($request->rwAcceptance['acc_id']) && $request->rwAcceptance['acc_id'] > 0) {

            rwAcceptance::where('acc_id', $request->rwAcceptance['acc_id'])->update([
                'acc_ext_id' => $request->rwAcceptance['acc_ext_id'],
                'acc_date' => $request->rwAcceptance['acc_date'],
                'acc_type' => $request->rwAcceptance['acc_type'],
                'acc_comment' => $request->rwAcceptance['acc_comment'],
            ]);

            Alert::success(__('Данные успешно отредактированы!'));

        } else {

            $userId = rwWarehouse::query()->where('wh_id', $request->rwAcceptance['acc_wh_id'])->first()->wh_user_id;

            rwAcceptance::create([
                'acc_status'        => 1,
                'acc_ext_id'        => $request->rwAcceptance['acc_ext_id'],
                'acc_user_id'       => $userId,
                'acc_wh_id'         => $request->rwAcceptance['acc_wh_id'],
                'acc_shop_id'       => $request->rwAcceptance['acc_shop_id'],
                'acc_date'          => $request->rwAcceptance['acc_date'],
                'acc_type'          => $request->rwAcceptance['acc_type'],
                'acc_domain_id'     => $currentUser->domain_id,
                'acc_comment'       => $request->rwAcceptance['acc_comment'],
            ]);

            Alert::success(__('Накладная успешно создана!'));

        }


        return redirect()->route('platform.acceptances.index');
    }

}
