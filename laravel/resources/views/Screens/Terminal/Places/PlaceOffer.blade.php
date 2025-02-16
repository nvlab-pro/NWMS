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

    .scanCount {
        font-size: 40px;
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

    .div-info-scan-place {
        background-color: #5ed078;
        border-radius: 6px;
        box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.05);
    }
</style>
@isset($currentOffer['ao_id'])

    @if($currentOffer['ao_placed'] == 0 && $currentOffer['ao_accepted'] > 0 && $action == 'scanPlace')

        <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
            <div class="row g-0 div-info-scan-place">
                <div style="margin: 10px 0px 10px 10px;">

                    <h4>@lang('Отсканируйте место хранения:')</h4>

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
                                <b><span class="termText">@lang('Привязываем:')</span>
                                    <br><br>
                                    <div align="center"><h1 class="scanCount">{{ $scanCount }}</h1></div>
                                </b><br>
                                <hr>

                            </td>
                        </tr>
                    </table>

                </div>
            </div>
        </div>

    @endif

@endisset


