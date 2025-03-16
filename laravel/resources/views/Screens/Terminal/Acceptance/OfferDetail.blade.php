@php use App\Services\CustomTranslator; @endphp
<style>
    /* Общий стиль для всех экранов */
    body, td {
        font-size: 13px;
    }

    H5 {
        font-size: 18px;
    }

    .countInput {
        font-size: 20px;
    }

    .photo {
        width: 150px;
    }

    .buttonPut {
        width: 160px;
        height: 50px;
    }

    .offPut {
        height: 40px;
    }

    /* Для экранов шире 768px (планшеты и выше) */
    @media (min-width: 0px) {
        body, td {
            font-size: 13px;
        }

        H5 {
            font-size: 18px;
        }

        .countInput {
            font-size: 20px;
            width: 175px;
        }

        .photo {
            width: 100px;
        }

        .buttonPut {
            width: 130px;
            height: 50px;
        }

        .termText {
            font-size: 16px;
            vertical-align: top;
        }
    }

    @media (min-width: 500px) {
        body, td {
            font-size: 16px;
        }

        H5 {
            font-size: 26px;
        }

        .countInput {
            font-size: 26px;
        }

        .photo {
            width: 250px;
        }

        .buttonPut {
            width: 260px;
            height: 70px;
        }

        .termText {
            font-size: 24px;
        }
    }

    /* Для экранов шире 768px (планшеты и выше) */
    @media (min-width: 760px) {
        body, td {
            font-size: 16px;
        }

        H5 {
            font-size: 32px;
        }

        .countInput {
            font-size: 38px;
        }

        .photo {
            width: 350px;
        }

        .buttonPut {
            width: 360px;
            height: 70px;
        }

        .offPut {
            height: 50px;
        }
    }

    /* Для экранов шире 1200px (настольные компьютеры) */
    @media (min-width: 1200px) {
        body, td {
            font-size: 18px;
        }

        H5 {
            font-size: 34px;
        }

        .countInput {
            font-size: 34px;
        }

        .photo {
            width: 550px;
        }

        .buttonPut {
            width: 400px;
            height: 70px;
        }

        .offPut {
            height: 60px;
        }
    }

    /* Для экранов шире 1200px (настольные компьютеры) */
    @media (min-width: 1500px) {
        body, td {
            font-size: 14px;
        }

        H5 {
            font-size: 36px;
        }

        .countInput {
            font-size: 36px;
        }

        .photo {
            width: 550px;
        }

        .buttonPut {
            width: 400px;
            height: 70px;
        }

        .offPut {
            height: 60px;
        }
    }
