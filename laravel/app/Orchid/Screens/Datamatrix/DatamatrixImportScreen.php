<?php

namespace App\Orchid\Screens\Datamatrix;

use App\Imports\DatamatrixImport;
use App\Imports\OffersImport;
use App\Models\rwShop;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class DatamatrixImportScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return CustomTranslator::get('Импорт кодов DataMatrix');
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

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        $currentUser = Auth::user();
        $dbShopsList = rwShop::where('sh_domain_id', $currentUser->domain_id);

        return [
            Layout::rows([
                Upload::make('datamatrix_file')
                    ->title(CustomTranslator::get('Загрузите файл с товарами'))
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

                Select::make('import_type')
                    ->options([
                        '0'   => CustomTranslator::get('Немедленно'),
                        '1' => CustomTranslator::get('С задержкой'),
                    ])
                    ->title(CustomTranslator::get('Выберите способ загрузки'))
                    ->help('Небольшие файлы могут быть загружены немедленно. Для загрузки файлов с большим количеством записей используйте загрузку с задержкой.'),

                Button::make(CustomTranslator::get('Начать импорт'))
                    ->method('importDatamatrix')
                    ->class('btn btn-outline-primary')
                    ->icon('cloud-upload'),
            ]),

            Layout::view('Screens.Datamatrix.DatamatrixImportInstructions'),
        ];
    }

    public function importDatamatrix(Request $request)
    {
        $currentUser = Auth::user();
        $id = $request->get('datamatrix_file')[0] ?? null;

        if (!$id || $id == null) {
            Alert::error(CustomTranslator::get('Пожалуйста, загрузите файл перед импортом.'));
            return redirect()->route('platform.lib.datamatrix.import');
        }

        $attachment = Attachment::find($id);

        $attachment->domain_id = $currentUser->domain_id;
        $attachment->status = 0; // 0 - загружено, 1 - обрабатывается, 2 - импорт окончен, 3 - ошибка
        $attachment->type = 'импорт';
        $attachment->group = 'datamatrix';
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

                $currentImport = new  DatamatrixImport($request->get('of_shop_id'), $id);

                Excel::import($currentImport, $fullPath);
                Alert::success(CustomTranslator::get('Файл успешно обработан! Добавлено: ') . $currentImport->getSuccessCount() . ', ' . CustomTranslator::get('в ошибке: ') . $currentImport->getErrorCount() . '. ' . CustomTranslator::get('Подробнее смотрите в разделе управления импорта!'));
                $attachment->status = 2;
                $attachment->save();

                return redirect()->route('platform.lib.datamatrix.import');

            } catch (\Exception $e) {
                Alert::error(CustomTranslator::get('Ошибка при импорте: ') . $e->getMessage());

            }

        } else {

            Alert::success(CustomTranslator::get('Файл успешно загружен и будет обработан в ближайшее время!'));

            return redirect()->route('platform.lib.datamatrix.import');
        }
    }
}
