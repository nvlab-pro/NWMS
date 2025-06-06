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

@if($countAssembledOffers > 0)

    <div class="alert alert-warning text-center" role="alert" style="font-size: 20px;">
        {{ CustomTranslator::get('Товар не собран! Чтобы продолжить упаковку, нажмите на "Печать листа подбора" и укажите с каких мест вы взяли товар!') }}
    </div>

@else
    @if($printPalletLabel)
        @php

            $showCurrentBarcode = new DNS1D();
            $controlSum = $dbOrder->o_id + 101;
            $orderCode = '101' . '*' . $dbOrder->o_id . '*' . $controlSum . '*' . $currentPallet;
            $orderBarcode = $showCurrentBarcode->getBarcodeSVG($orderCode, 'C128', 1, 30, 'black', false);

            $orderLable = '<div align="center">'.$orderBarcode.'<br>'.$dbOrder->o_id.' (pl ' . $currentPallet . ')<br><span style="font-size: 10px;">'.$dbOrder->getWarehouse->wh_name.'</span></div>';

        @endphp

                <!-- Блок с содержимым для печати (изначально скрыт) -->
        <div id="printableArea">
            {!! $orderLable !!}
        </div>

        <script>
            document.addEventListener("turbo:load", function () {
                printDivContent('printableArea');
            });
        </script>

    @endif
    @if($printBoxLabel)
        @php

            $showCurrentBarcode = new DNS1D();
            $controlSum = $dbOrder->o_id + 101;
            $orderCode = '101' . '*' . $dbOrder->o_id . '*' . $controlSum . '*' . $currentPallet . '*' . $currentBox;
            $orderBarcode = $showCurrentBarcode->getBarcodeSVG($orderCode, 'C128', 1, 30, 'black', false);

            if ($dbOrder->o_ext_id != '')
                $orderCode3 = $dbOrder->o_ext_id;
            else
                $orderCode3 = '@#$';

            $orderExtBarcode = $showCurrentBarcode->getBarcodeSVG($orderCode3, 'C128', 1, 30, 'black', false);

            $orderLable = '<div align="center">'.$orderBarcode.'<br>'.$dbOrder->o_id.' (pl ' . $currentPallet . ' / box ' . $currentBox . ')<br><span style="font-size: 10px;">'.$dbOrder->getWarehouse->wh_name.'</span></div>';

        @endphp

                <!-- Блок с содержимым для печати (изначально скрыт) -->
        <div id="printableArea">
            {!! $orderLable !!}
        </div>

        <script>
            document.addEventListener("turbo:load", function () {
                printDivContent('printableArea');
            });
        </script>
    @endif

    @if(count($arOffersList) == 0 && count($arPackedOffersList) > 0)

        @php

            $showCurrentBarcode = new DNS1D();
            $controlSum = $dbOrder->o_id + 101;
            $orderCode = '101' . '*' . $dbOrder->o_id . '*' . $controlSum;
            $orderCode2 = '101' . ';' . $dbOrder->o_id . ';' . $controlSum;
            if ($dbOrder->o_ext_id != '')
                $orderCode3 = $dbOrder->o_ext_id;
            else
                $orderCode3 = '@#$';

            $orderBarcode = $showCurrentBarcode->getBarcodeSVG($orderCode, 'C128', 1, 30, 'black', false);
            $orderExtBarcode = $showCurrentBarcode->getBarcodeSVG($orderCode3, 'C128', 1, 30, 'black', false);

            if (isset($dbOrder->getWarehouse->wh_use_custom_label) && $dbOrder->getWarehouse->wh_use_custom_label == 1) {

                $orderLable = $dbOrder->getWarehouse->wh_custom_label;
                $orderLable = str_replace('{order_id}', $dbOrder->o_id, $orderLable);
                $orderLable = str_replace('{order_ext_id}', $dbOrder->o_ext_id, $orderLable);
                $orderLable = str_replace('{barcode_order_id}', $orderBarcode, $orderLable);
                $orderLable = str_replace('{barcode_order_ext_id}', $orderExtBarcode, $orderLable);

                $fio = '';
                if (isset($dbOrder->getContact->oc_first_name)) $fio .= $dbOrder->getContact->oc_first_name;
                if (isset($dbOrder->getContact->oc_last_name)) $fio .= ' ' . $dbOrder->getContact->oc_last_name;

                $orderLable = str_replace('{customer_fio}', $fio, $orderLable);
                $orderLable = str_replace('{wh_name}', $dbOrder->getWarehouse->wh_name, $orderLable);

            } else {

                $orderLable = '<div align="center">'.$orderBarcode.'<br>'.$dbOrder->o_id.'<br><span style="font-size: 10px;">'.$dbOrder->getWarehouse->wh_name.'</span></div>';

            }

        @endphp

                <!-- Блок с содержимым для печати (изначально скрыт) -->
        <div id="printableArea">
            {!! $orderLable !!}
        </div>

        <!-- Сообщение для пользователя -->
        <div class="alert alert-warning" role="alert"
             style="text-align: center; font-size: 30px; border: #3ab0c3 dotted 1px; border-radius: 10px;">
            {{ CustomTranslator::get('Заказ собран! Пожалуйста отсканируйте полученную этикетку!') }}
        </div>

        <script>
            document.addEventListener("turbo:load", function () {
                printDivContent('printableArea');
            });
        </script>

        <form action="{{ route('platform.tables.packing.select', [$queueId]) }}" method="GET"
              style="text-align: center; padding: 0px; margin: 0px;">
            @csrf
            <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
                <div style="text-align: center; padding: 10px; margin: 5px;">
                    {{ CustomTranslator::get('Отсканируйте этикетку для завершения упаковки заказа') }}:
                    <input type="text" name="barcode" id="barcode" size="30" autofocus onkeyup="handleKeyPress(event)">
                    <input type="hidden" name="cash" id="cash" value="{{ time() }}">
                    <input type="hidden" name="action" value="finishOrder">
                    <input type="hidden" name="orderId" value="{{ $dbOrder->o_id }}">
                    <input type="hidden" name="tableId" value="{{ $tableId }}">
                    <input type="submit" value="Scan" id="btn">
                    <div id="error-message" style="display: none; margin-top: 10px; font-size: 20px;"
                         class="alert alert-danger" role="alert">
                        {{ CustomTranslator::get('Это неверный код! Пожалуйста отсканируйте правильную этикетку для завершения заказа!') }}
                    </div>
                </div>
            </div>
        </form>

        <script>
            function validateForm(event) {
                let barcodeInput = document.getElementById("barcode").value;
                let validCode = "{{ $orderCode }}";
                let validCode2 = "{{ $orderCode2 }}";
                let validCode3 = "{{ $orderCode3 }}";

                if (barcodeInput === validCode || barcodeInput === validCode2 || barcodeInput === validCode3) {
                    document.forms[0].submit();
                } else {
                    event.preventDefault();
                    document.getElementById("error-message").style.display = "block";
                }
                document.getElementById("barcode").value = "";
                document.getElementById("barcode").focus();
            }

            document.getElementById("btn").addEventListener("click", function (event) {
                validateForm(event);
            });

        </script>

    @else

        <form action="" method="GET"
              style="text-align: center; padding: 0px 0px 0px 0px; margin-top: 0px; margin-bottom: 0px;">
            <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">

                <div style="text-align: center; padding: 10px 10px 10px 10px; margin-top: 5px; margin-bottom: 5px;">
                    @if($queuePackType == 2)
                        <b>{{ CustomTranslator::get('Отсканируйте честный знак для товара с ЧЗ') }}:</b>
                        <input type="text" name="barcode" id="barcode" size="50" autofocus onkeyup="handleKeyPress(event)">
                    @else
                        <b>{{ CustomTranslator::get('Отсканируйте товар') }}:</b>
                        <input type="text" name="barcode" id="barcode" size="30" autofocus onkeyup="handleKeyPress(event)">
                    @endif
                    <input type="hidden" name="cash" id="cash" value="{{ time() }}">
                    <input type="submit" value="Scan" id="btn">
                </div>

            </div>
        </form>

    @endif

    <script>
        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                document.getElementById('btn').click();
            }
        }

        document.getElementById("barcode").focus();
    </script>
