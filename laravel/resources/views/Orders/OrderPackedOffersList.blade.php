@php use App\Services\CustomTranslator; @endphp
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="margin: 20px 20px 20px 20px;">

            <h3>{{ CustomTranslator::get('Информация по упаковке заказа') }}</h3>

            @if($order->o_status_id == 90 && isset($order->getOperationUser->id))
                <div style="background-color: #FFF3CD; padding: 10px; margin-bottom: 10px;">
                    <b>{{ CustomTranslator::get('Заказ упаковывается сотрудником: ') }} {{ $order->getOperationUser->name }}</b>
                </div>
            @endif

            <table class="table">
                <tr>
                    <th class="tdMain">{{ CustomTranslator::get('Дата') }}</th>
                    <th class="tdMain">{{ CustomTranslator::get('Кладовщик') }}</th>
                    <th class="tdMain">{{ CustomTranslator::get('Артикул') }}</th>
                    <th class="tdMain">{{ CustomTranslator::get('Название') }}</th>
                    <th class="tdMain">{{ CustomTranslator::get('Заказано') }}</th>
                    <th class="tdMain">{{ CustomTranslator::get('Упаковано') }}</th>
                    <th class="tdMain">{{ CustomTranslator::get('Место подбора') }}</th>
                </tr>
                @php
                    $sumPackedQty = 0;
                @endphp

                @foreach($arPackedOffersList as $offer)
                    @php
                        $sumPackedQty += $offer['op_qty'];
                    @endphp
                    <tr>
                        <td class="tdMain">{{ $offer['op_data'] }}</td>
                        <td class="tdMain">{{ $offer['op_user_name'] }}</td>
                        <td class="tdMain">{{ $offer['of_article'] }}</td>
                        <td>{{ $offer['of_name'] }}</td>
                        <td class="tdMain">{{ $offer['oo_qty'] }}</td>
                        <td class="tdMain">{{ $offer['op_qty'] }}</td>
                        <td class="tdMain">{{ $offer['end_place'] }}</td>
                    </tr>
                @endforeach
            </table>

            @if($sumPackedQty == 0 && ($currentUser->hasRole('admin') || $currentUser->hasRole('warehouse_manager')))
                <button style="
                    background-color: #f8d7da;
                    color: #721c24;
                    border: 1px solid #f5c6cb;
                    padding: 8px 16px;
                    border-radius: 8px;
                    font-size: 14px;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                " onClick="window.location.href ='{{ route('platform.orders.edit', $order->o_id) }}?action=cancel_packing'">
                    <nobr>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-x-square" viewBox="0 0 16 16">
                            <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                        </svg>
                        Отменить упаковку
                    </nobr>
                </button>
            @endif

        </div>
    </div>
</div>