<?php

namespace App\Orchid\Screens\Offers;

use App\Http\Middleware\Offers\OffersMiddleware;
use App\Imports\OffersImport;
use App\Models\rwOffer;
use App\Models\rwShop;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\TD;

class OffersImportScreen extends Screen
{
    /**
     * Название экрана.
     */
    public function name(): string
    {
        return __('Импорт товаров');
    }

    /**
     * Доступные запросы.
     */
    public function query(): array
    {

        return [
            'importDescriptions' => rwOffer::getImportDescriptions(),
            'recentOffers' => rwOffer::latest()->limit(10)->get(),
        ];
    }

    /**
     * Кнопки действий.
     */
    public function commandBar(): array
    {
        return [
        ];
    }

    /**
     * Макеты отображения.
     */
    public function layout(): array
    {
        $currentUser = Auth::user();
        $dbShopsList = rwShop::where('sh_domain_id', $currentUser->domain_id);


        return [
            Layout::rows([
                Upload::make('offer_file')
                    ->title(__('Загрузите файл с товарами'))
                    ->acceptedFiles('.xlsx, .xls, .csv')
                    ->maxFiles(1),

                Select::make('of_shop_id')
                    ->title(CustomTranslator::get('Выберите магазин'))
                    ->width('100px')
                    ->fromModel(
                        $currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')
                            ? $dbShopsList->get()
                            : $dbShopsList->whereIn('sh_user_id', [$currentUser->id, $currentUser->parent_id])->get(),
                        'sh_name',
                        'sh_id'
                    ),

                Button::make(__('Начать импорт'))
                    ->method('importOffers')
                    ->icon('upload'),
            ]),

            Layout::view('Offers.OffersImportInstructions'),


//            Layout::table('recentOffers', [
//                TD::make('of_id', __('ID')),
//                TD::make('of_name', __('Название')),
//                TD::make('of_sku', __('SKU')),
//                TD::make('of_price', __('Цена')),
//                TD::make('of_status', __('Статус'))->render(fn($offer) => $offer->getStatus->ls_name ?? '-'),
//            ])->title(__('Последние загруженные товары')),
        ];
    }

    /**
     * Обработчик импорта товаров.
     */
    public function importOffers(Request $request)
    {
        $file = $request->file('offer_file');

        if (!$file) {
            Toast::error(__('Пожалуйста, загрузите файл перед импортом.'));
            return;
        }

        // Сохраняем файл в хранилище
        $filePath = $file->store('uploads');
        $fullPath = storage_path('app/' . $filePath);

        if (!file_exists($fullPath)) {
            Toast::error(__('Файл не найден.'));
            return;
        }

        try {
            Excel::import(new OffersImport, $fullPath);
            Toast::success(__('Товары успешно загружены!'));
        } catch (\Exception $e) {
            Toast::error(__('Ошибка при импорте: ') . $e->getMessage());
        }
    }
}