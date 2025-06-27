@php
    use App\Services\CustomTranslator;
    use Milon\Barcode\DNS1D;
@endphp

        <!-- Стили для печати -->
<style>
    /* Скрываем блок на экране */
    #printableArea {
        display: none;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        #printableArea, #printableArea * {
            visibility: visible;
        }

        #printableArea {
            position: absolute;
            left: 0;
            top: 0;
            display: block;
        }
    }
</style>

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

<form action="{{ route('platform.tables.marking.scan', $queueId) }}" method="GET"
      style="text-align: center; padding: 0px 0px 0px 0px; margin-top: 0px; margin-bottom: 0px;">
    @csrf
    <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">

        <div style="text-align: center; padding: 10px 10px 10px 10px; margin-top: 5px; margin-bottom: 5px;">
            <b>{{ CustomTranslator::get('Отсканируйте посылку') }}:</b>
            <input type="text" name="barcode" id="barcode" size="30" autofocus onkeyup="handleKeyPress(event)">
            <input type="hidden" name="cash" id="cash" value="{{ time() }}">
            @if(isset($order->o_id) && !isset($currentLabel['url']))
                <input type="hidden" name="orderId" id="orderId" value="{{ $order->o_id }}">
            @endif
            <input type="submit" value="Scan" id="btn">
        </div>

    </div>
</form>

<script>
    function handleKeyPress(event) {
        if (event.key === 'Enter') {
            document.getElementById('btn').click();
        }
    }

    document.getElementById("barcode").focus();
</script>

