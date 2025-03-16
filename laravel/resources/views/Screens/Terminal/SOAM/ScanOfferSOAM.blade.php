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

<div style="text-align: center;" class="sizeBlock">

    <h2>{{ $currentOffer['offerName'] }}</h2>
    <br>
    <table>
        <tr>
            <td style="text-align: center">
                <img src="{{ $currentOffer['offerImg'] }}"
                     border="1" class="photoOffer">
            </td>
            <td style="vertical-align: top; padding-left: 15px;" class="textOffer">
                <b>{{ CustomTranslator::get('Арт.') }}:</b> {{ $currentOffer['offerArt'] }}<br>
                <br>
                <b>{{ CustomTranslator::get('Возьмите') }}:</b><br>
                <div class="offerCount"><b>{{ $currentOffer['offerQty'] }}</b></div>
                <br>
                {{ CustomTranslator::get('и отсканируйте штрих-код этого товара!') }}
            </td>
        </tr>
    </table>
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
            <td style="{{ $bgcolor }}"><b
                        style="color: #0a870c;">{{ $currentPlace['whcr_count'] }}</b></td>
        </tr>
    </table>

    <button type="button" onclick="skipGoods()" class="btn btn-warning btn-block">{{ CustomTranslator::get('ПРОПУСТИТЬ') }}</button>
    <script>
        function skipGoods() {
            window.location.href = "{{ route('platform.terminal.soam.order', [$soaId, $dbOrder->o_id]) }}";
        }
    </script>

</div>
<br>
<hr>
