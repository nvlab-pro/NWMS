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
        font-size: 14px;
    }
</style>

<form action="{{ route('platform.terminal.soa.get.offer', [$soaId, $dbOrder->o_id])  }}" method="GET"
      style="text-align: center; padding: 0px; margin-top: 0px; margin-bottom: 0px;">
    <input type="hidden" name="offerId" id="offerId" value="{{ $offerId }}">
    <input type="hidden" name="orderOfferId" id="orderOfferId" value="{{ $orderOfferId }}">
    <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
        <div class="row g-0">
            <div style="margin: 10px 10px 20px 10px; text-align: center;" class="sizeBlock">

                <h2>{{ $dbOffer->of_name }}</h2>
                <br>
                <table>
                    <tr>
                        <td style="text-align: center; vertical-align: top;">
                            <img src="{{ $dbOffer->of_img }}"
                                 border="1" class="photoOffer">
                        </td>
                        <td style="vertical-align: top; padding-left: 15px;" class="textOffer">
                            <b>@lang('Арт.:')</b> {{ $dbOffer->of_article }}<br>
                            <hr>
                            <b>@lang('Я взял'):</b><br>
                                <input type="text" name="count" id="count" value="{{ $dbOrderOffer->oo_qty }}" autofocus onkeyup="handleKeyPress(event)" class="sizeBlock" style="text-align: center;">
                            <hr>
                            <div class="alert alert-info warningDIV" role="alert">@lang('Внимательно пересчитайте взятый товар и укажите количество!')</div>
                        </td>
                    </tr>
                </table>
                <hr>
                <button type="submit" onclick="skipGoods()" class="btn btn-success btn-block">@lang('ВЗЯТЬ')</button>
                <script>
                    function skipGoods() {
                        window.location.href = "";
                    }
                </script>

            </div>
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

    }
</script>
