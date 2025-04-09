<?php
// ***********************************************
// *** Шаг 3: Непосредственно работа с товаром
// ***********************************************

namespace App\Orchid\Screens\WorkTables\Packing;

use App\Models\rwBarcode;
use App\Models\rwDatamatrix;
use App\Models\rwOffer;
use App\Models\rwOrder;
use App\Models\rwOrderAssembly;
use App\Models\rwOrderOffer;
use App\Models\rwOrderPacking;
use App\Models\rwOrderSorting;
use App\Models\rwPlace;
use App\Models\rwSettingsProcPacking;
use App\Models\WhcRest;
use App\Orchid\Services\ChestnyZnakParser;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Layouts\Modal;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Screen;

class PackingScreen extends Screen
{
    public function screenBaseView(): string
    {
        return 'loyouts.base';
    }

    protected $orderId, $tableId, $queueId, $queueStartPlaceType, $queuePackType, $currentPallet, $currentBox;

    public function query($queueId, $tableId, $orderId, Request $request): iterable
    {

        $currentUser = Auth::user();
        $this->orderId = $orderId;
        $arOffersList = [];
        $arPackedOffersList = [];
        $printPalletLabel = 0;
        $printBoxLabel = 0;

        $countAssembledOffers = 0;

        isset($request->barcode) ? $barcode = $request->barcode : $barcode = null;
        isset($request->cash) ? $cash = $request->cash : $cash = null;

        $dbCurrentPlace = rwPlace::find($tableId);
        $this->tableId = $dbCurrentPlace->pl_shelf;
        $this->queueId = $queueId;

        // **************************************
        // Получаем настройки очереди
        $currentQueue = rwSettingsProcPacking::where('spp_id', $queueId)
            ->first();

        if ($currentQueue) {

            // Получаем данные очереди
            $queueWhId = $currentQueue->spp_wh_id;
            $queueStartPlaceType = $currentQueue->spp_start_place_type; // 102 - подбор с полки, 104 -  Полка сортировки, 105 - Стол упаковки,
            $queueRackFrom = $currentQueue->spp_place_rack_from;
            $queueRackTo = $currentQueue->spp_place_rack_to;
            $queuePackType = $currentQueue->spp_packing_type;           // 0 - Скан артикула (под пересчет), 1 - Скан каждого товара, 2 - Со сканом честного знака

            $this->queueStartPlaceType = $queueStartPlaceType;
            $this->queuePackType = $queuePackType;

        }

        // **************************************
        // Получаем данные по заказу товарам
        $dbOrder = rwOrder::where('o_id', $orderId)
            ->with('getWarehouse')
            ->first();

        $this->currentPallet = $dbOrder->o_current_pallet;
        $this->currentBox = $dbOrder->o_current_box;

        // **************************************
        // Обрабатываем паллеты и коробки
        if ($barcode) {
            if ($barcode == 'NEWPALLET') {
                $this->currentPallet++;

                $dbOrder->o_current_pallet = $this->currentPallet;
                $dbOrder->save();

                Alert::success(CustomTranslator::get('Открыт следующий паллет!'));
                $barcode = null;
                $printPalletLabel = 1;
            }
            if ($barcode == 'PREVPALLET' && $this->currentPallet > 1) {
                $this->currentPallet--;

                $dbOrder->o_current_pallet = $this->currentPallet;
                $dbOrder->save();

                Alert::error(CustomTranslator::get('Вернулись на один паллет назад!'));
                $barcode = null;
            }
            if ($barcode == 'NEWBOX') {
                $this->currentBox++;

                $dbOrder->o_current_box = $this->currentBox;
                $dbOrder->save();

                Alert::success(CustomTranslator::get('Открыт следующий короб!'));
                $barcode = null;
                $printBoxLabel = 1;
            }
            if ($barcode == 'PREVBOX' && $this->currentBox > 1) {
                $this->currentBox--;

                $dbOrder->o_current_box = $this->currentBox;
                $dbOrder->save();

                Alert::error(CustomTranslator::get('Вернулись на один короб назад!'));
                $barcode = null;
            }

        }

        // **************************************
        // 💾 Сохраняем отсканированный товар
        if ($barcode) {

            $selectOffer = null;
            $datamatrixType = null;

            // Если это честный знак, ищем в базе
            if ($queuePackType == 2 && strlen($barcode) > 20) {

                $parser = new ChestnyZnakParser($barcode);

                if ($parser->isValid()) {

                    // Проверка на наличие такого кода в БД
                    $dmExists = rwDatamatrix::where('dmt_datamatrix', $barcode)
                        ->where('dmt_shop_id', $dbOrder->o_shop_id)
                        ->first();;

                    if (isset($dmExists->dmt_id)) {

                        // Запись с этим честным знаком есть в базе

                        if ($dmExists->dmt_status == 0) {
                            $barcode = $parser->getEAN13();
                            $datamatrixType = 1; // Запись есть в базе, нужно будет погасить
                        } else {

                            // Запись есть в базе есть, но была погашена!
                            Alert::error(CustomTranslator::get('Данный честный знак уже был погашен!'));
                            $barcode = null;
                        }

                    } else {

                        // Записи с этим честным знаком в базе нет, проверяю могу ли создать новый
                        // Проверяю есть ли у этого магазина активные честные знаки

                        $dmCount = rwDatamatrix::where('dmt_status', 0)
                            ->where('dmt_shop_id', $dbOrder->o_shop_id)
                            ->count();

                        dump($dbOrder->o_shop_id);

                        if ($dmCount > 0) {

                            Alert::error(CustomTranslator::get('Данного честного знака нет в базе!'));
                            $barcode = null;
                            dump('net 1');

                        } else {
                            // База пуста, создаем новую запись

                            $barcode = $parser->getEAN13();
                            $datamatrixType = 2; // Записи нет в базе, нужно будет создать новую

                            dump('net 2');
                        }

                    }

                } else {

                    Alert::error(CustomTranslator::get('Кривой честный знак!'));
                    $barcode = null;

                }

            }

            if ($barcode != null) {

                // Ищем товар по штрих-кода
                $dbBarcodes = rwBarcode::where('br_barcode', $barcode)
                    ->where('br_shop_id', $dbOrder->o_shop_id)
                    ->get();

                foreach ($dbBarcodes as $currentBarcode) {

                    $dbCurrentOffer = rwOrderOffer::where('oo_order_id', $orderId)
                        ->where('oo_offer_id', $currentBarcode->br_offer_id)
                        ->first();

                    if ($dbCurrentOffer) {
                        $selectOffer = $dbCurrentOffer;
                        break;
                    }
                }

                // Проверяем не должен ли иметь данный товар ЧЗ
                if ($selectOffer && $queuePackType == 2) {

                    $currentOfferId = $selectOffer->oo_offer_id;

                    $dbOffer = rwOffer::where('of_id', $currentOfferId)
                        ->first();

                    if (isset($dbOffer->of_id) && $dbOffer->of_datamatrix == 1 && $datamatrixType == null) {
                        $selectOffer = null;
                    }

                }

                // если товар с таким баркодом в заказе есть
                if ($selectOffer) {

                    $currentPackingOffer = rwOrderPacking::where('op_order_id', $orderId)
                        ->where('op_offer_id', $selectOffer->oo_offer_id)
                        ->where('op_pallet', $this->currentPallet)
                        ->where('op_box', $this->currentBox)
                        ->first();

                    if ($currentPackingOffer) {

                        if ($currentPackingOffer->op_cash != $cash) {

                            rwOrderPacking::where('op_id', $currentPackingOffer->op_id)->update([
                                'op_qty'    => $currentPackingOffer->op_qty + 1,
                                'op_pallet' => $this->currentPallet,
                                'op_box'    => $this->currentBox,
                                'op_cash'   => $cash,
                            ]);

                            Alert::success(CustomTranslator::get('Товар добавлен в заказ!'));

                        }

                    } else {

                        rwOrderPacking::create([
                            'op_order_id' => $orderId,
                            'op_offer_id' => $selectOffer->oo_offer_id,
                            'op_user_id' => $currentUser->id,
                            'op_barcode' => $barcode,
                            'op_data' => date('Y-m-d H:i:s', time()),
                            'op_pallet' => $this->currentPallet,
                            'op_box'    => $this->currentBox,
                            'op_cash' => $cash,
                            'op_qty' => 1,
                        ]);

                        Alert::success(CustomTranslator::get('Товар добавлен в заказ!'));

                    }

                    $dbCurrentOffer->oo_cash = $cash;
                    $dbCurrentOffer->save();

                    // Запись есть в базе, нужно будет погасить
                    if ($datamatrixType == 1) {
                        if ($dmExists) {

                            $dmExists->dmt_status = 1;
                            $dmExists->dmt_order_id = $orderId;
                            $dmExists->dmt_used_date = date('Y-m-d H:i:s', time());

                            $dmExists->save();

                        }
                    }

                    // Записи нет в базе, нужно создать и погасить
                    if ($datamatrixType == 2) {
                        if ($dmExists) {

                            $dmExists = new rwDatamatrix();

                            $dmExists->dmt_status = 1;
                            $dmExists->dmt_shop_id = $dbOrder->o_shop_id;
                            $dmExists->dmt_order_id = $orderId;
                            $dmExists->dmt_barcode = $barcode;
                            $dmExists->dmt_short_code = $parser->getShortCode();
                            $dmExists->dmt_crypto_tail = $parser->getCryptoTail();
                            $dmExists->dmt_datamatrix = $parser->getFullCode();
                            $dmExists->dmt_used_date = date('Y-m-d H:i:s', time());

                            $dmExists->save();

                        }
                    }


                } else {

                    if (isset($dbOffer->of_id) && $queuePackType == 2)
                        Alert::error(CustomTranslator::get('Для данного товара честный знак обязателен!'));
                    else
                        Alert::error(CustomTranslator::get('Товар с таким штрих-кодом в этом заказе нет!'));

                }
            }

        }

        // **************************************
        // Формируем список товаров для вывода

        $dbOffersList = rwOrderOffer::where('oo_order_id', $orderId)
            ->with('getOffer')
            ->orderBy('oo_cash', 'desc')
            ->get();

        foreach ($dbOffersList as $dbOffer) {

            $currentCash = 0;
            if (isset($dbOffersList->oo_cash) && $dbOffersList->oo_cash > 0) $currentCash = $dbOffersList->oo_cash;

            $packedQty = rwOrderPacking::where('op_order_id', $orderId)
                ->where('op_offer_id', $dbOffer->oo_offer_id)
                ->sum('op_qty');

            if ($packedQty == $dbOffer->oo_qty) {
                // Собираем уже упакованные товар

                $arPackedOffersList[$dbOffer->oo_id] = [
                    'oo_id' => $dbOffer->oo_id,
                    'oo_offer_id' => $dbOffer->oo_offer_id,
                    'of_name' => $dbOffer->getOffer->of_name,
                    'of_article' => $dbOffer->getOffer->of_article,
                    'of_img' => $dbOffer->getOffer->of_img,
                    'of_datamatrix' => $dbOffer->getOffer->of_datamatrix,
                    'oo_qty' => $dbOffer->oo_qty,
                    'op_cash' => $currentCash,
                    'packed_qty' => $packedQty,
                ];

            } else {
                // Еще не собранный товары

                $acceptedCount = 0;

                $acceptedCount = rwOrderAssembly::where('oa_order_id', $orderId)
                    ->where('oa_offer_id', $dbOffer->oo_offer_id)
                    ->sum('oa_qty');

                $countAssembledOffers += $dbOffer->oo_qty - $acceptedCount;

                $arOffersList[$dbOffer->oo_id] = [
                    'oo_id' => $dbOffer->oo_id,
                    'oo_offer_id' => $dbOffer->oo_offer_id,
                    'of_name' => $dbOffer->getOffer->of_name,
                    'of_article' => $dbOffer->getOffer->of_article,
                    'of_img' => $dbOffer->getOffer->of_img,
                    'of_datamatrix' => $dbOffer->getOffer->of_datamatrix,
                    'oo_qty' => $dbOffer->oo_qty,
                    'op_cash' => $currentCash,
                    'packed_qty' => $packedQty,
                    'accepted_count' => $acceptedCount,
                ];
            }
        }

//        uasort($arOffersList, function ($a, $b) {
//            return $b['op_cash'] <=> $a['op_cash'];
//        });

        return [
            'dbOrder' => $dbOrder,
            'arOffersList' => $arOffersList,
            'arPackedOffersList' => $arPackedOffersList,
            'dbCurrentPlace' => $dbCurrentPlace,
            'queueId' => $queueId,
            'tableId' => $tableId,
            'queueStartPlaceType' => $queueStartPlaceType,
            'countAssembledOffers' => $countAssembledOffers,
            'queuePackType' => $queuePackType,
            'currentPallet'  => $this->currentPallet,
            'currentBox'  => $this->currentBox,
            'printPalletLabel'  => $printPalletLabel,
            'printBoxLabel'  => $printBoxLabel,

        ];
    }


