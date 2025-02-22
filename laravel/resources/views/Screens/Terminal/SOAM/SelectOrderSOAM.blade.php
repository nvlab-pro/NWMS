<style>
    .warningDIV {
        font-size: 15px;
        margin-top: 10px;
    }
    .table tbody tr td.offerNameTD {
        font-size: 16px;
    }
</style>

@if(isset($currentOffer['offerId']))
            <form action="{{ route('platform.terminal.soam.offer', [$soaId, $dbOrder->o_id])  }}" method="GET"
          @else
            <form action="{{ route('platform.terminal.soam.order', [$soaId, $dbOrder->o_id])  }}" method="GET"
          @endif
          onsubmit="return validateForm()"
          style="text-align: center; padding: 0px; margin-top: 0px; margin-bottom: 0px;">
        <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
            <div style="text-align: center; padding: 10px; margin-top: 5px; margin-bottom: 5px;">

                <input type="text" name="barcode" id="barcode" size="30" autofocus onkeyup="handleKeyPress(event)">

                @if(isset($currentOffer['offerId']))
                    <input type="hidden" name="offerId" value="{{ $currentOffer['offerId'] }}">
                @endif
                @if(isset($currentOffer['offerDocId']))
                    <input type="hidden" name="orderOfferId" value="{{ $currentOffer['offerDocId'] }}">
                @endif
                @if(isset($selectedPlaceId))
                    <input type="hidden" name="placeId" value="{{ $selectedPlaceId }}">
                @endif

                <input type="submit" value="Scan" id="btn">

                @if($action == 'badBarcode')
                    <p class="alert alert-danger warningDIV" role="alert"
                    >@lang("Ошибка: отсканирован неверный штрих-код! Пожалуйста отсканируйте правильную полку!")</p>
                    @php
                        $action = '';
                    @endphp
                @endif
                <p id="error-message"
                   class="alert alert-danger warningDIV" role="alert" style="display: none;"
                >@lang("Ошибка: отсканирован неверный штрих-код! Пожалуйста отсканируйте правильную полку!")</p>
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

        @if(isset($currentOffer['offerId']))
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
        @else
        function validateForm() {
            let barcodeValue = document.getElementById('barcode').value;
            let errorMessage = document.getElementById('error-message');

            let regex = /^102\*(\d+)\*(\d+)$/; // Регулярное выражение для формата 102*YYY*ZZZ
            let match = barcodeValue.match(regex);

            if (!match) {
                errorMessage.textContent = '@lang("Ошибка: отсканирован неверный штрихкод! Пожалуйста отсканируте ближайшую полку!")';
                errorMessage.style.display = "block";
                document.getElementById('barcode').value = '';
                return false; // Отмена отправки формы
            }

            let yyy = parseInt(match[1]); // Получаем YYY
            let zzz = parseInt(match[2]); // Получаем ZZZ
            let expectedZZZ = 102 + yyy; // Вычисляем ожидаемую сумму

            if (zzz !== expectedZZZ) {
                errorMessage.textContent = '@lang('Ошибка: не верная контрольная сумма штрих-кода! Пожалуйста отсканируйте ближайшую полку еще раз!')';
                errorMessage.style.display = "block";
                document.getElementById('barcode').value = '';
                return false; // Отмена отправки формы
            }

            errorMessage.style.display = "none"; // Если всё ок, скрываем ошибку
            return true; // Разрешаем отправку
        }
        @endif

    </script>

 @if(count($arOffersList) == 0)
     <script>
         window.location.href = "{{ route('platform.terminal.soam.finish', [$soaId, $dbOrder->o_id]) }}";
     </script>
 @endif

    <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
        <div class="row g-0">
            <div style="margin: 10px 10px 20px 10px; text-align: center;">

                @if($action == '')
                    <div style="width: 95%; padding-right: 10px; padding-top: 5px; padding-bottom: 5px; font-size: 20px; background-color: #e8dcbb; border-radius: 10px;">@lang('Перейдите к выбранному месту хранения и отсканируйте полку:')</div>
                    <br>
                    <h4>@lang('Товары к подбору:')</h4>
                @endif
                @if($action == 'findOffers')
                    <div style="width: 95%; padding-right: 10px; padding-top: 5px; padding-bottom: 5px; font-size: 20px; background-color: #FFF3CD; border-radius: 10px;">@lang('Отсканируйте ШК следующего товара:')</div>
                    <br>

                    @include('Screens.Terminal.SOAM.ScanOfferSOAM')

                    <h4>@lang('Товары на этой полке:')</h4>
                @endif

                <table class="table table-striped" style="width: 95%;">
                    @foreach($arOffersList as $arOffer)
                        @if($action == '' || ($action == 'findOffers' && $arOffer['offerShow'] == 1))

                            <tr>
                                <td style="background-color: #CFE2FF;" class="offerNameTD"><b>{{ $arOffer['offerName'] }}</b>
                                    @if($arOffer['offerArt'] != '')
                                        ({{ $arOffer['offerArt'] }})
                                    @endif
                                </td>
                                <td style="text-align: center; color: #a30000; background-color: #CFF4FC;" class="offerNameTD"><b>
                                        <nobr>{{ $arOffer['offerQty'] }}</nobr>
                                    </b></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <table class="table table-striped">
                                        @foreach($arPlacesList[$arOffer['offerId']] as $arPlace)
                                            @php
                                                $bgcolor = ' background-color: #D1E7DD;';
                                                if ($arPlace['whcr_count'] < $arOffer['offerQty']) $bgcolor = ' background-color: #F8D7DA;';
                                            @endphp
                                            <tr>
                                                @if($arPlace['pl_room'] != '')
                                                    <td style="{{ $bgcolor }}">{{ $arPlace['pl_room'] }}<br><span style="font-size:10px; font-color: #AAAAAA;">{{ $arPlace['pl_id'] }}</span></td>
                                                @endif
                                                @if($arPlace['pl_floor'] != '')
                                                    <td style="{{ $bgcolor }}">{{ $arPlace['pl_floor'] }}</td>
                                                @endif
                                                @if($arPlace['pl_section'] != '')
                                                    <td style="{{ $bgcolor }}">{{ $arPlace['pl_section'] }}</td>
                                                @endif
                                                @if($arPlace['pl_row'] != '')
                                                    <td style="{{ $bgcolor }}">{{ $arPlace['pl_row'] }}</td>
                                                @endif
                                                @if($arPlace['pl_rack'] != '')
                                                    <td style="{{ $bgcolor }}">{{ $arPlace['pl_rack'] }}</td>
                                                @endif
                                                @if($arPlace['pl_shelf'] != '')
                                                    <td style="{{ $bgcolor }}">{{ $arPlace['pl_shelf'] }}</td>
                                                @endif
                                                <td style="{{ $bgcolor }}"><b
                                                            style="color: #0a870c;">{{ $arPlace['whcr_count'] }}</b>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            </div>
        </div>
    </div>
