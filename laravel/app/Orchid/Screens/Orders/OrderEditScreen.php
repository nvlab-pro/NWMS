<?php

namespace App\Orchid\Screens\Orders;

use App\Console\scheduleOrders;
use App\Models\rwOffer;
use App\Models\rwOrder;
use App\Models\rwOrderOffer;
use App\Models\rwOrderStatus;
use App\Orchid\Layouts\Orders\OrderOffersTable;
use App\Orchid\Services\OrderService;
use App\WhCore\WhCore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Alert;

class OrderEditScreen extends Screen
{
    public $order;

    public function query($orderId): array
    {
        $currentUser = Auth::user();
        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {

            $this->order = rwOrder::where('o_domain_id', $currentUser->domain_id)->where('o_id', $orderId)->firstOrFail();

        } else {

            $this->order = rwOrder::where('o_domain_id', $currentUser->domain_id)
                ->whereHas('getShop', function ($query) use ($currentUser) {
                    $query->whereIn('o_user_id', [$currentUser->id, $currentUser->parent_id]);
                })
                ->where('o_id', $orderId)
                ->firstOrFail();

        }

        $dbOrderOffersList = rwOrderOffer::where('oo_order_id', $orderId)->with('getOffer')->get();

        return [
            'order' => $this->order,
            'dbOrderOffersList' => $dbOrderOffersList,
        ];
    }

    public function name(): ?string
    {
        return __('Заказ') . ': ' . $this->order->o_id . ' (' . $this->order->o_ext_id . ')';
    }

    public function commandBar(): array
    {
        $arLinksList = [];
        $currentUser = Auth::user();

        $dbStatus = rwOrderStatus::where('os_id', $this->order->o_status_id)->first();

        return [
            Button::make(__(' Откатить'))
                ->icon('bs.arrow-return-left')
                ->style('border: 1px solid #D62222; color: #D62222; border-radius: 10px;')
                ->method('changeStatusToNew')
                ->canSee(in_array($this->order->o_status_id, [15, 20, 30, 40]))
                ->parameters([
                    '_token' => csrf_token(),
                    'docId' => $this->order->o_id,
                    'status' => $this->order->o_status_id,
                ]),

            Button::make(__(' В обработку'))
                ->icon('bs.check-circle')
                ->style('border: 1px solid #dd66ff; color: #dd66ff; border-radius: 10px;')
                ->method('changeStatusToProcessing')
                ->canSee(in_array($this->order->o_status_id, [10]))
                ->parameters([
                    '_token' => csrf_token(),
                    'docId' => $this->order->o_id,
                    'status' => $this->order->o_status_id,
                ]),

            Button::make(__(' В резерв'))
                ->icon('bs.piggy-bank')
                ->style('border: 1px solid #157347; color: #157347; border-radius: 10px;')
                ->method('changeStatus')
                ->canSee(in_array($this->order->o_status_id, [10, 15, 30]))
                ->parameters([
                    '_token' => csrf_token(),
                    'docId' => $this->order->o_id,
                    'status' => $this->order->o_status_id,
                ]),

            Button::make(__(' Зарезервировать вручную'))
                ->icon('bs.piggy-bank')
                ->style('border: 1px solid #9c4edb; color: #9c4edb; border-radius: 10px;')
                ->method('tryReservOrder')
                ->canSee(in_array($this->order->o_status_id, [20]))
                ->parameters([
                    '_token' => csrf_token(),
                    'docId' => $this->order->o_id,
                    'status' => $this->order->o_status_id,
                ]),

            ModalToggle::make(__(' Добавить товар'))
                ->icon('bs.plus-circle')
                ->modal('addOfferModal')
                ->method('addOffer')
                ->canSee($this->order->o_status_id == 10 ? true : false),

            Button::make(__($dbStatus->os_name))
                ->style('background-color: ' . $dbStatus->os_bgcolor . ';' . 'color: ' . $dbStatus->os_color . ';')
                ->disabled(true),
        ];
    }