    public function name(): ?string
    {
        return CustomTranslator::get('Упаковка заказа') . ' № ' . $this->orderId;
    }

    public function description(): ?string
    {
        return CustomTranslator::get('Стол упаковки') . ' № ' . $this->tableId . ', ' . CustomTranslator::get('очередь упаковки') . ' № ' . $this->queueId;
    }


    public function commandBar(): iterable
    {

        return [
            Link::make(CustomTranslator::get('Лист подбора'))
                ->icon('bi.printer')
                ->style('font-size: 24px;')
                ->canSee($this->queueStartPlaceType == 102)
                ->route('platform.tables.packing.assembling.print', [
                    $this->queueId,
                    $this->tableId,
                    $this->orderId,
                    0,
                ]),

            Link::make('')
                ->style('pointer-events: none; border-left: 1px solid #ccc; margin: 0 0 0 0; height: 42px;'),

            // Текущий паллет

//            Link::make('-')
//                ->route('platform.tables.packing.scan', [$this->queueId, $this->tableId, $this->orderId, 'barcode' => 'PREVPALLET'])
//                ->style('background-color: #f98690;' . 'color: #FFFFFF; font-size: 20px; padding: 0px 10px 0px 10px;'),

            Button::make($this->currentPallet)
                ->style('display: inline-block; vertical-align: middle;background-color: #d89e00;' . 'color: #FFFFFF; font-size: 40px; padding: 0px 20px 0px 20px;'),

            Link::make('+')
                ->route('platform.tables.packing.scan', [$this->queueId, $this->tableId, $this->orderId, 'barcode' => 'NEWPALLET'])
                ->style('background-color: #45bf41;' . 'color: #FFFFFF; font-size: 20px; padding: 0px 10px 0px 10px;'),

            Link::make('pallet')
                ->style('color: #FFFFFF; padding: 0 0 45px 0; margin-left: -110px; font-size: 14px'),

            // Текущий короб

//            Link::make('-')
//                ->route('platform.tables.packing.scan', [$this->queueId, $this->tableId, $this->orderId, 'barcode' => 'PREVBOX'])
//                ->style('background-color: #f98690;' . 'color: #FFFFFF; font-size: 20px; padding: 0px 10px 0px 10px;'),

            Button::make($this->currentBox)
                ->style('background-color: #7c5d00;' . 'color: #FFFFFF; font-size: 40px; padding: 0px 20px 0px 20px;'),

            Link::make('+')
                ->route('platform.tables.packing.scan', [$this->queueId, $this->tableId, $this->orderId, 'barcode' => 'NEWBOX'])
                ->style('background-color: #45bf41;' . 'color: #FFFFFF; font-size: 20px; padding: 0px 10px 0px 10px;'),

            Link::make('box')
                ->style('color: #FFFFFF; padding: 0 0 45px 0; margin-left: -106px; font-size: 14px;'),

            // Настройки упаковки

            Link::make('')
                ->style('pointer-events: none; border-left: 1px solid #ccc; margin: 0 0px 0  25px; height: 42px;'),

            Button::make(CustomTranslator::get('Упаковка без сборки'))
                ->style('background-color: #44f237;' . 'color: #000000;')
                ->canSee($this->queueStartPlaceType == 102)
                ->disabled(true),

            Button::make(CustomTranslator::get('Упаковка с мест сортировки'))
                ->style('background-color: #00abb5;' . 'color: #FFFFFF;')
                ->canSee($this->queueStartPlaceType == 104)
                ->disabled(true),

            Button::make(CustomTranslator::get('Упаковка с упаковочного стола'))
                ->style('background-color: #c40000;' . 'color: #FFFFFF;')
                ->canSee($this->queueStartPlaceType == 105)
                ->disabled(true),

            Button::make(CustomTranslator::get('Под пересчет'))
                ->style('background-color: #0ca004;' . 'color: #FFFFFF;')
                ->icon('bi.stack')
                ->canSee($this->queuePackType == 0)
                ->disabled(true),

            Button::make(CustomTranslator::get('Скан каждого товара'))
                ->style('background-color: #a00486;' . 'color: #FFFFFF;')
                ->icon('bi.stack-overflow')
                ->canSee($this->queuePackType == 1)
                ->disabled(true),

            Button::make(CustomTranslator::get('Честный знак'))
                ->style('background-color: #003fff;' . 'color: #FFFFFF;')
                ->icon('bi.qr-code-scan')
                ->canSee($this->queuePackType == 2)
                ->disabled(true),

        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::view('Screens.WorkTables.Packing.ScanInput'),
            Layout::view('Screens.WorkTables.Packing.PackingOffersList'),
        ];
    }

    public function asyncPackedOffers(): iterable
    {
//        $packed = rwOrderPacking::where('op_order_id', $this->orderId)
//            ->with('getOffer')
//            ->get();

        return [
            'items' => [1,2,3,4],
        ];
    }
}
