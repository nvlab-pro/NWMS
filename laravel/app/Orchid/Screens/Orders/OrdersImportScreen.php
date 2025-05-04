<?php

namespace App\Orchid\Screens\Orders;

use App\Imports\AcceptancesImport;
use App\Imports\OrdersImport;
use App\Models\rwLibAcceptType;
use App\Models\rwOrder;
use App\Models\rwOrderType;
use App\Models\rwShop;
use App\Models\rwWarehouse;
use App\Orchid\Services\DocumentService;
use App\Orchid\Services\OrderService;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class OrdersImportScreen extends Screen
{
    public $orderId;

    public function query($orderId = 0): iterable
    {

        $this->orderId = $orderId;

        return [
            'importDescriptions' => rwOrder::getImportDescriptions(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return CustomTranslator::get('Импорт заказов');
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

    public function layout(): array
    {
        $currentUser = Auth::user();
        $dbWhList = rwWarehouse::where('wh_domain_id', $currentUser->domain_id)->where('wh_type', 2);
        $dbShopList = rwShop::where('sh_domain_id', $currentUser->domain_id);

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {

        } else {

            $dbWhList = $dbWhList->where('wh_user_id', $currentUser->id);
            $dbShopList = $dbShopList->where('sh_user_id', $currentUser->id);

        }

        if ($this->orderId == 0) {

            $arFields = [

                Group::make([

                    Select::make('o_type_id')
                        ->title(CustomTranslator::get('Тип заказа'))
                        ->options(
                            rwOrderType::all()
                                ->pluck('ot_name', 'ot_id')
                                ->map(fn($name) => CustomTranslator::get($name)) // Переводим название типа заказа
                        )
                        ->required(),

                    Input::make('o_ext_id')
                        ->title(CustomTranslator::get('Внешний ID')),

                ]),

                Group::make([

                    Select::make('o_shop_id')
                        ->title(CustomTranslator::get('Магазин'))
                        ->fromModel(
                            $currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')
                                ? rwShop::where('sh_domain_id', $currentUser->domain_id)
                                : rwShop::where('sh_domain_id', $currentUser->domain_id)
                                ->whereIn('sh_user_id', [$currentUser->id, $currentUser->parent_id]),
                            'sh_name',
                            'sh_id'
                        )
                        ->required(),

                    Select::make('o_wh_id')
                        ->title(CustomTranslator::get('Склад'))
                        ->fromModel(
                            $currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')
                                ? rwWarehouse::where('wh_domain_id', $currentUser->domain_id)->where('wh_type', 2)
                                : rwWarehouse::where('wh_domain_id', $currentUser->domain_id)
                                ->where('wh_type', 2)
                                ->whereIn('wh_user_id', [$currentUser->id, $currentUser->parent_id]),
                            'wh_name',
                            'wh_id'
                        )
                        ->required(),

                ]),
                Group::make([

                    DateTimer::make('o_date')
                        ->title(CustomTranslator::get('Дата заказа'))
                        ->format('Y-m-d')
                        ->value(now()->format('Y-m-d'))
                        ->required(),

                    DateTimer::make('o_date_send')
                        ->title(CustomTranslator::get('Дата отправки'))
                        ->format('Y-m-d'),

                ]),

            ];
        } else {

            $arFields = [
                Input::make('o_id')
                    ->value($this->orderId)
                    ->type('hidden'),
            ];

        }

        return [
            Layout::rows(array_merge([
                Upload::make('offer_file')
                    ->title(CustomTranslator::get('Загрузите файл с товарами для заказа'))
                    ->acceptedFiles('.xlsx, .xls, .csv')
                    ->maxFiles(1),

            ],
                $arFields,
                [

                    Input::make('o_domain_id')
                        ->type('hidden')
                        ->value($currentUser->domain_id),

                    Input::make('o_user_id')
                        ->type('hidden')
                        ->value($currentUser->id),

                    Select::make('import_type')
                        ->options([
                            '0' => CustomTranslator::get('Немедленно'),
                            '1' => CustomTranslator::get('С задержкой'),
                        ])
                        ->title(CustomTranslator::get('Выберите способ загрузки'))
                        ->help('Небольшие файлы могут быть загружены немедленно. Для загрузки файлов с большим количеством записей используйте загрузку с задержкой.'),

                    Button::make(CustomTranslator::get('Начать импорт'))
                        ->method('importOrder')
                        ->class('btn btn-outline-primary')
                        ->icon('cloud-upload'),
                ])),

            Layout::view('Orders.OrderImportInstructions'),

        ];
    }

    /**
     * Обработчик импорта заказа.
     */

    public function importOrder(Request $request)
    {
        $currentUser = Auth::user();
        $id = $request->get('offer_file')[0] ?? null;

        $orderId = $request->get('o_id') ?? 0;

        if (!$id || $id == null) {
            Alert::error(CustomTranslator::get('Пожалуйста, загрузите файл перед импортом.'));
            if ($orderId == 0)
                return redirect()->route('platform.orders.import');
            else
                return redirect()->route('platform.orders.import', $orderId);
        }

        $attachment = Attachment::find($id);

        $attachment->domain_id = $currentUser->domain_id;
        $attachment->status = 0; // 0 - загружено, 1 - обрабатывается, 2 - импорт окончен, 3 - ошибка
        $attachment->type = 'импорт';
        $attachment->group = 'заказ';
        $attachment->import_type = $request->get('import_type'); // 0 - немедленно, 1 - отложено
        $attachment->save();

        if (!$attachment || !$attachment->path) {
            Alert::error(CustomTranslator::get('Файл не найден.'));
        }

        if ($request->get('import_type') == 0) {

            $fullPath = base_path().'/storage/app/'.$attachment->disk.'/'.$attachment->physicalPath();

            if (!file_exists($fullPath)) {
                Alert::error(CustomTranslator::get('Физически файл не найден: ') . $fullPath);
            }

            try {

//                $orderId = Excel::import(new AcceptancesImport($request, $id, $orderId), $fullPath);

                $import = new OrdersImport($request, $id, $orderId);
                Excel::import($import, $fullPath);

                $orderId = $import->getOrderId();
                $whId = $import->getWhId();

                // Пересчитывем данные в накладной
                $currentDoc = new OrderService($orderId);
                $currentDoc->recalcOrderRest();

                Alert::success(CustomTranslator::get('Товары успешно загружены!'));
                $attachment->status = 2;
                $attachment->save();

                return redirect()->route('platform.orders.edit', $orderId);

            } catch (\Exception $e) {
                Alert::error(CustomTranslator::get('Ошибка при импорте: ') . $e->getMessage());

            }

        } else {

            Alert::success(CustomTranslator::get('Файл успешно загружен и будет обработан в ближайшее время!'));

            return redirect()->route('platform.orders.index');
        }
    }
}