    public function layout(): array
    {
        $currentUser = Auth::user();

        return [
            Layout::modal('addOfferModal', [
                Layout::rows([
                    Input::make('offer.oo_order_id')
                        ->value($this->order->o_id)
                        ->type('hidden'),

                    Select::make('offer.oo_offer_id')
                        ->title(__('Выберите добавляемый товар:'))
                        ->width('100px')
                        ->options(
                            rwOffer::where('of_shop_id', $this->order->o_shop_id)
                                ->whereNotIn('of_id', function ($query) {
                                    $query->select('oo_offer_id')
                                        ->from('rw_order_offers')
                                        ->where('oo_order_id', $this->order->o_id); // Условие для конкретной накладной
                                })
                                ->get()
                                ->mapWithKeys(function ($offer) {
                                    return [$offer->of_id => $offer->of_name . ' (' . $offer->of_article . ')'];
                                })->toArray()
                        )
                        ->empty('', 0),

                    Input::make('offer.o_wh_id')
                        ->value($this->order->o_wh_id)
                        ->type('hidden'),

                    Input::make('offer.docDate')
                        ->value($this->order->o_date)
                        ->type('hidden'),

                ]),
            ])
                ->size('xl')
                ->method('addOffer')
                ->title('Добавление нового товара')->applyButton('Добавить')->closeButton('Закрыть'),

            Layout::tabs([
                'Основная' => [
                    Layout::rows([
                        Input::make('order.o_status_id')
                            ->type('hidden'),

                        Input::make('order.o_domain_id')
                            ->type('hidden')
                            ->value($currentUser->domain_id),

                        Input::make('order.o_user_id')
                            ->type('hidden'),


                        Group::make([
                            // Модальное окно для редактирования Внешнего ID
                            ModalToggle::make($this->order->o_ext_id ?? __('Не указана'))
                                ->modal('editExtIdModal')
                                ->method('update')
                                ->title('Внешний ID')
                                ->asyncParameters([
                                    'order' => $this->order->o_id
                                ]),

                            Label::make('order.getType.ot_name')
                                ->title('Тип заказа: '),


                        ]),

                        Group::make([
                            // Модальное окно для редактирования Даты заказа
                            ModalToggle::make($this->order->o_date)
                                ->modal('editOrderDateModal')
                                ->method('update')
                                ->title('Дата заказа')
                                ->asyncParameters([
                                    'order' => $this->order->o_id
                                ]),

                            Label::make('order.getShop.sh_name')
                                ->title('Магазин: '),

                        ]),

                        Group::make([

                            // Модальное окно для редактирования Даты отправки
                            ModalToggle::make($this->order->o_date_send ?? __('Не указана'))
                                ->modal('editOrderDateSendModal')
                                ->method('update')
                                ->title('Дата отправки')
                                ->asyncParameters([
                                    'order' => $this->order->o_id
                                ]),

                            Label::make('order.getWarehouse.wh_name')
                                ->title('Склад: '),

                        ]),
                    ]),
                ],
                __('Товары') => [
                    OrderOffersTable::class,

                    Layout::rows([
                        Button::make('Сохранить изменения')
                            ->class('btn btn-primary d-block mx-auto')
                            ->method('saveChanges') // Указывает метод экрана для вызова
                            ->parameters([
                                '_token' => csrf_token(),
                                'docId' => $this->order->o_id,
                                'shopId' => $this->order->o_shop_id,
                                'whId' => $this->order->o_wh_id,
                                'docDate' => $this->order->o_date,
                            ]),
                    ])
                        ->canSee($this->order->o_status_id <= 10),
                ],
            ]),

            // Определение модальных окон
            Layout::modal('editExtIdModal', [
                Layout::rows([
                    Input::make('order.o_id')
                        ->type('hidden'),
                    Input::make('order.o_ext_id')
                        ->title('Внешний ID')
                        ->required(),
                ])
            ])->title('Редактировать Внешний ID')
                ->applyButton('Сохранить')
                ->async('asyncGetOrder'),

            Layout::modal('editOrderDateModal', [
                Layout::rows([
                    Input::make('order.o_id')
                        ->type('hidden'),
                    DateTimer::make('order.o_date')
                        ->title('Дата заказа')
                        ->enableTime()
                        ->format('Y-m-d H:i:s')
                        ->required(),
                ])
            ])->title('Редактировать Дату заказа')
                ->applyButton('Сохранить')
                ->async('asyncGetOrder'),

            Layout::modal('editOrderDateSendModal', [
                Layout::rows([
                    Input::make('order.o_id')
                        ->type('hidden'),
                    DateTimer::make('order.o_date_send')
                        ->title('Дата отправки')
                        ->format('Y-m-d'),
                ])
            ])->title('Редактировать Дату отправки')
                ->applyButton('Сохранить')
                ->async('asyncGetOrder'),
        ];
    }

