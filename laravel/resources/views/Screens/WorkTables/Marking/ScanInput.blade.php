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

<form action="" method="GET"
      style="text-align: center; padding: 0px 0px 0px 0px; margin-top: 0px; margin-bottom: 0px;">
    <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">

        <div style="text-align: center; padding: 10px 10px 10px 10px; margin-top: 5px; margin-bottom: 5px;">
            <b>{{ CustomTranslator::get('Отсканируйте посылку') }}:</b>
            <input type="text" name="barcode" id="barcode" size="30" autofocus onkeyup="handleKeyPress(event)">
            <input type="hidden" name="cash" id="cash" value="{{ time() }}">
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