</style>
@isset($currentOffer['ao_id'])

    <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
        <div class="row g-0">
            <div style="margin: 10px 0px 10px 10px;">

                <h5>{{ $currentOffer['ao_name'] }}</h5>

                <table>
                    <tr>
                        <td style="text-align: center">
                            <img src="{{ $currentOffer['ao_img'] }}"
                                 border="1" class="photo">
                            <hr>
                            <button onclick="skipGoods()"
                                    class="btn btn-warning btn-block offPut">{{ CustomTranslator::get('ПРОПУСТИТЬ') }}</button>
                            <hr>
                            <button onclick="offGoods()"
                                    class="btn btn-outline-danger btn-block offPut">{{ CustomTranslator::get('НЕ НАШЕЛ') }}</button>

                            <script>
                                function skipGoods() {
                                    window.location.href = "?skip=1";
                                }

                                function offGoods() {
                                    var result = confirm("{{ CustomTranslator::get('Вы уверены что не нашли этот товар?') }}");

                                    if (result) {
                                        window.location.href = "?offerId={{ $currentOffer['ao_wh_offer_id'] }}&action=skip";
                                    }
                                }
                            </script>

                        </td>
                        <td style="vertical-align: top; padding-left: 15px;">
                            <div align="left"
                                 class="termText">{{ CustomTranslator::get('Артикул') }}: {{ $currentOffer['ao_article']  }}</div>

                            <hr>
                            <b style="color: #AAAAAA;"
                               class="termText">{{ CustomTranslator::get('Ожидается') }}: {{ round($currentOffer['ao_expected'], 2) }}</b><br>
                            <b style="color: #AAAAAA;"
                               class="termText">{{ CustomTranslator::get('Принято') }}: {{ round($currentOffer['ao_accepted'], 2)  }}</b><br>
                            @php
                                $restOfCount = $currentOffer['ao_expected'] - $currentOffer['ao_accepted'];
                            @endphp
                            <b><span
                                        onclick="document.getElementById('input_data').value='{{ $restOfCount }}'"
                                        class="termText">{{ CustomTranslator::get('Не принято') }}: {{ $restOfCount }}</span></b><br>
                            <hr>

                            <form action="" method="get"
                                  style="text-align: center; padding: 0px 0px 0px 0px; margin-top: 0px; margin-bottom: 0px;"
                                  id="goodsDetailSend">
                                <input type="hidden" name="offerWhId" value="{{ $currentOffer['ao_wh_offer_id'] }}">
                                <input type="hidden" name="offerId" value="{{ $currentOffer['ao_offer_id'] }}">
                                <input type="hidden" name="saveBarcode" value="{{ $saveBarcode }}">
                                <input type="hidden" name="currentTime" value="{{ time() }}">
                                <input type="text" name="scanCount" id="input_data" size="15" value=""
                                       placeholder="{{ CustomTranslator::get('Количество') }}"
                                       style="margin-bottom: 5px;" class="countInput"><br>

                                @if($settingExeptDate)
                                    <input type="text" name="scanExpDate" id="scanExpDate" size="15" value="@php
                                            if (isset($currentOffer['ao_expiration_date']) && (strlen($currentOffer['ao_expiration_date']) == 10 )) {
                                                $tDate = $currentOffer['ao_expiration_date'];
                                                echo substr($tDate, 0, 2).substr($tDate, 3, 2).substr($tDate, 8, 2);
                                            }
                                        @endphp"
                                           placeholder="{{ CustomTranslator::get('Срок (DDMMYY)') }}" style="margin-top: 10px;"><br>
                                    <input type="text" name="scanBatch" id="scanBatch" size="15" value="@php
                                            if (isset($currentOffer['ao_batch']))
                                                echo $currentOffer['ao_batch'];
                                    @endphp"
                                           placeholder="Batch"><br>

                                    <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                            const input = document.getElementById('scanExpDate');
                                            Inputmask("999999").mask(input);
                                        });
                                    </script>

                                @endif

                                <br>
                                <input type="submit" value="PUT" id="btn2" class="btn btn-success btn-block buttonPut">

                                <script>
                                    function checkData(event) {
                                        count = document.getElementById('input_data').value;
                                        if (count > 100000) {
                                            alert('{{ CustomTranslator::get('ОШИБКА! Склишком много товара!') }}');
                                            event.preventDefault();
                                        }
                                        if (count == 0 || count == '') {
                                            alert('{{ CustomTranslator::get('ОШИБКА! Пустая строка!') }}');
                                            event.preventDefault();
                                        }
                                    }

                                    document.getElementById('goodsDetailSend').addEventListener('submit', checkData);

                                    function setCount() {
                                        document.getElementById('input_data').value = document.getElementById('exeptId').innerText;
                                    }

                                    function handleKeyPressCount(event) {
                                        if (event.key === 'Enter') {
                                            document.getElementById('btn2').click();
                                        }
                                    }

                                    function handleKeyPressExpDate(event) {
                                        if (event.key === 'Enter') {
                                            document.getElementById('btn2').click();
                                        }
                                    }

                                    document.getElementById("barcode").disabled = true;
                                    document.getElementById("btn").disabled = true;

                                    function getDeviceType() {
                                        const userAgent = navigator.userAgent.toLowerCase();
                                        const isMobile = /mobile|iphone|ipad|ipod|android|blackberry|mini|windows\sce|palm/i.test(userAgent);

                                        if (isMobile) {
                                            return "mobile";
                                        } else {
                                            return "desktop";
                                        }
                                    }

                                </script>
                            </form>

                        </td>
                    </tr>
                </table>

            </div>
        </div>
    </div>

@endisset
