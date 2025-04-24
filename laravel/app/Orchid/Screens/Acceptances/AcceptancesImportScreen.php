<?php

namespace App\Orchid\Screens\Acceptances;

use App\Http\Middleware\Offers\OffersMiddleware;
use App\Imports\AcceptancesImport;
use App\Imports\OffersImport;
use App\Models\rwAcceptance;
use App\Models\rwLibAcceptType;
use App\Models\rwOffer;
use App\Models\rwShop;
use App\Models\rwWarehouse;
use App\Orchid\Services\DocumentService;
use App\Services\CustomTranslator;
use App\WhCore\WhCore;
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

class AcceptancesImportScreen extends Screen
{

    public $acceptId;

    public function query($acceptId = 0): iterable
    {

        $this->acceptId = $acceptId;

        return [
            'importDescriptions' => rwAcceptance::getImportDescriptions(),
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Импорт приходных накладных');
    }

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

        if ($this->acceptId == 0) {

            $arFields = [

                Group::make([

                    Select::make('acc_wh_id')
                        ->title(CustomTranslator::get('Склад'))
                        ->width('100px')
                        ->fromModel($dbWhList->get(), 'wh_name', 'wh_id')
                        ->required(),

                    Select::make('acc_shop_id')
                        ->title(CustomTranslator::get('Выберите магазин'))
                        ->fromModel(
                            $dbShopList->get(),
                            'sh_name',
                            'sh_id'
                        )
                        ->empty(CustomTranslator::get('Не выбран')),

                ]),

                Group::make([

                    Input::make('acc_ext_id')
                        ->width(50)
                        ->title(CustomTranslator::get('Внешний ID')),

                    DateTimer::make('acc_date')
                        ->width(50)
                        ->title(CustomTranslator::get('Дата'))
                        ->required()
                        ->format('Y-m-d')
                        ->value(now()->format('Y-m-d')),

                ]),
                Group::make([

                    Select::make('acc_type')
                        ->options(
                            rwLibAcceptType::all()
                                ->pluck('lat_name', 'lat_id')
                                ->map(fn($name) => CustomTranslator::get($name))
                                ->toArray()
                        )
                        ->value(1)
                        ->title(CustomTranslator::get('Тип накладной')),

                    TextArea::make('acc_comment')
                        ->width(50)
                        ->title(CustomTranslator::get('Комментарий')),

                ]),

            ];
        } else {

            $arFields = [
                Input::make('ac_id')
                    ->value($this->acceptId)
                    ->type('hidden'),
            ];

        }

        return [
            Layout::rows(array_merge([
                Upload::make('offer_file')
                    ->title(CustomTranslator::get('Загрузите файл с товарами для приходной накладной'))
                    ->acceptedFiles('.xlsx, .xls, .csv')
                    ->maxFiles(1),

                ],
                $arFields,
                [

                Select::make('import_type')
                    ->options([
                        '0' => CustomTranslator::get('Немедленно'),
                        '1' => CustomTranslator::get('С задержкой'),
                    ])
                    ->title(CustomTranslator::get('Выберите способ загрузки'))
                    ->help('Небольшие файлы могут быть загружены немедленно. Для загрузки файлов с большим количеством записей используйте загрузку с задержкой.'),

                Button::make(CustomTranslator::get('Начать импорт'))
                    ->method('importAcceptance')
                    ->class('btn btn-outline-primary')
                    ->icon('cloud-upload'),
            ])),

            Layout::view('Acceptances.AcceptanceImportInstructions'),

        ];
    }

    /**
     * Обработчик импорта накладной.
     */

    public function importAcceptance(Request $request)
    {
        $currentUser = Auth::user();
        $id = $request->get('offer_file')[0] ?? null;

        $acceptId = $request->get('ac_id') ?? 0;

        if (!$id || $id == null) {
            Alert::error(CustomTranslator::get('Пожалуйста, загрузите файл перед импортом.'));
            if ($acceptId == 0)
                return redirect()->route('platform.acceptances.import');
            else
                return redirect()->route('platform.acceptance.import', $acceptId);
        }

        $attachment = Attachment::find($id);

        $attachment->domain_id = $currentUser->domain_id;
        $attachment->status = 0; // 0 - загружено, 1 - обрабатывается, 2 - импорт окончен, 3 - ошибка
        $attachment->type = 'импорт';
        $attachment->group = 'приемка';
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

//                $acceptId = Excel::import(new AcceptancesImport($request, $id, $acceptId), $fullPath);

                $import = new AcceptancesImport($request, $id, $acceptId);
                Excel::import($import, $fullPath);

                $acceptId = $import->getAcceptId();
                $whId = $import->getWhId();

                $currentDocs = new DocumentService($acceptId);
                $currentDocs->updateRest(1);

                Alert::success(CustomTranslator::get('Товары успешно загружены!'));
                $attachment->status = 2;
                $attachment->save();

                return redirect()->route('platform.acceptances.offers', $acceptId);

            } catch (\Exception $e) {
                Alert::error(CustomTranslator::get('Ошибка при импорте: ') . $e->getMessage());

            }

        } else {

            Alert::success(CustomTranslator::get('Файл успешно загружен и будет обработан в ближайшее время!'));

            return redirect()->route('platform.acceptances.index');
        }
    }
}