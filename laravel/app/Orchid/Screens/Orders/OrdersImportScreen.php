<?php

namespace App\Orchid\Screens\Orders;

use App\Imports\OrdersImport;
use App\Models\{ rwOrder, rwShop, rwWarehouse };
use App\Orchid\Services\OrderService;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\{ Group, Input, Select, Upload };
use Orchid\Screen\Screen;
use Orchid\Support\Facades\{ Alert, Layout };

class OrdersImportScreen extends Screen
{
    public $orderId = 0;

    /* ------------------ QUERY ------------------ */
    public function query($orderId = 0): iterable
    {
        $this->orderId = $orderId;

        return [
            'importDescriptions' => rwOrder::getImportDescriptions(),
        ];
    }

    /* ------------------ UI ------------------ */
    public function name(): ?string
    {
        return CustomTranslator::get('Импорт заказов');
    }

    public function commandBar(): iterable { return []; }

    public function layout(): array
    {
        $u = Auth::user();

        $shopOptions = rwShop::where('sh_domain_id', $u->domain_id)
            ->when(!$u->hasAnyRole(['admin', 'warehouse_manager']),
                fn($q) => $q->whereIn('sh_user_id', [$u->id, $u->parent_id]))
            ->pluck('sh_name', 'sh_id');

        $warehouseOptions = rwWarehouse::where('wh_domain_id', $u->domain_id)
            ->where('wh_type', 2)
            ->when(!$u->hasAnyRole(['admin', 'warehouse_manager']),
                fn($q) => $q->whereIn('wh_user_id', [$u->id, $u->parent_id]))
            ->pluck('wh_name', 'wh_id');

        return [
            Layout::rows([
                Upload::make('offer_file')
                    ->title(CustomTranslator::get('Загрузите Excel файл'))
                    ->acceptedFiles('.xlsx,.xls,.csv')
                    ->maxFiles(1),

                Group::make([
                    Select::make('o_shop_id')
                        ->title(CustomTranslator::get('Магазин'))
                        ->options($shopOptions)
                        ->required(),

                    Select::make('o_wh_id')
                        ->title(CustomTranslator::get('Склад'))
                        ->options($warehouseOptions)
                        ->required(),
                ]),

                Select::make('import_type')
                    ->title(CustomTranslator::get('Способ загрузки'))
                    ->options([
                        '0' => CustomTranslator::get('Немедленно'),
                        '1' => CustomTranslator::get('С задержкой'),
                    ])
                    ->help(CustomTranslator::get(
                        'Небольшие файлы можно загружать немедленно; для крупных используйте отложенную загрузку.'
                    )),

                Input::make('o_domain_id')->type('hidden')->value($u->domain_id),
                Input::make('o_user_id')->type('hidden')->value($u->id),

                Button::make(CustomTranslator::get('Начать импорт'))
                    ->method('importOrder')
                    ->icon('cloud-upload')
                    ->class('btn btn-outline-primary'),
            ]),

            Layout::view('Orders.OrderImportInstructions'),
        ];
    }

    /* ------------------ ACTION ------------------ */
    public function importOrder(Request $request)
    {
        $u = Auth::user();
        $attId = $request->get('offer_file')[0] ?? null;

        if (!$attId) {
            Alert::error(CustomTranslator::get('Пожалуйста, выберите файл для импорта.'));
            return redirect()->back();
        }

        /** @var Attachment $attachment */
        $attachment = Attachment::find($attId);
        $attachment->fill([
            'domain_id'   => $u->domain_id,
            'status'      => 0,          // 0 – загружен, ещё не обрабатывался
            'type'        => 'импорт',
            'group'       => 'заказ',
            'import_type' => $request->import_type,
        ])->save();

        $fullPath = storage_path('app/' . $attachment->disk . '/' . $attachment->physicalPath());

        /* ---- Немедленный импорт ---- */
        if ($request->import_type == 0) {
            try {
                $import = new OrdersImport($request, $attId);
                Excel::import($import, $fullPath);

                $firstOrderId = $import->getFirstOrderId();

                // Пересчёт первого заказа (остальные пересчитаются в очереди/по крону)
                if ($firstOrderId) {
                    (new OrderService($firstOrderId))->recalcOrderRest();
                }

                $attachment->status = 2; // завершён
                $attachment->save();

                Alert::success(CustomTranslator::get('Импорт завершён успешно!'));

                return $firstOrderId
                    ? redirect()->route('platform.orders.edit', $firstOrderId)
                    : redirect()->route('platform.orders.index');

            } catch (\Throwable $e) {
                $attachment->status = 3; // ошибка
                $attachment->save();

                Alert::error(CustomTranslator::get('Ошибка импорта: ') . $e->getMessage());
                return back();
            }
        }

        /* ---- Отложенный импорт (в очередь) ---- */
        //  тут можно запланировать Job, если нужно
        Alert::success(CustomTranslator::get('Файл принят и будет обработан позже.'));
        return redirect()->route('platform.orders.index');
    }
}
