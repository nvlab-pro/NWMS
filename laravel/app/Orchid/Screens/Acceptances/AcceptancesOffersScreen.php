<?php

namespace App\Orchid\Screens\Acceptances;

use App\Models\rwAcceptance;
use App\Models\rwAcceptanceOffer;
use App\Models\rwBarcode;
use App\Models\rwOffer;
use App\Orchid\Layouts\Acceptances\AcceptancesOffersTable;
use App\Orchid\Services\DocumentService;
use Illuminate\Support\Facades\Auth;
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

    public function query($acceptId): iterable
    {
        $this->acceptId = $acceptId;

        $currentUser = Auth::user();

        $dbCurrentAcceptance = rwAcceptance::where('acc_id', $this->acceptId)->where('acc_domain_id', $currentUser->domain_id)->with('getWarehouse')->first();
        $this->shopId = $dbCurrentAcceptance->acc_shop_id;
        $this->whId = $dbCurrentAcceptance->acc_wh_id;
        $this->whName = $dbCurrentAcceptance->getWarehouse->wh_name;
        $this->docStatus = $dbCurrentAcceptance->acc_status;
        $this->docDate = $dbCurrentAcceptance->acc_date;

        $currentDocument = new DocumentService($this->acceptId);
        $collection = $currentDocument->getAcceptanceList();

        return [
            'acceptId'              => $this->acceptId,
            'whId'                  => $this->whId,
            'shopId'                => $this->shopId,
            'dbAcceptOffersList'    => $collection,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Приемка № ') . $this->acceptId . ' (' . $this->whName . ')';
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

                    Button::make(__('Начать приемку'))
                        ->class('btn btn-info')
                        ->method('startAccepting') // Метод, вызываемый на сервере
                        ->parameters([
                            'acceptId' => $this->acceptId,
                            '_token' => csrf_token() // Добавляем CSRF-токен вручную
                        ])
                        ->confirm(__('Вы уверены, что хотите начать приемку?')),
                ];

            } else {


            }

            $arLinksList = [
                ModalToggle::make(__(' Добавить товар'))
                    ->icon('bs.plus-circle')
                    ->modal('addOfferModal') // Имя модального окна
                    ->method('addOffer'), // Класс для стилизации (по желанию)

            ];

            $arLinksList3 = [
                Button::make('NEW')
                    ->class('btn btn-danger')
                    ->disabled(true),

            ];

        }

        // Статус 2 (Приемка)
        if ($this->docStatus == 2) {

            $arLinksList = [
                Button::make(__('Закрыть накладную'))
                    ->icon('bs.lock')
                    ->method('closeDocument') // Метод, вызываемый на сервере
                    ->parameters([
                        'docId' => $this->acceptId,
                        '_token' => csrf_token(), // Добавляем CSRF-токен вручную
                    ])
                    ->confirm(__('Вы уверены, что хотите закрыть накладную?')),
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
                Button::make(__('Закрыта'))
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
            Layout::modal('addOfferModal', [
                Layout::rows([
                    Input::make('offer.acceptId')
                        ->value($this->acceptId)
                        ->type('hidden'),

                    Input::make('offer.whId')
                        ->value($this->whId)
                        ->type('hidden'),

                    Input::make('offer.shopId')
                        ->value($this->shopId)
                        ->type('hidden'),

                    Input::make('offer.docDate')
                        ->value($this->docDate)
                        ->type('text'),

                    Select::make('offer.of_id')
                        ->title(__('Выберите добавляемый товар:'))
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
                        ->empty('', 0),

                ]),
            ])
                ->size('xl')
                ->method('addOffer')
                ->title('Добавление нового товара')->applyButton('Добавить')->closeButton('Закрыть'),

            Layout::modal('editDimensions', [
                Layout::rows([
                    Input::make('offer.dimension_x')
                        ->title('Длина')
                        ->required(),

                    Input::make('offer.dimension_y')
                        ->title('Ширина')
                        ->required(),

                    Input::make('offer.dimension_z')
                        ->title('Высота')
                        ->required(),

                    Input::make('offer.weight')
                        ->title('Вес')
                        ->required(),
                ]),
            ])->async('asyncGetOfferDimensions'), // Метод для загрузки данных

            AcceptancesOffersTable::class,

            Layout::rows([
                Input::make('acceptId')
                    ->type('hidden')
                    ->value($this->acceptId),

                Input::make('whId')
                    ->type('hidden')
                    ->value($this->whId),

                Input::make('shopId')
                    ->type('hidden')
                    ->value($this->shopId),

                Input::make('docDate')
                    ->value($this->docDate)
                    ->type('hidden'),

                // Ваша форма и таблицы
                Button::make('Сохранить изменения')
                    ->canSee($this->docStatus < 4 ? true : false)
                    ->class('btn btn-primary d-block mx-auto')
                    ->method('saveChanges') // Указывает метод экрана для вызова
                    ->parameters([
                        '_token' => csrf_token(), // Добавляем CSRF-токен вручную
                    ]),
            ]),
            Layout::view('Acceptances.AcceptanceOffersFormFooter'),
        ];
    }

    public function saveChanges(Request $request)
    {

        $validatedData = $request->validate([
            'docOfferExpDate.*' => 'nullable|date', // Каждая дата должна быть обязательной и формата даты
            'docOfferBarcode.*' => 'nullable|string|max:30', // Штрих-код может быть пустым, но если указан, то это строка
            'docOfferBatch.*' => 'nullable|string|max:15',
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

        foreach ($validatedData['docOfferExpDate'] as $id => $expirationDate) {

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
                    $expirationDate,
                    $validatedData['docOfferBatch'][$id]
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

        Alert::success('Данные успешно сохранены');
    }

    public function deleteOffer(Request $request)
    {
        $data = $request->all(); // Получаем все данные формы

        rwAcceptanceOffer::where('ao_id', $data['offerId'])->forceDelete();

        $currentWarehouse = new WhCore($data['whId']);

        $currentWarehouse->deleteItem($data['offerId'], 1);

        Toast::error('Данные успешно удалены!');
    }

    public function asyncGetOfferDimensions(int $offerId): array
    {
        $offer = rwAcceptanceOffer::find($offerId);
        return [
            'offer' => [
                'dimension_x' => $offer->getOffers->of_dimension_x,
                'dimension_y' => $offer->getOffers->of_dimension_y,
                'dimension_z' => $offer->getOffers->of_dimension_z,
                'weight' => $offer->getOffers->of_weight,
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

        Toast::info('Размеры успешно обновлены.');
    }

    public function startAccepting($acceptId)
    {

        $dbAcceptance = rwAcceptance::where('acc_id', $acceptId)->first();

        if ($dbAcceptance->acc_status == 1) {

            rwAcceptance::where('acc_id', $acceptId)->update([
                'acc_status' => 2,
            ]);

            Alert::info(__('Приемка началась!'));

        } else {

            Alert::error(__('Текущий статус накладной не позволяет перевод документа в статус приемки!'));

        }


    }

    public function addOffer(Request $request)
    {
        $validated = $request->validate([
            'offer.of_id' => 'required|integer|min:1',
            'offer.acceptId' => 'required|integer|min:1',
            'offer.whId' => 'required|integer|min:1',
            'offer.docDate' => 'nullable|date_format:Y-m-d H:i:s', // Каждая дата должна быть обязательной и формата даты
        ]);

        $dbOffersList = rwAcceptanceOffer::where('ao_acceptance_id', $validated['offer']['acceptId'])
            ->where('ao_offer_id', $validated['offer']['of_id'])
            ->first();

        if (!isset($dbOffersList->ao_id)) {

            $dbAccptence = rwAcceptanceOffer::create([
                'ao_acceptance_id' => $validated['offer']['acceptId'],
                'ao_offer_id' => $validated['offer']['of_id'],
            ]);

            Alert::success(__('Товар добавлен в накладную'));

            $currentWarehouse = new WhCore($validated['offer']['whId']);

            $barcode = '';

            $currentWarehouse->saveOffers(
                $validated['offer']['acceptId'],
                $validated['offer']['docDate'],
                1,                       // Приемка (таблица rw_lib_type_doc)
                $dbAccptence->ao_id,                                // ID офера в документе
                $validated['offer']['of_id'],                                // оригинальный ID товара
                0,
                0,
                $barcode,
                0,
                NULL,
                NULL,
            );


        } else {

            Alert::error(__('Данный товар уже есть в накладной!'));

        }

    }

    public function closeDocument($docId)
    {

        rwAcceptance::where('acc_id', $docId)
            ->where('acc_status', 2)
            ->update([
                'acc_status' => 4,
            ]);

        Alert::info(__('Накладная закрыта!'));

    }

}