    public function tryReservOrder(Request $request)
    {
        $validatedData = $request->validate([
            'docId' => 'nullable|numeric|min:0', // Цена должна быть числом >= 0
            'status' => 'nullable|numeric|min:0', // Цена должна быть числом >= 0
        ]);

        if ($validatedData['status'] == 20) {

            $order = rwOrder::where('o_id', $validatedData['docId'])
                ->first();

            if ($order) {

                scheduleOrders::reserveOrders($validatedData['docId']);

            }

            Alert::success(__('Заказ ' . $validatedData['docId'] . ' перерезервирован!'));

        } else {

            Alert::error(__('Заказ ' . $validatedData['docId'] . ' не может быть зарезервирован!'));

        }
    }
    public function changeStatusToNew(Request $request)
    {
        $validatedData = $request->validate([
            'docId' => 'nullable|numeric|min:0', // Цена должна быть числом >= 0
            'status' => 'nullable|numeric|min:0', // Цена должна быть числом >= 0
        ]);

        if ($validatedData['status'] == 10 || $validatedData['status'] == 15 || $validatedData['status'] == 20 || $validatedData['status'] == 30 || $validatedData['status'] == 40) {

            $order = rwOrder::where('o_id', $validatedData['docId'])
                ->first();

            if ($order) {

                $order->o_status_id = 10;
                $order->save(); // Автоматически вызовет аудит, если модель использует AuditableContract

                $serviceOrder = new OrderService($validatedData['docId']);
                $serviceOrder->resaveOrderList();

            }

            Alert::success(__('Заказ ' . $validatedData['docId'] . ' переведен в статус "Новый"!'));

        } else {

            Alert::error(__('Заказ ' . $validatedData['docId'] . ' не может быть переведен в статус "Новый"!'));

        }
    }

    public function changeStatusToProcessing(Request $request)
    {
        $validatedData = $request->validate([
            'docId' => 'nullable|numeric|min:0', // Цена должна быть числом >= 0
            'status' => 'nullable|numeric|min:0', // Цена должна быть числом >= 0
        ]);

        if ($validatedData['status'] == 10) {

            $order = rwOrder::where('o_id', $validatedData['docId'])
                ->first();

            if ($order) {

                $order->o_status_id = 15;
                $order->save(); // Автоматически вызовет аудит, если модель использует AuditableContract

                $serviceOrder = new OrderService($validatedData['docId']);
                $serviceOrder->resaveOrderList();

            }

            Alert::success(__('Заказ ' . $validatedData['docId'] . ' переведен в статус "В обработке"!'));

        } else {

            Alert::error(__('Заказ ' . $validatedData['docId'] . ' не может быть переведен в статус "В обработке"!'));

        }
    }

    public function changeStatus(Request $request)
    {
        $validatedData = $request->validate([
            'docId' => 'nullable|numeric|min:0', // Цена должна быть числом >= 0
            'status' => 'nullable|numeric|min:0', // Цена должна быть числом >= 0
        ]);

        if ($validatedData['status'] == 10 || $validatedData['status'] == 15 || $validatedData['status'] == 30) {

            $order = rwOrder::where('o_id', $validatedData['docId'])
                ->first();

            if ($order) {

                $order->o_status_id = 20;
                $order->save(); // Автоматически вызовет аудит, если модель использует AuditableContract

                $serviceOrder = new OrderService($validatedData['docId']);
                $serviceOrder->resaveOrderList();

            }

            Alert::success(__('Заказ ' . $validatedData['docId'] . ' переведен в резерв!'));

        } else {

            Alert::error(__('Заказ ' . $validatedData['docId'] . ' не может быть переведен в резерв!'));

        }
    }

    public function saveChanges(Request $request)
    {
        $validatedData = $request->validate([
            'orderOfferDocId.*' => 'nullable|numeric|min:0',
            'orderOfferId.*' => 'nullable|numeric|min:0',
            'orderOfferQty.*' => 'nullable|numeric|min:0',
            'orderOfferOcPrice.*' => 'nullable|numeric|min:0',
            'orderOfferPrice.*' => 'nullable|numeric|min:0',
            'docId' => 'nullable|numeric|min:0', // Цена должна быть числом >= 0
            'shopId' => 'nullable|numeric|min:0', // Цена должна быть числом >= 0
            'whId' => 'nullable|numeric|min:0', // Цена должна быть числом >= 0
            'docDate' => 'nullable|date', // Каждая дата должна быть обязательной и формата даты
        ]);

        $currentWarehouse = new WhCore($validatedData['whId']);
        $count = $sum = 0;
        $dbOrder = rwOrder::where('o_id', $validatedData['docId'])->first();

        foreach ($validatedData['orderOfferId'] as $id => $offerId) {

            $offer = rwOrderOffer::find($id);

            if ($offer) {

                $offer->oo_order_id = $validatedData['docId'] ?? 0;
                $offer->oo_offer_id = $validatedData['orderOfferId'][$id] ?? 0;
                $offer->oo_qty = $validatedData['orderOfferQty'][$id] ?? 0;
                $offer->oo_oc_price = $validatedData['orderOfferOcPrice'][$id] ?? 0;
                $offer->oo_price = $validatedData['orderOfferPrice'][$id] ?? 0;
                $offer->save();

                $status = 0;
                $count = $validatedData['orderOfferQty'][$id];
                if ($dbOrder->o_status_id <= 10) $count = 0; // Если статус документа "новый", товар не резервируем

                if ($count > 0) {
                    // Сохраняем товар
                    $currentWarehouse->saveOffers(
                        $validatedData['docId'],
                        $validatedData['docDate'],
                        2,                                  // Приемка (таблица rw_lib_type_doc)
                        $id,                                        // ID офера в документе
                        $validatedData['orderOfferId'][$id],          // оригинальный ID товара
                        $status,
                        $count,
                        NULL,
                        $validatedData['orderOfferOcPrice'][$id],
                        NULL,
                        NULL,
                        time()
                    );
                } else {
                    // Удаляем товар
                    $currentWarehouse->deleteItemFromDocument($id, $validatedData['docId'], 2);
                }

                $count += $offer->oo_qty;
                $sum += $offer->oo_oc_price * $offer->oo_qty;

                // Резервируем товары в заказе
                $currentWarehouse->calcRestOffer($validatedData['orderOfferId'][$id]);

            }


        }

        // Перерезервируем товары в заказе
        $currentWarehouse->reservOffers($validatedData['orderOfferId'][$id]);

        $dbOrder = rwOrder::find($validatedData['docId']);
        $dbOrder->o_count = $count;
        $dbOrder->o_sum = $sum;
        $dbOrder->save();

        Alert::success(__('Данные о товаре сохранены!'));

    }

