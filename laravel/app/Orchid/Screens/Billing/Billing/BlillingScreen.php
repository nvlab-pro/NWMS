<?php

namespace App\Orchid\Screens\Billing\Billing;

use App\Models\rwBillingSetting;
use App\Services\CustomTranslator;
use Carbon\Carbon;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Alert;

class BlillingScreen extends Screen
{
    public $name = 'Billing settings';
    public $description = 'Список настроек биллинга';

    public function query(): iterable
    {
        return [
            'billings' => rwBillingSetting::paginate()
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make(CustomTranslator::get('Создать'))
                ->icon('bs.plus-circle')
                ->route('platform.billing.billing.create')
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::table('billings', [
                TD::make('bs_id', 'ID')->sort()->render(function ($b) {
                    return Link::make($b->bs_id)
                        ->route('platform.billing.billing.edit', $b->bs_id);
                }),
                TD::make('bs_status', 'Статус')->sort(),
                TD::make('bs_name', 'Название')->sort()->render(function ($b) {
                    return Link::make($b->bs_name)
                        ->route('platform.billing.billing.edit', $b->bs_id);
                }),
                TD::make('bs_data', 'Дата')
                    ->sort()
                    ->render(function ($b) {
                        return Link::make(
                            Carbon::parse($b->bs_data)->format('d.m.Y')  // YYYY-MM-DD → DD.MM.YYYY
                        )->route('platform.billing.billing.edit', $b->bs_id);
                    }),
                TD::make()->width('100px')->render(fn ($b) =>
                Button::make('Удалить')
                    ->icon('bs.trash')
                    ->method('remove', ['id' => $b->bs_id])
                    ->confirm('Точно удалить?')
                ),
            ])
        ];
    }

    public function remove(int $id)
    {
        rwBillingSetting::findOrFail($id)->delete();
        Alert::error(CustomTranslator::get('Запись удалена!'));

    }
}
