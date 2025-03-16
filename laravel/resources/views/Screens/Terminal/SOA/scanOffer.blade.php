@php use App\Services\CustomTranslator; @endphp
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

    .photoOffer {
        width: 150px;
    }

    .textOffer {
        font-size: 18px;
    }

    .offerCount {
        font-size: 35px;
        color: #157347;
    }

    .sizeBlock {
        width: 95%;
    }

    .warningDIV {
        font-size: 15px;
    }
</style>

<form action="{{ route('platform.terminal.soa.get.offer', [$soaId, $dbOrder->o_id])  }}" method="GET"
      onsubmit="return validateForm()"
      style="text-align: center; padding: 0px; margin-top: 0px; margin-bottom: 0px;">
    <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
        <div style="text-align: center; padding: 10px; margin-top: 5px; margin-bottom: 5px;">
            <div class="alert alert-success warningDIV" role="alert">
                <h4>{{ CustomTranslator::get('Найдите и отсканируйте указанный товар') }}:</h4>
            </div>

            <input type="text" name="barcode" id="barcode" size="30" autofocus onkeyup="handleKeyPress(event)">
            <input type="hidden" name="offerId" id="offerId" value="{{ $offerId }}">
            <input type="hidden" name="orderOfferId" id="orderOfferId" value="{{ $orderOfferId }}">
            <input type="submit" value="Scan" id="btn">
            <p id="error-message" class="alert alert-danger warningDIV" style="margin-top: 10px; display: none;"><b>{{ CustomTranslator::get('Ошибка: отсканирован штрих-код не
                    того товара!') }}</b></p>
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

        // Массив допустимых значений
        let validBarcodes = @json($arBarcodes);

        if (!validBarcodes.includes(barcodeValue)) {
            errorMessage.style.display = 'block'; // Показываем ошибку
            let barcodeInput = document.getElementById('barcode');
            barcodeInput.value = ''; // Очищаем поле
            barcodeInput.focus(); // Устанавливаем фокус
            return false; // Отменяем отправку формы
        } else {
            errorMessage.style.display = 'none'; // Скрываем ошибку
            return true; // Разрешаем отправку
        }
    }

</script>

<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="margin: 10px 10px 20px 10px; text-align: center;" class="sizeBlock">

            <h2>{{ $dbOffer->of_name }}</h2>
            <br>
            <table>
                <tr>
                    <td style="text-align: center">
                        <img src="{{ $dbOffer->of_img }}"
                             border="1" class="photoOffer">
                    </td>
                    <td style="vertical-align: top; padding-left: 15px;" class="textOffer">
                        <b>{{ CustomTranslator::get('Арт.') }}:</b> {{ $dbOffer->of_article }}<br>
                        <br>
                        <b>{{ CustomTranslator::get('Возьмите') }}:</b><br>
                        <div class="offerCount"><b>{{ $dbOrderOffer->oo_qty }}</b></div>
                        <br>
                        {{ CustomTranslator::get('и отсканируйте штрих-код этого товара!') }}
                    </td>
                </tr>
            </table>
            <hr>
            <button onclick="skipGoods()" class="btn btn-warning btn-block">{{ CustomTranslator::get('ПРОПУСТИТЬ') }}</button>
            <script>
                function skipGoods() {
                    window.location.href = "";
                }
            </script>

        </div>
    </div>
</div>