    public function addOffer(Request $request)
    {
        $validated = $request->validate([
            'offer.oo_order_id' => 'required|integer|min:1',
            'offer.o_wh_id' => 'required|integer|min:1',
            'offer.oo_offer_id' => 'required|integer|min:1',
            'offer.docDate' => 'nullable|date', // Каждая дата должна быть обязательной и формата даты
        ]);

        $dbOffer = rwOrderOffer::where('oo_order_id', $validated['offer']['oo_order_id'])
            ->where('oo_offer_id', $validated['offer']['oo_offer_id'])
            ->first();

        if (!isset($dbOffer->oo_id)) {

            $dbOffer = rwOrderOffer::create([
                'oo_order_id' => $validated['offer']['oo_order_id'],
                'oo_offer_id' => $validated['offer']['oo_offer_id'],
            ]);

            Alert::success(__('Товар добавлен в заказ!'));

            $currentWarehouse = new WhCore($validated['offer']['o_wh_id']);

            $barcode = '';

            $currentWarehouse->saveOffers(
                $validated['offer']['oo_order_id'],
                $validated['offer']['docDate'],
                2,                       // Приемка (таблица rw_lib_type_doc)
                $dbOffer->oo_id,                                // ID офера в документе
                $validated['offer']['oo_offer_id'],                                // оригинальный ID товара
                0,
                0,
                $barcode,
                0,
                NULL,
                NULL,
            );


        } else {

            Alert::error(__('Данный товар уже есть в заказе!'));

        }

    }

    public function deleteOrderOffer(Request $request)
    {
        $validated = $request->validate([
            'offerDocId' => 'required|integer|min:1',
        ]);

        $dbOrderOffer = rwOrderOffer::where('oo_id', $validated['offerDocId'])->first();

        if (isset($dbOrderOffer->oo_id)) {

            $dbOrder = rwOrder::where('o_id', $dbOrderOffer->oo_order_id)->first();

            $offerId = $dbOrderOffer->oo_offer_id;

            if (isset($dbOrder->o_id)) {

                // Удаляем товар со склада
                $currentWh = new WhCore($dbOrder->o_wh_id);
                $currentWh->deleteItemFromDocument($dbOrderOffer->oo_id, $dbOrder->o_id, 2);

                // Удаляем товар из накладной
                $dbOrderOffer->delete();

                // Пересчитывем остатки
                $currentWh->getRestOfOfferId($offerId);

                // Пересчитывем данные в накладной
                $currentDoc = new OrderService($dbOrder->o_id);
                $currentDoc->recalcOrderRest();
            }

            Alert::error(__('Товар удален из заказа!'));
        }

    }

    public function asyncGetOrder(Request $request): array
    {
        // Получаем orderId из параметров запроса
        $orderId = $request->input('order');

        // Загружаем заказ по orderId
        $order = rwOrder::findOrFail($orderId);

        return [
            'order' => $order,
        ];
    }

    public function update(Request $request)
    {
        // Получаем данные из запроса
        $orderData = $request->input('order');

        // Находим заказ по ID
        $order = rwOrder::findOrFail($orderData['o_id']);

        $request->validate([
            'order.o_ext_id' => 'nullable',
            'order.o_date' => 'nullable|date',
            'order.o_date_send' => 'nullable|date',
        ]);

        // Обновляем данные заказа
        $order->fill($orderData);
        $order->save();

        Alert::info(__('Заказ успешно обновлен'));

        return redirect()->route('platform.orders.edit', $order);
    }

}