@endif

<script>
    function printDivContent(divId) {
        // Берем содержимое дива
        const divContents = document.getElementById(divId).innerHTML;

        // Открываем новое окно (попап)
        const printWindow = window.open('', '', 'height=600,width=800');

        // Записываем туда HTML
        printWindow.document.write('<html><head><title>Print</title>');
        // Можно добавить базовые стили для печати
        printWindow.document.write('<style>body { font-family: Arial, sans-serif; text-align: center; } @page { margin: 0; } </style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write(divContents);
        printWindow.document.write('</body></html>');

        printWindow.document.close();

        // Ждем пока контент подгрузится, потом печатаем
        printWindow.onload = function () {
            printWindow.focus();
            printWindow.print();
            printWindow.close();

            // После закрытия окна ПЕЧАТИ вернуть фокус на #barcode
            setTimeout(() => {
                const barcodeInput = document.getElementById("barcode");
                if (barcodeInput) {
                    barcodeInput.focus();
                }
            }, 300); // небольшая задержка, чтобы DOM успел восстановиться
        };

        // На всякий случай фокусим #barcode при каждом turbo:load
        document.addEventListener("turbo:load", function () {
            const barcodeInput = document.getElementById("barcode");
            if (barcodeInput) {
                barcodeInput.focus();
            }
        });

        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                document.getElementById('btn').click();
            }
        }
    }
</script>