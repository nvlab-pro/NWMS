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
        .warningDIV {
            font-size: 15px;
            margin-top: 10px;
        }
    </style>

    <form action="{{ route('platform.terminal.soa.scan.offer', [$soaId, $dbOrder->o_id])  }}" method="GET"
          onsubmit="return validateForm()"
          style="text-align: center; padding: 0px; margin-top: 0px; margin-bottom: 0px;">
        <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
            <div style="text-align: center; padding: 10px; margin-top: 5px; margin-bottom: 5px;">
                <input type="text" name="barcode" id="barcode" size="30" autofocus onkeyup="handleKeyPress(event)">
                <input type="hidden" name="offerId" id="offerId" value="{{ $offerId }}">
                <input type="hidden" name="orderOfferId" id="orderOfferId" value="{{ $orderOfferId }}">
                <input type="hidden" name="placeId" id="placeId" value="{{ $nextPlaceId }}">
                <input type="submit" value="Scan" id="btn">
                <p id="error-message"
                   class="alert alert-danger warningDIV" role="alert" style="margin-top: 10px; display: none;"
                >{{ CustomTranslator::get("Ошибка: вы отсканировали не то место хранения!") }} (102*{{ $nextPlaceId }}*{{ $nextPlaceId+102 }})</p>
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

            if (barcodeValue !== '102*{{ $nextPlaceId }}*{{ $nextPlaceId+102 }}') {
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
            <div style="margin: 10px 10px 20px 10px; text-align: center;">

                <div style="width: 95%; padding-right: 10px; font-size: 20px; background-color: #c6e3ff; border-radius: 10px;">{{ CustomTranslator::get('Пройдите к месту размещения и отсканируйте полку') }}</div>
                <br>
                    <table style="width: 95%; background-color: #fff3cd;">
                        <tr>
                            @if(isset($arPlace['pl_room']))
                                <td class="placeTD">
                                    {{ $arPlace['pl_room'] }}
                                    <div class="placeName">room</div>
                                </td>
                            @endif
                            @if(isset($arPlace['pl_floor']))
                                <td class="placeTD">
                                    {{ $arPlace['pl_floor'] }}
                                    <div class="placeName">floor</div>
                                </td>
                            @endif
                            @if(isset($arPlace['pl_section']))
                                <td class="placeTD">
                                    {{ $arPlace['pl_section'] }}
                                    <div class="placeName">section</div>
                                </td>
                            @endif
                            @if(isset($arPlace['pl_row']))
                                <td class="placeTD">
                                    {{ $arPlace['pl_row'] }}
                                    <div class="placeName">row</div>
                                </td>
                            @endif
                            @if(isset($arPlace['pl_rack']))
                                <td class="placeTD">
                                    {{ $arPlace['pl_rack'] }}
                                    <div class="placeName">rack</div>
                                </td>
                            @endif
                            @if(isset($arPlace['pl_shelf']))
                                <td class="placeTD">
                                    {{ $arPlace['pl_shelf'] }}
                                    <div class="placeName">shelf</div>
                                </td>
                            @endif
                        </tr>
                    </table>
                <br>
                <img src="/img/findPlace.jpg" style="border-radius: 10px;" width="250">

            </div>
        </div>
    </div>
