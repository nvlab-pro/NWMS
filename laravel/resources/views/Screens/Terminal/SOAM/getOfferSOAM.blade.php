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
        font-size: 14px;
    }
</style>

<form action="{{ route('platform.terminal.soam.order', [$soaId, $dbOrder->o_id]) }}" method="GET"
      style="text-align: center; padding: 0px; margin-top: 0px; margin-bottom: 0px;">
    <input type="hidden" name="offerId" id="offerId" value="{{ $offerId }}">
    <input type="hidden" name="orderOfferId" id="orderOfferId" value="{{ $dbOrderOffer->oo_offer_id }}">
    <input type="hidden" name="currentPlace" id="currentPlace" value="{{ $currentPlace['pl_id'] }}">
    <input type="hidden" name="action" value="saveOffer">
    <input type="hidden" name="cash" value="{{ time() }}">
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
                            <b>{{ CustomTranslator::get('Арт.') }}:</b> {{ $dbOffer->of_article }}<br>
                            <hr>
                            <b>{{ CustomTranslator::get('Всего нужно этого товара') }}: {{ $offerRest }}</b><br>
                            <hr>
                            <b>{{ CustomTranslator::get('Я взял') }}:</b><br>
                            <input type="text" name="count" id="count" value="{{ $maxCount > $offerRest ? $offerRest : $maxCount }}"
                                   autofocus onkeyup="handleDecimalInput(this, {{ $maxCount > $offerRest ? $offerRest : $maxCount }});"
                                   class="sizeBlock" style="text-align: center;">

                            <script>
                                function handleDecimalInput(input, max) {
                                    // Разрешаем только цифры и одну точку
                                    input.value = input.value.replace(/[^0-9.]/g, '');

                                    // Убираем лишние точки
                                    let parts = input.value.split('.');
                                    if (parts.length > 2) {
                                        input.value = parts[0] + '.' + parts.slice(1).join('');
                                    }

                                    // Проверяем число на превышение максимума
                                    if (parseFloat(input.value) > max) {
                                        input.value = max;
                                    }
                                }
                            </script>

                        </td>
                    </tr>
                </table>
                @if($maxCount < $offerRest)
                    <hr>
                    <div class="alert alert-danger"
                         role="alert">{{ CustomTranslator::get('Внимание: на этой полке товара меньше, чем нужно для заказа! Возьмите только эту часть.') }}</div>
                @endif
                <hr>
                <button type="submit" class="btn btn-success btn-block">{{ CustomTranslator::get('ВЗЯТЬ') }}</button>
                <hr>
                <div class="alert alert-info warningDIV"
                     role="alert">{{ CustomTranslator::get('Внимательно пересчитайте взятый товар и укажите количество!') }}</div>
                <hr>
                <button type="button" onclick="skipGoods()" class="btn btn-warning btn-block">{{ CustomTranslator::get('ПРОПУСТИТЬ') }}</button>
                <script>
                    function skipGoods() {
                        window.location.href = "{{ route('platform.terminal.soam.order', [$soaId, $dbOrder->o_id]) }}";
                    }
                </script>

            </div>
        </div>
    </div>
</form>

<hr>
<h4>{{ CustomTranslator::get('Товар расположен на месте') }}:</h4>

@php
    $bgcolor = 'border: 1px solid #000000; background-color: #FFF3CD;';
@endphp
<table class="table" style="border: 1px solid #000000;">
    <tr>
        @if($currentPlace['pl_room'] != '')
            <td style="{{ $bgcolor }}">{{ $currentPlace['pl_room'] }}</td>
        @endif
        @if($currentPlace['pl_floor'] != '')
            <td style="{{ $bgcolor }}">{{ $currentPlace['pl_floor'] }}</td>
        @endif
        @if($currentPlace['pl_section'] != '')
            <td style="{{ $bgcolor }}">{{ $currentPlace['pl_section'] }}</td>
        @endif
        @if($currentPlace['pl_row'] != '')
            <td style="{{ $bgcolor }}">{{ $currentPlace['pl_row'] }}</td>
        @endif
        @if($currentPlace['pl_rack'] != '')
            <td style="{{ $bgcolor }}">{{ $currentPlace['pl_rack'] }}</td>
        @endif
        @if($currentPlace['pl_shelf'] != '')
            <td style="{{ $bgcolor }}">{{ $currentPlace['pl_shelf'] }}</td>
        @endif
    </tr>
</table>

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
