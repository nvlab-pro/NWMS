<?php

namespace App\Orchid\Screens\Datamatrix;

use App\Models\rwDatamatrix;
use App\Models\rwShop;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Color;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DatamatrixImport;
use Illuminate\Support\Facades\Auth;

class DatamatrixScreen extends Screen
{
    public $dbShop;

    public function query(): iterable
    {
        $currentUser = Auth::user();

        $dbShop = rwShop::where('sh_domain_id', $currentUser->domain_id)
            ->with('getOwner');

        if (!$currentUser->hasRole('admin') && !$currentUser->hasRole('warehouse_manager')) {
            $dbShop->where('sh_user_id', $currentUser->id);
        }

//        $this->dbShop = $dbShop->pluck('sh_name', 'sh_id')->toArray();
        $this->dbShop = $dbShop->pluck('sh_id')->toArray();

        return [
            'datamatrixList' => !empty($this->dbShop)
                ? rwDatamatrix::whereIn('dmt_shop_id', $this->dbShop)->paginate(50)
                : rwDatamatrix::whereRaw('1 = 0')->paginate(50), // безопасно вернёт пустой результат
        ];
    }

    public function name(): ?string
    {
        return CustomTranslator::get('Коды DataMatrix');
    }

    public function description(): ?string
    {
        return CustomTranslator::get('Управление кодами маркировки');
    }

    public function commandBar(): iterable
    {
        return [
            Link::make(CustomTranslator::get('Импорт товаров'))
                ->icon('bs.cloud-upload')
                ->route('platform.lib.datamatrix.import'),
        ];
    }

    public function layout(): iterable
    {
        return [

            Layout::table('datamatrixList', [
                TD::make('dmt_id', CustomTranslator::get('ID'))->sort()->align('center'),

                TD::make('dmt_status', CustomTranslator::get('Статус'))->render(function (rwDatamatrix $code) {

                    if ($code->dmt_status == 0) return '-';
                    if ($code->dmt_status == 1) return '❌';
                    if ($code->dmt_status == 2) return '✅';
                })->align('center'),

                TD::make('dmt_barcode', CustomTranslator::get('Штрихкод'))->filter()->align('center'),

                TD::make('dmt_short_code', CustomTranslator::get('Короткий код'))->filter()->align('center'),

                TD::make('dmt_datamatrix', CustomTranslator::get('Код DataMatrix'))->filter()->align('center'),

                TD::make('dmt_acceptance_id', CustomTranslator::get('Документ приемки'))->filter()->align('center'),
                TD::make('dmt_order_id', CustomTranslator::get('Документ отгрузки'))->filter()->align('center'),

                TD::make(CustomTranslator::get('Действия'))->render(function (rwDatamatrix $code) {
                    if ($code->dmt_status) {
                        return '—';
                    }

                    return Button::make(CustomTranslator::get('Погасить'))
                        ->icon('fire')
                        ->confirm(CustomTranslator::get('Вы уверены, что хотите погасить этот код?'))
                        ->method('extinguish', [
                            'id' => $code->dmt_id,
                        ])
                        ->type(Color::WARNING());
                })->align('center'),
            ]),
        ];
    }

    public function extinguish(Request $request)
    {
        $code = rwDatamatrix::find($request->get('id'));

        if ($code) {
            $code->dmt_status = 2;
            $code->save();

            Alert::success(CustomTranslator::get('Код успешно погашен!'));
        } else {
            Alert::error(CustomTranslator::get('Код не найден!'));
        }
    }

    public function refresh()
    {
        Alert::info(CustomTranslator::get('Обновлено.'));
    }


//    public function import(Request $request)
//    {
//        $file = $request->file('file');
//
//        Excel::import(new DatamatrixImport, $file);
//
//        Alert::success(CustomTranslator::get('Импорт завершён!'));
//    }
}