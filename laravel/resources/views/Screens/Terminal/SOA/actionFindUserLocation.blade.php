@php use App\Services\CustomTranslator; @endphp
<style>
    .warningDIV {
        font-size: 15px;
        margin-top: 10px;
    }
</style>

<form action="{{ route('platform.terminal.soa.scan.place', [$soaId, $dbOrder->o_id])  }}" method="GET"
      onsubmit="return validateForm()"
      style="text-align: center; padding: 0px; margin-top: 0px; margin-bottom: 0px;">
    <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
        <div style="text-align: center; padding: 10px; margin-top: 5px; margin-bottom: 5px;">
            <input type="text" name="barcode" id="barcode" size="30" autofocus onkeyup="handleKeyPress(event)">
            <input type="submit" value="Scan" id="btn">
            <p id="error-message"
               class="alert alert-danger warningDIV" role="alert" style="display: none;"
            >{{ CustomTranslator::get("Ошибка: отсканирован неверный штрихкод! Пожалуйста отсканируте ближайшую полку!") }}</p>
        </div>
    </div>
</form>

<script>
    function handleKeyPress(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Предотвращаем отправку по Enter
            document.getElementById('btn').click();
        }
    }

    function validateForm() {
        let barcodeValue = document.getElementById('barcode').value;
        let errorMessage = document.getElementById('error-message');

        let regex = /^102\*(\d+)\*(\d+)$/; // Регулярное выражение для формата 102*YYY*ZZZ
        let match = barcodeValue.match(regex);

        if (!match) {
            errorMessage.textContent = '{{ CustomTranslator::get("Ошибка: отсканирован неверный штрихкод! Пожалуйста отсканируте ближайшую полку!") }}';
            errorMessage.style.display = "block";
            document.getElementById('barcode').value = '';
            return false; // Отмена отправки формы
        }

        let yyy = parseInt(match[1]); // Получаем YYY
        let zzz = parseInt(match[2]); // Получаем ZZZ
        let expectedZZZ = 102 + yyy; // Вычисляем ожидаемую сумму

        if (zzz !== expectedZZZ) {
            errorMessage.textContent = '{{ CustomTranslator::get('Ошибка: не верная контрольная сумма штрих-кода! Пожалуйста отсканируйте ближайшую полку еще раз!') }}';
            errorMessage.style.display = "block";
            document.getElementById('barcode').value = '';
            return false; // Отмена отправки формы
        }

        errorMessage.style.display = "none"; // Если всё ок, скрываем ошибку
        return true; // Разрешаем отправку
    }
</script>

<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="margin: 10px 10px 20px 10px; text-align: center;">

            <div style="width: 95%; padding-right: 10px; font-size: 20px; background-color: #e8dcbb; border-radius: 10px;">{{ CustomTranslator::get('Отсканируйте ближайшую к вам полку, чтобы я мог понять, где именно вы находитесь!') }}</div>
            <br>
            <img src="/img/selectPlace.jpg" style="border-radius: 10px;">

        </div>
    </div>
</div>
