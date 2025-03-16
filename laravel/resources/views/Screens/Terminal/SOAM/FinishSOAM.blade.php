@php use App\Services\CustomTranslator; @endphp
@if($action == '')
    <style>
        .placeTD {
            border: 2px solid #AAAAAA;
            padding: 10px 10px 10px 10px;
            font-size: 20px;
        }

        .placeName {
            font-size: 12px;
            color: #AAAAAA;
        }
        .warningDIV {
            font-size: 16px;
            margin-top: 10px;
        }
    </style>
    <form action="{{ route('platform.terminal.soam.finish', [$soaId, $dbOrder->o_id])  }}" method="GET"
          onsubmit="return validateForm()"
          style="text-align: center; padding: 0px; margin-top: 0px; margin-bottom: 0px;">
        <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
            <div style="text-align: center; padding: 10px; margin-top: 5px; margin-bottom: 5px;">
                <input type="text" name="barcode" id="barcode" size="30" autofocus onkeyup="handleKeyPress(event)">
                <input type="submit" value="Scan" id="btn">
                <p id="error-message"
                   class="alert alert-danger warningDIV" role="alert" style="margin-top: 10px; display: none;"
                >{{ CustomTranslator::get("Ошибка: вы отсканировали не то место хранения!") }}</p>
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
            let barcodeInput = document.getElementById('barcode');
            let barcodeValue = barcodeInput.value.trim();
            let errorMessage = document.getElementById('error-message');
            let allowedPrefixes = ["{{ $placeType }}*", "108*"];

            if (!allowedPrefixes.some(prefix => barcodeValue.startsWith(prefix))) {
                errorMessage.style.display = 'block'; // Показываем ошибку
                barcodeInput.value = ''; // Очищаем поле
                barcodeInput.focus(); // Устанавливаем фокус
                return false; // Отменяем отправку формы
            } else {
                errorMessage.style.display = 'none'; // Скрываем ошибку
            }

            // Дополнительное подтверждение, если штрих-код начинается с 108*
            if (barcodeValue.startsWith("108*")) {
                let confirmAction = confirm("{{ CustomTranslator::get('Вы уверены, что хотите отменить сборку этого заказа?') }}");
                if (!confirmAction) {
                    barcodeInput.value = ''; // Очищаем поле, если отменено
                    barcodeInput.focus();
                    return false;
                }
            }

            return true; // Разрешаем отправку формы
        }

        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById('barcode').focus();
        });

    </script>

    <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
        <div style="padding: 10px 10px 10px 10px; text-align: center;">

            <div class="alert alert-info warningDIV" role="alert">
                {{ CustomTranslator::get('Поздравляю! Заказ полностью собран. Пожалуйста, привяжите его к') }}:<br><br>
                <b>{{ $placeTypeName }}</b>
                <br><br>
                {{ CustomTranslator::get('Или к') }}:<br>
                <br>
                <b style="color: red">{{ CustomTranslator::get('Месту для отмененных заказов') }}</b><br>
                <i style="color: #999999;">{{ CustomTranslator::get('если вы хотите отменить заказ') }}</i>
            </div>
            <img src="/img/place_{{ $placeType }}.jpg" width="250" style="border-radius: 10px;">
            <br><br>
        </div>
    </div>
@endif