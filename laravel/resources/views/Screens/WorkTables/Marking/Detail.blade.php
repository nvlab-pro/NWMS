@php use App\Services\CustomTranslator; @endphp

@if(isset($currentLabel['url']))
    <script>
        function openLabelWindow(url) {
            const win = window.open(url, '_blank');
            if (win) {
                // Ждём загрузки и вызываем печать
                win.onload = () => {
                    win.focus();
                    win.print();
                };
            } else {
                alert('Браузер блокирует всплывающие окна');
            }
        }

        openLabelWindow('{{ $currentLabel['url'] }}');
    </script>
@endif

<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="margin: 10px 0px 10px 10px;">
            @if(isset($order->o_id))

                <div style="text-align: center; font-size: 30px;"><b>{{ CustomTranslator::get('Номер заказа') }}
                        :</b> {{ $order->o_id }}</div>
                <hr>
                <div style="font-size: 20px;">
                    <table>
                        <tr>
                            <td><b>{{ CustomTranslator::get('Служба доставки') }}
                                    :</b></td>
                            <td>{{ $order->getDs?->getDsName?->ds_name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td><b>{{ CustomTranslator::get('Источник') }}
                                    :</b></td>
                            <td> {{ $order->getDs?->getSource?->int_name ?? '—' }}</td>
                        </tr>
                    </table>
                    <br>

                    <div style="text-align: center;">
                        <b>{{ CustomTranslator::get('Габариты') }}:</b>
                        <table border="1" style="margin: 0 auto; text-align: center;">
                            <tr>
                                <td style="border: 1px solid white; padding: 10px 10px 10px 10px; background-color: #0289e7; color: white;">
                                    <b>{{ CustomTranslator::get('Длина (см.)') }}:</b></td>
                                <td style="border: 1px solid white; padding: 10px 10px 10px 10px; background-color: #0069c7; color: white;">
                                    <b>{{ CustomTranslator::get('Ширина (см.)') }}:</b></td>
                                <td style="border: 1px solid white; padding: 10px 10px 10px 10px; background-color: #0049a7; color: white;">
                                    <b>{{ CustomTranslator::get('Глубина (см.)') }}:</b></td>
                                <td style="border: 1px solid white; padding: 10px 10px 10px 10px; background-color: #0c4128; color: white;">
                                    <b>{{ CustomTranslator::get('Вес (граммы)') }}:</b></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid white; padding: 10px 10px 10px 10px; background-color: #42C9F7; color: white;">
                                    <b>{{ $dX ?? '—' }}</b></td>
                                <td style="border: 1px solid white; padding: 10px 10px 10px 10px; background-color: #22B9F7; color: white;">
                                    <b>{{ $dY ?? '—' }}</b></td>
                                <td style="border: 1px solid white; padding: 10px 10px 10px 10px; background-color: #0289F7; color: white;">
                                    <b>{{ $dZ ?? '—' }}</b></td>
                                <td style="border: 1px solid white; padding: 10px 10px 10px 10px; background-color: #5c8168; color: white;">
                                    <b>{{ $Weight ?? '—' }}</b></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <br>

            @else

                <div class="alert alert-warning" role="alert" style="font-size: 20px;">
                    {{ CustomTranslator::get('Отсканируйте любой заказ, который нужно отправить службой доставки') }}
                    : {{ $currentQueues->getDs->ds_name ?? '—' }}
                </div>

            @endif
        </div>
    </div>
</div>
