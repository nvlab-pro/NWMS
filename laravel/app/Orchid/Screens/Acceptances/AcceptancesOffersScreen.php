<?php

namespace App\Orchid\Screens\Acceptances;

use App\Models\rwAcceptance;
use App\Models\rwAcceptanceOffer;
use App\Models\rwBarcode;
use App\Models\rwOffer;
use App\Orchid\Layouts\Acceptances\AcceptancesOffersTable;
use App\Orchid\Services\DocumentService;
use App\Services\CustomTranslator;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Support\Facades\Alert;
use App\WhCore\WhCore;

class AcceptancesOffersScreen extends Screen
{

    protected $acceptId, $shopId, $whId, $whName, $docStatus, $docDate;
    protected $isExpirationDate, $isBatch, $isProductionDate;

//    public function screenBaseView(): string
//    {
//        return 'loyouts.base';
//
//    }

    public function query($acceptId): iterable
    {
        $this->acceptId = $acceptId;

        $currentUser = Auth::user();

        if ($currentUser->hasRole('admin'))
            $dbCurrentAcceptance = rwAcceptance::where('acc_id', $this->acceptId)->with('getWarehouse')->first();
        else
            $dbCurrentAcceptance = rwAcceptance::where('acc_id', $this->acceptId)->where('acc_domain_id', $currentUser->domain_id)->with('getWarehouse')->first();

        if ($dbCurrentAcceptance) {
            $this->shopId = $dbCurrentAcceptance->acc_shop_id;
            $this->whId = $dbCurrentAcceptance->acc_wh_id;
            $this->whName = $dbCurrentAcceptance->getWarehouse->wh_name;
            $this->docStatus = $dbCurrentAcceptance->acc_status;
            $this->docDate = $dbCurrentAcceptance->acc_date;

            $this->isExpirationDate = $dbCurrentAcceptance->getWarehouse->wh_set_expiration_date;
            $this->isBatch = $dbCurrentAcceptance->getWarehouse->wh_set_batch;
            $this->isProductionDate = $dbCurrentAcceptance->getWarehouse->wh_set_production_date;
        }

        $currentDocument = new DocumentService($this->acceptId);
        $collection = $currentDocument->getAcceptanceList();

        $route = route('platform.acceptances.offers', $acceptId);

        return [
            'acceptId'              => $this->acceptId,
            'whId'                  => $this->whId,
            'shopId'                => $this->shopId,
            'docDate'                => $this->docDate,
            'dbAcceptOffersList'    => $collection,
            'route'                => $route,

            'isExpirationDate'      => $this->isExpirationDate,
            'isBatch'               => $this->isBatch,
            'isProductionDate'      => $this->isProductionDate,        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Приемка № ') . $this->acceptId . ' (' . $this->whName . ')';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        $arLinksList = [];
        $arLinksList2 = [];
        $arLinksList3 = [];

        $currentUser = Auth::user();

        // Статус 1 (NEW)
        if ($this->docStatus == 1) {

            if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {

                $arLinksList2 = [

                    Button::make(CustomTranslator::get('Начать приемку'))
                        ->class('btn btn-info')
                        ->method('startAccepting') // Метод, вызываемый на сервере
                        ->parameters([
                            'acceptId' => $this->acceptId,
                            '_token' => csrf_token() // Добавляем CSRF-токен вручную
                        ])
                        ->confirm(CustomTranslator::get('Вы уверены, что хотите начать приемку?')),
                ];

            } else {


            }

            $arLinksList3 = [

                Link::make(CustomTranslator::get('Импорт документа'))
                    ->icon('bs.cloud-upload')
                    ->route('platform.acceptance.import', $this->acceptId),

                Button::make('NEW')
                    ->class('btn btn-danger')
                    ->disabled(true),

            ];

        }

        // Статус 2 (Приемка)
        if ($this->docStatus == 2) {

            $arLinksList = [
                Button::make(CustomTranslator::get('Закрыть накладную'))
                    ->icon('bs.lock')
                    ->method('closeDocument') // Метод, вызываемый на сервере
                    ->parameters([
                        'docId' => $this->acceptId,
                        '_token' => csrf_token(), // Добавляем CSRF-токен вручную
                    ])
                    ->confirm(CustomTranslator::get('Вы уверены, что хотите закрыть накладную?')),
            ];

            $arLinksList3 = [
                Button::make('Приемка')
                    ->class('btn')
                    ->style('background-color: #128497; color: white;')
                    ->disabled(true),

            ];

        }

        // Статус 4 (Накладная закрыта)
        if ($this->docStatus == 4) {

            $arLinksList3 = [
                Button::make(CustomTranslator::get('Закрыта'))
                    ->class('btn')
                    ->style('background-color: #119900; color: white;')
                    ->disabled(true),

            ];


        }

        return array_merge($arLinksList, $arLinksList2, $arLinksList3);

    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {

        return [
            Layout::modal('editDimensions', [
                Layout::rows([
                    Input::make('offer.dimension_x')
                        ->title(CustomTranslator::get('Длина'))
                        ->required(),

                    Input::make(CustomTranslator::get('offer.dimension_y'))
                        ->title('Ширина')
                        ->required(),

                    Input::make(CustomTranslator::get('offer.dimension_z'))
                        ->title('Высота')
                        ->required(),

                    Input::make('offer.weight')
                        ->title(CustomTranslator::get('Вес'))
                        ->required(),
                ]),
            ])->async('asyncGetOfferDimensions'), // Метод для загрузки данных

            Layout::rows([
                Group::make([

                    Select::make('of_id')
                    ->title(CustomTranslator::get('Выберите добавляемый товар:'))
                    ->width('100px')
                    ->options(
                        rwOffer::where('of_shop_id', $this->shopId)
                            ->whereNotIn('of_id', function ($query) {
                                $query->select('ao_offer_id')
                                    ->from('rw_acceptance_offers')
                                    ->where('ao_acceptance_id', $this->acceptId); // Условие для конкретной накладной
                            })
                            ->get()
                            ->mapWithKeys(function ($offer) {
                                return [$offer->of_id => $offer->of_name . ' (' . $offer->of_article . ')'];
                            })->toArray()
                    )
                    ->horizontal()
                    ->empty('', 0),

                Button::make(CustomTranslator::get('Добавить товар'))
                    ->method('addOffer')
                    ->class('btn btn-outline-success btn-sm')
                    ->parameters([
                        'acceptId' => $this->acceptId,
                        'whId' => $this->whId,
                        'docDate' => $this->docDate,
                        ]),

                ])->fullWidth(),
            ])->canSee($this->docStatus < 4),

            AcceptancesOffersTable::class,

            Layout::rows([
                Button::make(CustomTranslator::get('Сохранить изменения'))
                    ->method('saveChanges')
                    ->class('btn btn-primary d-block mx-auto')
                    ->parameters([
                        'acceptId' => $this->acceptId,
                        'shopId' => $this->shopId,
                        'whId' => $this->whId,
                        'docDate' => $this->docDate,
                    ]),
            ])->canSee($this->docStatus < 4),

        ];
    }

    public function saveChanges(Request $request)
    {

        $validatedData = $request->validate([
            'docOfferProdDate.*' => 'nullable|date', // Каждая дата должна быть обязательной и формата даты
            'docOfferExpDate.*' => 'nullable|date', // Каждая дата должна быть обязательной и формата даты
            'docOfferBatch.*' => 'nullable|string|max:15',
            'docOfferBarcode.*' => 'nullable|string|max:30', // Штрих-код может быть пустым, но если указан, то это строка
            'docOfferExept.*' => 'nullable|numeric|min:0', // Ожидаемое количество должно быть числом >= 0
            'docOfferAccept.*' => 'nullable|numeric|min:0', // Принятое количество должно быть числом >= 0
            'docOfferPrice.*' => 'nullable|numeric|min:0', // Цена должна быть числом >= 0
            'docOfferId.*' => 'nullable|numeric|min:0', // Цена должна быть числом >= 0
            'docOfferPlaced.*' => 'nullable|numeric|min:0', // Цена должна быть числом >= 0
            'acceptId' => 'nullable|numeric|min:0', // Цена должна быть числом >= 0
            'shopId' => 'nullable|numeric|min:0', // Цена должна быть числом >= 0
            'whId' => 'nullable|numeric|min:0', // Цена должна быть числом >= 0
            'docDate' => 'nullable|date_format:Y-m-d H:i:s', // Каждая дата должна быть обязательной и формата даты
        ]);

        $currentWarehouse = new WhCore($validatedData['whId']);

        $countExpected = $countAccepted = $countPlaced = 0;

        foreach ($validatedData['docOfferId'] as $id => $expirationDate) {

            $offer = rwAcceptanceOffer::find($id);

            if ($offer && ($validatedData['docOfferPlaced'][$id] == 0 || $validatedData['docOfferPlaced'][$id] === null)) {

                $offer->ao_expected = $validatedData['docOfferExept'][$id] ?? 0;
                $offer->save();

                $status = 0;

                $currentWarehouse->saveOffers(
                    $validatedData['acceptId'],
                    $validatedData['docDate'],
                    1,                                  // Приемка (таблица rw_lib_type_doc)
                    $id,                                        // ID офера в документе
                    $validatedData['docOfferId'][$id],          // оригинальный ID товара
                    $status,
                    $validatedData['docOfferAccept'][$id],
                    $validatedData['docOfferBarcode'][$id],
                    $validatedData['docOfferPrice'][$id],
                    $validatedData['docOfferExpDate'][$id] ?? null,
                    $validatedData['docOfferBatch'][$id] ?? null,
                    $validatedData['docOfferProdDate'][$id] ?? null
                );

                $countExpected += $offer->ao_expected;
                $countAccepted += $validatedData['docOfferAccept'][$id];

                if ($validatedData['docOfferBarcode'][$id] != '') {

                    $currentBarcode = rwBarcode::where('br_offer_id', $validatedData['docOfferId'][$id])
                        ->where('br_barcode', $validatedData['docOfferBarcode'][$id])
                        ->first();

                    if (!isset($currentBarcode->br_id)) {
                        rwBarcode::query()->insert([
                            'br_offer_id' => $validatedData['docOfferId'][$id],
                            'br_shop_id' => $validatedData['shopId'],
                            'br_barcode' => $validatedData['docOfferBarcode'][$id],
                        ]);
                    }
                }

                $currentWarehouse->calcRestOffer($validatedData['docOfferId'][$id]);

            }
        }

        $countPlaced = $currentWarehouse->getPlacedCount($validatedData['acceptId'], 1);

        rwAcceptance::where('acc_id', $validatedData['acceptId'])->update([
            'acc_count_expected'     => $countExpected,
            'acc_count_accepted'     => $countAccepted,
            'acc_count_placed'       => $countPlaced,
        ]);

        Alert::success(CustomTranslator::get('Данные успешно сохранены'));
    }

    public function deleteOffer(Request $request)
    {
        $data = $request->all(); // Получаем все данные формы

        rwAcceptanceOffer::where('ao_id', $data['offerId'])->forceDelete();

        $currentWarehouse = new WhCore($data['whId']);

        $currentWarehouse->deleteItem($data['offerId'], 1);

        Toast::error(CustomTranslator::get('Данные успешно удалены!'));
    }

    public function asyncGetOfferDimensions(int $offerId): array
    {
//        $offer = rwAcceptanceOffer::find($offerId);
//
//        if (!$offer || !$offer->getOffers) {
//            return ['error' => 'Offer not found or missing related data'];
//        }
//
//        return [
//            'offer' => [
//                'dimension_x' => $offer->getOffers->of_dimension_x ?? 0,
//                'dimension_y' => $offer->getOffers->of_dimension_y ?? 0,
//                'dimension_z' => $offer->getOffers->of_dimension_z ?? 0,
//                'weight' => $offer->getOffers->of_weight ?? 0,
//            ],
//        ];
        return [
            'offer' => [
                'dimension_x' => 1,
                'dimension_y' => 2,
                'dimension_z' => 3,
                'weight' => 4,
            ],
        ];
    }

    public function saveDimensions(Request $request)
    {
        $validated = $request->validate([
            'offerId' => 'required|integer',
            'offer.dimension_x' => 'required|numeric',
            'offer.dimension_y' => 'required|numeric',
            'offer.dimension_z' => 'required|numeric',
            'offer.weight' => 'required|numeric',
        ]);

        $offer = rwAcceptanceOffer::find($validated['offerId']);
        $offer->getOffers->update([
            'of_dimension_x' => $validated['offer']['dimension_x'],
            'of_dimension_y' => $validated['offer']['dimension_y'],
            'of_dimension_z' => $validated['offer']['dimension_z'],
            'of_weight' => $validated['offer']['weight'],
        ]);

        Toast::info(CustomTranslator::get('Размеры успешно обновлены.'));
    }

    public function startAccepting($acceptId)
    {

        $dbAcceptance = rwAcceptance::where('acc_id', $acceptId)->first();

        if ($dbAcceptance->acc_status == 1) {

            rwAcceptance::where('acc_id', $acceptId)->update([
                'acc_status' => 2,
            ]);

            Alert::info(CustomTranslator::get('Приемка началась!'));

        } else {

            Alert::error(CustomTranslator::get('Текущий статус накладной не позволяет перевод документа в статус приемки!'));

        }


    }

    public function addOffer(Request $request)
    {
        $validated = $request->validate([
            'of_id' => 'required|integer|min:1',
            'acceptId' => 'required|integer|min:1',
            'whId' => 'required|integer|min:1',
            'docDate' => 'nullable|date_format:Y-m-d H:i:s', // Каждая дата должна быть обязательной и формата даты
        ]);

        $dbOffersList = rwAcceptanceOffer::where('ao_acceptance_id', $validated['acceptId'])
            ->where('ao_offer_id', $validated['of_id'])
            ->first();

        if (!isset($dbOffersList->ao_id)) {

            $dbAccptence = rwAcceptanceOffer::create([
                'ao_acceptance_id' => $validated['acceptId'],
                'ao_offer_id' => $validated['of_id'],
            ]);

            Alert::success(CustomTranslator::get('Товар добавлен в накладную'));

            $currentWarehouse = new WhCore($validated['whId']);

            $barcode = '';
            $dbBarcode = rwBarcode::where('br_offer_id', $validated['of_id'])
                ->where('br_main', 1)
                ->first();

            if ($dbBarcode) {
                $barcode = $dbBarcode->br_barcode;
            } else {

                $dbBarcode = rwBarcode::where('br_offer_id', $validated['of_id'])
                    ->orderBy('br_id', 'DESC')
                    ->first();

                if ($dbBarcode) $barcode = $dbBarcode->br_barcode;

            }

            $currentWarehouse->saveOffers(
                $validated['acceptId'],
                $validated['docDate'],
                1,                       // Приемка (таблица rw_lib_type_doc)
                $dbAccptence->ao_id,                                // ID офера в документе
                $validated['of_id'],                                // оригинальный ID товара
                0,
                0,
                $barcode,
                0,
                NULL,
                NULL,
            );


        } else {

            Alert::error(CustomTranslator::get('Данный товар уже есть в накладной!'));

        }

    }

    public function closeDocument($docId)
    {

        rwAcceptance::where('acc_id', $docId)
            ->where('acc_status', 2)
            ->update([
                'acc_status' => 4,
            ]);

        Alert::info(CustomTranslator::get('Накладная закрыта!'));

    }

    public function deleteItem($acceptId, $offerId)
    {

        $whId = rwAcceptance::where('acc_id', $acceptId)->first()->acc_wh_id;

        $currentWarehouse = new WhCore($whId);
        $currentWarehouse->deleteItemFromDocument($offerId, $acceptId, 1);

        rwAcceptanceOffer::where('ao_id', $offerId)
            ->where('ao_acceptance_id', $acceptId)
            ->forceDelete();

        Alert::error(CustomTranslator::get('Товар удален!'));

    }

}
