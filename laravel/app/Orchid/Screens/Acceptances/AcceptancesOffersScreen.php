<?php

namespace App\Orchid\Screens\Acceptances;

use App\Models\MistralLocations;
use App\Models\rwAcceptance;
use App\Models\rwAcceptanceOffer;
use App\Models\rwOffer;
use App\Models\rwWarehouse;
use App\Orchid\Layouts\Acceptances\AcceptancesOffersTable;
use App\Orchid\Layouts\Acceptances\AcceptancesTable;
use Illuminate\Support\Facades\Auth;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Support\Facades\Alert;

class AcceptancesOffersScreen extends Screen
{

    public $acceptId;

    public function query($acceptId): iterable
    {
        $this->acceptId = $acceptId;

        $currentUser = Auth::user();

        $dbAcceptOffersList = rwAcceptanceOffer::where('ao_acceptance_id', $this->acceptId)->orderBy('ao_id', 'DESC');

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')) {


        } else {

//            $dbWhList = $dbAcceptList->where('wh_user_id', $currentUser->id);

        }

        $dbAcceptOffersList = $dbAcceptOffersList->with('getOffers');

        return [
            'acceptId' => $this->acceptId,
            'dbAcceptOffersList' => $dbAcceptOffersList->paginate(50),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Приемка № ' . $this->acceptId;
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make(__(' Добавить товар в накладную'))
                ->icon('bs.plus-circle')
                ->modal('addOfferModal') // Имя модального окна
                ->method('addOffer'), // Класс для стилизации (по желанию)
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
            Layout::modal('addOfferModal', [
                Layout::rows([

                    Select::make('offer.of_id')
                        ->title(__('Выберите добавляемый товар:'))
                        ->width('100px')
                        ->options(
                            rwOffer::where('of_shop_id', $this->acceptId)
                                ->whereNotIn('of_id', function ($query) {
                                    $query->select('ao_offer_id')
                                        ->from('rw_acceptance_offers')
                                        ->where('ao_acceptance_id', $this->acceptId); // Условие для конкретной накладной
                                })
                                ->get()
                                ->mapWithKeys(function ($offer) {
                                    return [$offer->of_id => $offer->getShop->sh_name . ': ' . $offer->of_name . ' (' . $offer->of_article . ')'];
                                })->toArray()
                        )
                        ->empty('Не выбрано', 0),

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
                // Ваша форма и таблицы
                Button::make('Сохранить изменения')
                    ->class('btn btn-primary d-block mx-auto')
                    ->method('saveChanges'), // Указывает метод экрана для вызова
            ]),
            Layout::view('acceptances.AcceptanceOffersFormFooter'),
        ];
    }

    public function saveChanges(Request $request)
    {
        $data = $request->all(); // Получаем все данные формы

        /*
                foreach ($data['docOfferExpDate'] as $id => $expirationDate) {
                    $offer = rwAcceptanceOffer::find($id);
                    if ($offer) {
                        $offer->ao_expiration_date = $expirationDate;
                        $offer->ao_barcode = $data['docOfferBarcode'][$id] ?? null;
                        $offer->ao_expected = $data['docOfferExept'][$id] ?? null;
                        $offer->ao_accepted = $data['docOfferAccept'][$id] ?? null;
                        $offer->ao_price = $data['docOfferPrice'][$id] ?? null;

                        $offer->save();
                    }
                }
        */

        Toast::info('Данные успешно сохранены');
    }

    public function deleteOffer(Request $request)
    {
        $data = $request->all(); // Получаем все данные формы

        rwAcceptanceOffer::where('ao_id', $data['offerId'])->forceDelete();

        Toast::success('Данные успешно удалены');
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

    public function addOffer(Request $request)
    {
        $validated = $request->validate([
            'offer.of_id' => 'required|integer|min:1',
        ]);

        $dbOffersList = rwAcceptanceOffer::where('ao_acceptance_id', $this->acceptId)
            ->where('ao_offer_id', $validated['offer']['of_id'])
            ->first();

        if (!isset($dbOffersList->ao_id)) {

            rwAcceptanceOffer::insert([
                'ao_acceptance_id' => $this->acceptId,
                'ao_offer_id' => $validated['offer']['of_id'],
            ]);

            Alert::success(__('Товар добавлен в накладную'));

        } else {

            Alert::error(__('Данный товар уже есть в накладной!'));

        }

    }
}
