<?php

namespace App\Orchid\Screens\Orders;

use App\Console\scheduleOrders;
use App\Http\Middleware\RoleMiddleware;
use App\Models\rwCompany;
use App\Models\rwDeliveryService;
use App\Models\rwOffer;
use App\Models\rwOrder;
use App\Models\rwOrderAssembly;
use App\Models\rwOrderContact;
use App\Models\rwOrderDs;
use App\Models\rwOrderDsStatus;
use App\Models\rwOrderOffer;
use App\Models\rwOrderPacking;
use App\Models\rwOrderStatus;
use App\Models\rwPrintTemplate;
use App\Models\WhcRest;
use App\Orchid\Layouts\Orders\OrderOffersTable;
use App\Orchid\Layouts\Orders\OrderPrint;
use App\Orchid\Services\OrderService;
use App\Services\CustomTranslator;
use App\WhCore\WhCore;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Link;
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
    public $order, $currentUser;

    public function query($orderId, Request $request): array
    {
        $currentUser = Auth::user();
        $this->currentUser = $currentUser;
        $arPickingOffersList = [];
        $arPackedOffersList = [];

        if ($currentUser->hasRole('admin')) {

            $this->order = rwOrder::where('o_id', $orderId)
                ->with('getPlace')
                ->with('getCompany')
                ->with('getContact')
                ->firstOrFail();

        } else {

            if ($currentUser->hasRole('warehouse_manager')) {

                $this->order = rwOrder::where('o_domain_id', $currentUser->domain_id)
                    ->where('o_id', $orderId)
                    ->with('getPlace')
                    ->with('getCompany')
                    ->with('getOperationUser')
                    ->with('getContact')
                    ->firstOrFail();

            } else {

                $this->order = rwOrder::where('o_domain_id', $currentUser->domain_id)
                    ->whereHas('getShop', function ($query) use ($currentUser) {
                        $query->whereIn('sh_user_id', [$currentUser->id, $currentUser->parent_id]);
                    })
                    ->where('o_id', $orderId)
                    ->with('getPlace')
                    ->with('getCompany')
                    ->with('getOperationUser')
                    ->with('getContact')
                    ->firstOrFail();
            }
        }

        $dbOrderOffersList = rwOrderOffer::where('oo_order_id', $orderId)
            ->with('getOffer')
            ->orderBy('oo_id', 'DESC')
            ->get();

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {

            // *************************************************************
            // *** Скидываем упаковку если ни один товар еще не упакован
            if (isset($request->action) && $request->action == 'cancel_packing' && $this->order->o_status_id == 90) {

                $packSum = rwOrderPacking::where('op_order_id', $orderId)
                    ->sum('op_qty');

                if ($packSum == 0) {

                    $this->order->o_status_id = 80;
                    $this->order->save();

                    Alert::success(CustomTranslator::get('Заказ вернули обратно на место размещения после сборки!'));

                }
            }

            if ($this->order->o_status_id >= 50) {
                // ****************************************
                // ** Собираем список собранных товаров
                $arPickingOffersList = [];
                foreach ($dbOrderOffersList as $currentOffer) {

                    $dbOfferPicking = rwOrderAssembly::where('oa_order_id', $orderId)
                        ->where('oa_offer_id', $currentOffer->oo_offer_id)
                        ->with('getPlace')
                        ->with('getUser')
                        ->orderBy('oa_data', 'ASC')
                        ->get();

                    foreach ($dbOfferPicking as $dbOffer) {

                        isset($dbOffer->getPlace->pl_id) ? $currentPlace = $this->getPageStr($dbOffer->getPlace) : $currentPlace = '-';
                        isset($this->order->getPlace->pl_id) ? $endPlace = $this->getPageStr($this->order->getPlace) : $endPlace = '-';

                        $arPickingOffersList[] = [
                            'of_name' => $currentOffer->getOffer->of_name,
                            'of_article' => $currentOffer->getOffer->of_article,
                            'oa_data' => Carbon::parse($dbOffer->oa_data)->format('d.m.Y H:i:s'),
                            'oa_timestamp' => Carbon::parse($dbOffer->oa_data)->timestamp,
                            'oa_user_name' => $dbOffer->getUser->name,
                            'oo_qty' => $currentOffer->oo_qty,
                            'oa_qty' => $dbOffer->oa_qty,
                            'picking_place' => $currentPlace,
                            'end_place' => $endPlace,
                        ];
                    }
                }

                // Сортируем $arPickingOffersList по oa_timestamp по возрастанию
                usort($arPickingOffersList, function ($a, $b) {
                    return $a['oa_timestamp'] <=> $b['oa_timestamp'];
                });
            }

            // ****************************************
            // ** Собираем список упакованных товаров
            if ($this->order->o_status_id >= 90) {

                foreach ($dbOrderOffersList as $currentOffer) {

                    $dbOfferPacking = rwOrderPacking::where('op_order_id', $orderId)
                        ->where('op_offer_id', $currentOffer->oo_offer_id)
                        ->with('getUser')
                        ->first();

                    $dbOffer = [];

                    $packDate = '-';
                    $packTime = '0';
                    $packQty = 0;
                    $userName = '-';

                    if (isset($dbOfferPacking->op_id) && $dbOfferPacking->op_qty > 0) {

                        $dbOffer = $dbOfferPacking;
                        $packQty = $dbOfferPacking->op_qty;
                        $userName = $dbOfferPacking->getUser->name;

                        $packDate = Carbon::parse($dbOfferPacking->op_data)->format('d.m.Y H:i:s');
                        $packTime = Carbon::parse($dbOfferPacking->op_data)->timestamp;
                    }

                    isset($currentOffer->getPlace->pl_id) ? $currentPlace = $this->getPageStr($currentOffer->getPlace) : $currentPlace = '-';
                    isset($this->order->getPlace->pl_id) ? $endPlace = $this->getPageStr($this->order->getPlace) : $endPlace = '-';

                    $arPackedOffersList[] = [
                        'of_name' => $currentOffer->getOffer->of_name,
                        'of_article' => $currentOffer->getOffer->of_article,
                        'op_data' => $packDate,
                        'op_timestamp' => $packTime,
                        'op_user_name' => $userName,
                        'oo_qty' => $currentOffer->oo_qty,
                        'op_qty' => $packQty,
                        'picking_place' => $currentPlace,
                        'end_place' => $endPlace,
                    ];

                }
            }
        }


        // ****************************************
        // ** Получаем список шаблонов для печати
        $dbTemplates = rwPrintTemplate::where('pt_domain_id', $currentUser->domain_id)
            ->where(function ($query) use ($currentUser) {
                $query->whereNull('pt_user_id')
                    ->orWhere('pt_user_id', $currentUser->id);
            })
            ->filters()
            ->get();

        // Контакты
        $contact = rwOrderContact::where('oc_order_id', $orderId)->first();

        // Службы доставки
        $dbOrderDs = rwOrderDs::where('ods_id', $orderId)->first();

        return [
            'order' => $this->order,
            'currentUser' => $currentUser,
            'dbOrderOffersList' => $dbOrderOffersList,
            'arPickingOffersList' => $arPickingOffersList,
            'arPackedOffersList' => $arPackedOffersList,
            'printTemplates' => $dbTemplates,
            'contact' => $contact ? $contact->toArray() : [],
            'ds' => $dbOrderDs ? $dbOrderDs->toArray() : [],
        ];
    }

    protected function getPageStr($dbPlace)
    {

        $currentPlace = '';

        $currentPlace .= $dbPlace->pl_room;
        if (strlen($dbPlace->pl_floor) > 0) $currentPlace .= ' | ' . $dbPlace->pl_floor;
        if (strlen($dbPlace->pl_section) > 0) $currentPlace .= ' | ' . $dbPlace->pl_section;
        if ($dbPlace->pl_row > 0) $currentPlace .= ' | ' . $dbPlace->pl_row;
        if ($dbPlace->pl_rack > 0) $currentPlace .= ' | ' . $dbPlace->pl_rack;
        if ($dbPlace->pl_shelf > 0) $currentPlace .= ' | ' . $dbPlace->pl_shelf;

        if (substr($currentPlace, 0, 3) == ' | ') $currentPlace = substr($currentPlace, 3);

        return $currentPlace;

    }

    public function name(): ?string
    {
        return CustomTranslator::get('Заказ') . ': ' . $this->order->o_id . ' (' . $this->order->o_ext_id . ')';
    }

    public function commandBar(): array
    {
        $arLinksList = [];
        $currentUser = Auth::user();

        $dbStatus = rwOrderStatus::where('os_id', $this->order->o_status_id)->first();

        return [
            Button::make(' ' . CustomTranslator::get('Отменить'))
                ->icon('bs.trash3')
                ->style('border: 1px solid #D62222; background-color: #D62222; color: #FFFFFF; border-radius: 10px;')
                ->method('changeStatusToCancel')
                ->canSee(in_array($this->order->o_status_id, [10, 15, 20, 30, 40]))
                ->confirm(CustomTranslator::get('Вы уверены, что хотите отменить этот заказ?'))
                ->parameters([
                    '_token' => csrf_token(),
                    'docId' => $this->order->o_id,
                    'status' => $this->order->o_status_id,
                ]),

            Button::make(' ' . CustomTranslator::get('Откатить'))
                ->icon('bs.arrow-return-left')
                ->style('border: 1px solid #D62222; color: #D62222; border-radius: 10px;')
                ->method('changeStatusToNew')
                ->canSee(in_array($this->order->o_status_id, [5, 15, 20, 30, 40]))
                ->parameters([
                    '_token' => csrf_token(),
                    'docId' => $this->order->o_id,
                    'status' => $this->order->o_status_id,
                ]),

            Button::make(' ' . CustomTranslator::get('В обработку'))
                ->icon('bs.check-circle')
                ->style('border: 1px solid #dd66ff; color: #dd66ff; border-radius: 10px;')
                ->method('changeStatusToProcessing')
                ->canSee(in_array($this->order->o_status_id, [10]))
                ->parameters([
                    '_token' => csrf_token(),
                    'docId' => $this->order->o_id,
                    'status' => $this->order->o_status_id,
                ]),

            Button::make(' ' . CustomTranslator::get('В резерв'))
                ->icon('bs.piggy-bank')
                ->style('border: 1px solid #157347; color: #157347; border-radius: 10px;')
                ->method('changeStatus')
                ->canSee(in_array($this->order->o_status_id, [10, 15, 30]))
                ->parameters([
                    '_token' => csrf_token(),
                    'docId' => $this->order->o_id,
                    'status' => $this->order->o_status_id,
                ]),

            Button::make(' ' . CustomTranslator::get('Зарезервировать вручную'))
                ->icon('bs.piggy-bank')
                ->style('border: 1px solid #9c4edb; color: #9c4edb; border-radius: 10px;')
                ->method('tryReservOrder')
                ->canSee(in_array($this->order->o_status_id, [20]))
                ->parameters([
                    '_token' => csrf_token(),
                    'docId' => $this->order->o_id,
                    'status' => $this->order->o_status_id,
                ]),

            Link::make(CustomTranslator::get('Импорт заказа'))
                ->icon('bs.cloud-upload')
                ->canSee(in_array($this->order->o_status_id, [10]))
                ->route('platform.orders.import', $this->order->o_id),

            Link::make(CustomTranslator::get('Настройка печати'))
                ->icon('bs.gear')
                ->canSee(RoleMiddleware::checkUserPermission('admin,warehouse_manager'))
                ->route('platform.orders.print.settings'),

            Button::make(CustomTranslator::get($dbStatus->os_name))
                ->style('background-color: ' . $dbStatus->os_bgcolor . ';' . 'color: ' . $dbStatus->os_color . ';')
                ->disabled(true),
        ];
    }

    public function layout(): array
    {
        $currentUser = Auth::user();

        $warehouseId = $this->order->o_wh_id;

        // Выгружаем все остатки по складу, сгруппированные по товару
        $stockMap = whcRest::where('whcr_wh_id', $warehouseId)
            ->selectRaw('whcr_offer_id, SUM(whcr_count) as total')
            ->groupBy('whcr_offer_id')
            ->pluck('total', 'whcr_offer_id'); // [offer_id => total]

        $tabs = [
            CustomTranslator::get('Основная') => [
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
                        ModalToggle::make($this->order->o_ext_id ?? CustomTranslator::get('Не указана'))
                            ->modal('editExtIdModal')
                            ->method('update')
                            ->title(CustomTranslator::get('Внешний ID'))
                            ->asyncParameters([
                                'order' => $this->order->o_id
                            ]),

                        Label::make('order.getType.ot_name')
                            ->title(CustomTranslator::get('Тип заказа'))
                            ->value(function ($model) {
                                return CustomTranslator::get($model);
                            })


                    ]),

                    Group::make([
                        // Модальное окно для редактирования Даты заказа
                        ModalToggle::make($this->order->o_date)
                            ->modal('editOrderDateModal')
                            ->method('update')
                            ->title(CustomTranslator::get('Дата заказа'))
                            ->asyncParameters([
                                'order' => $this->order->o_id
                            ]),

                        Label::make('order.getShop.sh_name')
                            ->title(CustomTranslator::get('Магазин')),

                    ]),

                    Group::make([

                        // Модальное окно для редактирования Даты отправки
                        ModalToggle::make($this->order->o_date_send ?? CustomTranslator::get('Не указана'))
                            ->modal('editOrderDateSendModal')
                            ->method('update')
                            ->title(CustomTranslator::get('Дата отправки'))
                            ->asyncParameters([
                                'order' => $this->order->o_id
                            ]),

                        Label::make('order.getWarehouse.wh_name')
                            ->title(CustomTranslator::get('Склад')),

                    ]),
                ]),
            ],
            CustomTranslator::get('Товары') => [

                Layout::rows([
                    Group::make([

                        Select::make('oo_offer_id')
                            ->title(CustomTranslator::get('Выберите добавляемый товар:'))
                            ->width('100px')
                            ->options(
                                rwOffer::where('of_shop_id', $this->order->o_shop_id)
                                    ->whereNotIn('of_id', function ($query) {
                                        $query->select('oo_offer_id')
                                            ->from('rw_order_offers')
                                            ->where('oo_order_id', $this->order->o_id); // Условие для конкретной накладной
                                    })
                                    ->get()
                                    ->mapWithKeys(function ($offer) use ($stockMap) {
                                        $stock = $stockMap[$offer->of_id] ?? 0;

                                        return [
                                            $offer->of_id => $offer->of_name . ' (' . $offer->of_article . ') — остаток: ' . $stock,
                                        ];
                                    })
                            )
                            ->horizontal()
                            ->empty('', 0),

                        Button::make(CustomTranslator::get('Добавить товар'))
                            ->method('addOffer')
                            ->class('btn btn-outline-success btn-sm')
                            ->parameters([
                                'oo_order_id' => $this->order->o_id,
                                'o_wh_id' => $this->order->o_wh_id,
                                'docDate' => $this->order->o_date,
                            ]),

                    ])->fullWidth(),
                ])->canSee($this->order->o_status_id == 10),

                OrderOffersTable::class,

                Layout::rows([
                    Button::make(CustomTranslator::get('Сохранить изменения'))
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
        ];

        if ($this->order->o_customer_type == 0) {
            $tabs[CustomTranslator::get('Физическое лицо')] = [
                Layout::rows([
                    Input::make('contact.oc_first_name')
                        ->title(CustomTranslator::get('Имя'))
                        ->readonly($this->order->o_status_id > 10),

                    Input::make('contact.oc_middle_name')
                        ->title(CustomTranslator::get('Отчество'))
                        ->readonly($this->order->o_status_id > 10),

                    Input::make('contact.oc_last_name')
                        ->title(CustomTranslator::get('Фамилия'))
                        ->readonly($this->order->o_status_id > 10),

                    Input::make('contact.oc_phone')
                        ->title(CustomTranslator::get('Телефон'))
                        ->readonly($this->order->o_status_id > 10),

                    Input::make('contact.oc_email')
                        ->title(CustomTranslator::get('Email'))
                        ->readonly($this->order->o_status_id > 10),

                    Input::make('contact.oc_postcode')
                        ->title(CustomTranslator::get('Почтовый индекс'))
                        ->readonly($this->order->o_status_id > 10),

                    Input::make('contact.oc_full_address')
                        ->title(CustomTranslator::get('Полный адрес'))
                        ->readonly($this->order->o_status_id > 10),

                    Button::make(CustomTranslator::get('Сохранить изменения'))
                        ->method('saveIndividualEntityChanges')
                        ->parameters([
                            '_token' => csrf_token(),
                            'docId' => $this->order->o_id,
                            'status' => $this->order->o_status_id,
                        ])
                        ->canSee($this->order->o_status_id <= 10)
                        ->class('btn btn-primary'),
                ]),
            ];
        } else {
            $tabs[CustomTranslator::get('Юридическое лицо')] = [
                Layout::rows([

                    Select::make('order.o_company_id')
                        ->title(CustomTranslator::get('Выберите юридическое лицо:'))
                        ->width('100px')
                        ->fromModel(rwCompany::where('co_domain_id', $currentUser->domain_id), 'co_name', 'co_id')
                        ->empty(CustomTranslator::get('Не выбрано'), 0),

                    Button::make(CustomTranslator::get('Сохранить изменения'))
                        ->method('saveLegalEntityChanges')
                        ->parameters([
                            '_token' => csrf_token(),
                            'docId' => $this->order->o_id,
                            'status' => $this->order->o_status_id,
                        ])
                        ->canSee($this->order->o_status_id <= 10)
                        ->class('btn btn-primary'),

                ]),
                Layout::view('Orders/OrderLegalEntityShow'),
            ];
        }

        $tabs[CustomTranslator::get('Доставка')] = [
            Layout::rows([
                Select::make('ds.ods_ds_id')
                    ->title(CustomTranslator::get('Служба доставки'))
                    ->options(
                        rwDeliveryService::all()->pluck('ds_name', 'ds_id')->toArray()
                    )
                    ->empty(CustomTranslator::get('Не выбрано'), 0)
                    ->readonly($this->order->o_status_id > 10),

                Select::make('ds.ods_status')
                    ->title(CustomTranslator::get('Статус'))
                    ->options(
                        rwOrderDsStatus::all()->pluck('odss_name', 'odss_id')->toArray()
                    )
                    ->empty(CustomTranslator::get('Не выбран'), 0)
                    ->readonly($this->order->o_status_id > 10),

                Input::make('ds.ods_track_number')
                    ->title(CustomTranslator::get('Трек-номер'))
                    ->readonly($this->order->o_status_id > 10),

                Button::make(CustomTranslator::get('Сохранить изменения'))
                    ->method('saveDSChanges')
                    ->parameters([
                        '_token' => csrf_token(),
                        'docId' => $this->order->o_id,
                        'status' => $this->order->o_status_id,
                    ])
                    ->canSee($this->order->o_status_id <= 10)
                    ->class('btn btn-primary'),
            ]),
        ];


        $tabs[CustomTranslator::get('Печать')] = OrderPrint::class;

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {

            if ($this->order->o_status_id >= 50) {
                $tabs[CustomTranslator::get('Сборка')] = Layout::view('Orders/OrderPickedOffersList');
            }
            if ($this->order->o_status_id >= 90) {
                $tabs[CustomTranslator::get('Упаковка')] = Layout::view('Orders/OrderPackedOffersList');
            }

        }

        return [
            Layout::tabs($tabs),

            //                  ->title(CustomTranslator::get('Добавление нового товара'))->applyButton(CustomTranslator::get('Добавить'))->closeButton(CustomTranslator::get('Закрыть')),


            // Определение модальных окон
            Layout::modal('editExtIdModal', [
                Layout::rows([
                    Input::make('order.o_id')
                        ->type('hidden'),
                    Input::make('order.o_ext_id')
                        ->title(CustomTranslator::get('Внешний ID'))
                        ->required(),
                ])
            ])->title(CustomTranslator::get('Редактировать Внешний ID'))
                ->applyButton(CustomTranslator::get('Сохранить'))
                ->async('asyncGetOrder'),

            Layout::modal('editOrderDateModal', [
                Layout::rows([
                    Input::make('order.o_id')
                        ->type('hidden'),
                    DateTimer::make('order.o_date')
                        ->title(CustomTranslator::get('Дата заказа'))
                        ->enableTime()
                        ->format('Y-m-d H:i:s')
                        ->required(),
                ])
            ])->title(CustomTranslator::get('Редактировать дату заказа'))
                ->applyButton(CustomTranslator::get('Сохранить'))
                ->async('asyncGetOrder'),

            Layout::modal('editOrderDateSendModal', [
                Layout::rows([
                    Input::make('order.o_id')
                        ->type('hidden'),
                    DateTimer::make('order.o_date_send')
                        ->title(CustomTranslator::get('Дата отправки'))
                        ->format('Y-m-d'),
                ])
            ])->title(CustomTranslator::get('Редактировать дату отправки'))
                ->applyButton(CustomTranslator::get('Сохранить'))
                ->async('asyncGetOrder'),
        ];
    }

    public function saveDSChanges(Request $request)
    {
        $validatedData = $request->validate([
            'ds.ods_ds_id' => 'nullable|numeric|min:0',
            'ds.ods_status' => 'nullable|numeric|min:0',
            'ds.ods_track_number' => 'nullable|string|max:50',
            'docId' => 'nullable|numeric|min:0',
            'status' => 'nullable|numeric|min:0',
        ]);

        if ($validatedData['status'] <= 10) {

            $orderDs = rwOrderDs::where('ods_id', $validatedData['docId'])
                ->first();

            if (!$orderDs) {
                // создаем новую запись, если нет
                $orderDs = new rwOrderDs();
                $orderDs->ods_id = $validatedData['docId'];
            }

            // сохраняем данные
            $orderDs->ods_ds_id = $validatedData['ds']['ods_ds_id'];
            $orderDs->ods_status = $validatedData['ds']['ods_status'];
            $orderDs->ods_track_number = $validatedData['ds']['ods_track_number'];
            $orderDs->save(); // Автоматически вызовет аудит, если модель использует AuditableContract

            Alert::success(CustomTranslator::get('Данные по доставке у заказа') . ' ' . $validatedData['docId'] . ' ' . CustomTranslator::get('были изменены!'));
        }
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

            Alert::success(CustomTranslator::get('Заказ') . ' ' . $validatedData['docId'] . ' ' . CustomTranslator::get('перерезервирован') . '!');

        } else {

            Alert::error(CustomTranslator::get('Заказ') . ' ' . $validatedData['docId'] . ' ' . CustomTranslator::get('не может быть зарезервирован') . '!');

        }
    }

    public function saveIndividualEntityChanges(Request $request)
    {
        $validatedData = $request->validate([
        'docId' => 'required|numeric|min:0',
        'status' => 'nullable|numeric|min:0',
        'contact.oc_first_name' => 'nullable|string|max:50',
        'contact.oc_middle_name' => 'nullable|string|max:50',
        'contact.oc_last_name' => 'nullable|string|max:50',
        'contact.oc_phone' => 'nullable|string|max:20',
        'contact.oc_email' => 'nullable|string|max:75',
        'contact.oc_postcode' => 'nullable|string|max:10',
        'contact.oc_full_address' => 'nullable|string|max:255',
    ]);

    $order = rwOrder::findOrFail($validatedData['docId']);

    $contact = $order->getContact;

    if (!$contact) {
        $contact = new rwOrderContact();
        $contact->oc_order_id = $order->o_id;
    }

    $contact->fill($validatedData['contact'] ?? []);
    $contact->save();

    Alert::success(CustomTranslator::get('Данные физического лица сохранены!'));
}

    public function changeStatusToNew(Request $request)
    {
        $validatedData = $request->validate([
            'docId' => 'nullable|numeric|min:0',
            'status' => 'nullable|numeric|min:0',
        ]);

        if ($validatedData['status'] == 5 || $validatedData['status'] == 10 || $validatedData['status'] == 15 || $validatedData['status'] == 20 || $validatedData['status'] == 30 || $validatedData['status'] == 40) {

            $order = rwOrder::where('o_id', $validatedData['docId'])
                ->first();

            if ($order) {

                $order->o_status_id = 10;
                $order->save(); // Автоматически вызовет аудит, если модель использует AuditableContract

                $serviceOrder = new OrderService($validatedData['docId']);
                $serviceOrder->resaveOrderList();

            }

            Alert::success(CustomTranslator::get('Заказ') . ' ' . $validatedData['docId'] . ' ' . CustomTranslator::get('переведен в статус "Новый"!'));

        } else {

            Alert::error(CustomTranslator::get('Заказ') . ' ' . $validatedData['docId'] . ' ' . CustomTranslator::get('не может быть переведен в статус "Новый"!'));

        }
    }

    public function saveLegalEntityChanges(Request $request)
    {
        $validatedData = $request->validate([
            'docId' => 'nullable|numeric|min:0',
            'order.o_company_id' => 'nullable|numeric|min:0',
            'status' => 'nullable|numeric|min:0',

        ]);

        if ($validatedData['status'] <= 10) {

            $order = rwOrder::where('o_id', $validatedData['docId'])
                ->first();

            if ($order) {

                $order->o_company_id = $validatedData['order']['o_company_id'];
                $order->save(); // Автоматически вызовет аудит, если модель использует AuditableContract

            }

            Alert::success(CustomTranslator::get('Юридическо лицо у заказа') . ' ' . $validatedData['docId'] . ' ' . CustomTranslator::get('было изменено!'));

        }
    }

    public function changeStatusToCancel(Request $request)
    {
        $validatedData = $request->validate([
            'docId' => 'nullable|numeric|min:0',
            'status' => 'nullable|numeric|min:0',
        ]);

        if ($validatedData['status'] == 10 || $validatedData['status'] == 15 || $validatedData['status'] == 20 || $validatedData['status'] == 30 || $validatedData['status'] == 40) {

            $order = rwOrder::where('o_id', $validatedData['docId'])
                ->first();

            if ($order) {

                $order->o_status_id = 5;
                $order->save(); // Автоматически вызовет аудит, если модель использует AuditableContract

                $serviceOrder = new OrderService($validatedData['docId']);
                $serviceOrder->resaveOrderList();

            }

            Alert::success(CustomTranslator::get('Заказ') . ' ' . $validatedData['docId'] . ' ' . CustomTranslator::get('был отменен!'));

        } else {

            Alert::error(CustomTranslator::get('Заказ') . ' ' . $validatedData['docId'] . ' ' . CustomTranslator::get('не может быть отменен!'));

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

            Alert::success(CustomTranslator::get('Заказ ') . ' ' . $validatedData['docId'] . ' ' . CustomTranslator::get('переведен в статус "В обработке"!'));

        } else {

            Alert::error(CustomTranslator::get('Заказ') . ' ' . $validatedData['docId'] . ' ' . CustomTranslator::get('не может быть переведен в статус "В обработке"!'));

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

            Alert::success(CustomTranslator::get('Заказ') . ' ' . $validatedData['docId'] . ' ' . CustomTranslator::get('переведен в резерв!'));

        } else {

            Alert::error(CustomTranslator::get('Заказ') . ' ' . $validatedData['docId'] . ' ' . CustomTranslator::get('не может быть переведен в резерв!'));

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

        if (isset($validatedData['orderOfferId'])) {

            $currentWarehouse = new WhCore($validatedData['whId']);
            $sumCount = $sumPrice = 0;
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

                    $sumCount += $offer->oo_qty;
                    $sumPrice += $offer->oo_oc_price * $offer->oo_qty;

                    // Резервируем товары в заказе
                    $currentWarehouse->calcRestOffer($validatedData['orderOfferId'][$id]);

                }


            }

            // Перерезервируем товары в заказе
            $currentWarehouse->reservOffers($validatedData['orderOfferId'][$id]);

            $dbOrder = rwOrder::find($validatedData['docId']);
            $dbOrder->o_count = $sumCount;
            $dbOrder->o_sum = $sumPrice;
            $dbOrder->save();

            Alert::success(CustomTranslator::get('Данные о товаре сохранены!'));


        } else {

            Alert::error(CustomTranslator::get('Сохранять пока нечего! В начале добавьте товары в заказ!'));

        }

    }

    public function addOffer(Request $request)
    {
        $validated = $request->validate([
            'oo_order_id' => 'required|integer|min:1',
            'o_wh_id' => 'required|integer|min:1',
            'oo_offer_id' => 'required|integer|min:1',
            'docDate' => 'nullable|date', // Каждая дата должна быть обязательной и формата даты
        ]);

        $dbOffer = rwOrderOffer::where('oo_order_id', $validated['oo_order_id'])
            ->where('oo_offer_id', $validated['oo_offer_id'])
            ->first();

        if (!isset($dbOffer->oo_id)) {

            $dbOffer = rwOrderOffer::create([
                'oo_order_id' => $validated['oo_order_id'],
                'oo_offer_id' => $validated['oo_offer_id'],
            ]);

            Alert::success(CustomTranslator::get('Товар добавлен в заказ!'));

            $currentWarehouse = new WhCore($validated['o_wh_id']);

            $barcode = '';

            $currentWarehouse->saveOffers(
                $validated['oo_order_id'],
                $validated['docDate'],
                2,                       // Приемка (таблица rw_lib_type_doc)
                $dbOffer->oo_id,                                // ID офера в документе
                $validated['oo_offer_id'],                                // оригинальный ID товара
                0,
                0,
                $barcode,
                0,
                NULL,
                NULL,
            );


        } else {

            Alert::error(CustomTranslator::get('Данный товар уже есть в заказе!'));

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

            Alert::error(CustomTranslator::get('Товар удален из заказа!'));
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

        Alert::info(CustomTranslator::get('Заказ успешно обновлен'));

        return redirect()->route('platform.orders.edit', $order);
    }

}