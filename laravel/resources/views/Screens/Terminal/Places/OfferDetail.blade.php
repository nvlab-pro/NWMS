@isset($currentOffer['ao_id'])
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

        .div-info-select-offer {
            background-color: #F8D7DA;
            border-radius: 6px;
            box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.05);
        }

        .div-info-save-offer {
            background-color: #fff2aa;
            border-radius: 6px;
            box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.05);
        }
    </style>

    @if($currentOffer['ao_placed'] == 0 && $currentOffer['ao_accepted'] > 0 && $action != 'scanPlace')

        <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
            <div class="row g-0 div-info-save-offer">
                <div style="margin: 10px 0px 10px 10px;">

                    <h4>@lang('Укажите количество привязываемого товара:')</h4>

                </div>
            </div>
        </div>

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
                                        class="btn btn-warning btn-block offPut">@lang('ПРОПУСТИТЬ')</button>

                                <script>
                                    function skipGoods() {
                                        window.location.href = "?skip=1";
                                    }

                                    function offGoods() {
                                        var result = confirm("@lang('Вы уверены что не нашли этот товар?')");

                                        if (result) {
                                            window.location.href = "?offerId={{ $currentOffer['ao_wh_offer_id'] }}&action=skip";
                                        }
                                    }
                                </script>

                            </td>
                            <td style="vertical-align: top; padding-left: 15px;">
                                <div align="left"
                                     class="termText">@lang('Артикул: ') {{ $currentOffer['ao_article']  }}</div>

                                <hr>
                                <b><span
                                            class="termText">@lang('Не привязано:') {{ $currentOffer['ao_accepted'] }}</span></b><br>
                                <hr>

                                <form action="" method="get"
                                      style="text-align: center; padding: 0px 0px 0px 0px; margin-top: 0px; margin-bottom: 0px;"
                                      id="goodsDetailSend">
                                    <input type="hidden" name="action" value="selectPlace">
                                    <input type="hidden" name="offerWhId" value="{{ $currentOffer['ao_wh_offer_id'] }}">
                                    <input type="hidden" name="offerId" value="{{ $currentOffer['ao_offer_id'] }}">
                                    <input type="hidden" name="currentTime" value="{{ time() }}">
                                    <input type="text" name="scanCount" id="input_data" size="15" value="{{ $currentOffer['ao_accepted'] }}"
                                           placeholder="Количество"
                                           style="margin-bottom: 5px;" class="countInput"><br>

                                    <br>
                                    <input type="submit" value="PUT" id="btn2"
                                           class="btn btn-success btn-block buttonPut">

                                    <script>
                                        function checkData(event) {
                                            count = document.getElementById('input_data').value;
                                            if (count > 100000) {
                                                alert('@lang('ОШИБКА! Склишком много товара!')');
                                                event.preventDefault();
                                            }
                                            if (count == 0 || count == '') {
                                                alert('@lang('ОШИБКА! Пустая строка!')');
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

    @endif

@else

    <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
        <div class="row g-0 div-info-select-offer">
            <div style="margin: 10px 0px 10px 10px;">

                <h4>@lang('Выберите или отсканируйте товар для привязки:')</h4>

            </div>
        </div>
    </div>

@endisset


