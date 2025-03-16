<?php

namespace App\Orchid\Screens\terminal\SOAM;

use App\Models\rwOrder;
use App\Models\rwSettingsSoa;
use App\Services\CustomTranslator;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;

class EndSOAMScreen extends Screen
{
    private $orderId;

    public function query($soaId, $orderId, Request $request): iterable
    {
        $validatedData = $request->validate([
            'barcode' => 'nullable|string',
        ]);

        isset($validatedData['barcode']) ? $barcode = $validatedData['barcode'] : $barcode = '';

        $this->orderId = $orderId;

        // Получаем заказ для сборки
        $dbOrder = rwOrder::find($orderId);

        // Если заказ есть
        if ($dbOrder) {

            $dbSOASettings = rwSettingsSoa::where('ssoa_id', $soaId)
                ->with('getFinishPlace')
                ->first();

            if ($dbSOASettings) {

                $placeTypeName = $dbSOASettings->getFinishPlace->pt_name;
                $placeType = $dbSOASettings->getFinishPlace->pt_id;

            }

        }

        return [];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return CustomTranslator::get('Заказ') . ' ' . $this->orderId . ' ' . CustomTranslator::get('завершен') . '!';
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
        return [];
    }
}
