@php use App\Services\CustomTranslator; @endphp
<style>
    .tdMain {
        text-align: center;
        font-size: 20px;
    }
    .restRed {
        color: red;
    }
</style>

<div class="alert alert-info" role="alert" style="text-align: center; font-size: 30px; border: #3ab0c3 dotted 1px; border-radius: 10px;">
    @if($dbCurrentPlace->pl_type == 105)
        {{ CustomTranslator::get('Товар находится в ячейке') }}: <b style="color: red;">@if($dbCurrentPlace->pl_rack > 0){{ $dbCurrentPlace->pl_rack }} / @endif {{ $dbCurrentPlace->pl_shelf }}</b>
    @else
        {{ CustomTranslator::get('Товар находится на столе') }}: <b style="color: red;">{{ $dbCurrentPlace->pl_shelf }}</b>
    @endif
</div>

<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="margin: 10px 10px 10px 10px; padding: 10px 10px 10px 10px;">

            <table class="table" style="width: 99%">
                <tr>
                    <th class="tdMain">№</th>
                    <th class="tdMain">{{ CustomTranslator::get('Фото') }}</th>
                    <th class="tdMain">{{ CustomTranslator::get('Артикул') }}</th>
                    <th class="tdMain">{{ CustomTranslator::get('Название') }}</th>
                    <th class="tdMain">{{ CustomTranslator::get('Должно быть') }}</th>
                    <th class="tdMain">{{ CustomTranslator::get('Отсканировано') }}</th>
                    <th class="tdMain">{{ CustomTranslator::get('Осталось') }}</th>
                </tr>
                @php
                    $num = 0;
                @endphp
                @foreach($arOffersList as $offer)
                    @php
                        $num++;
                        $tClass = 'table-danger';
                        $tStyle = 'font-size: 20px;';

                        $rest = $offer['oo_qty'] - $offer['packed_qty'];

                        if ($offer['packed_qty'] > 0) $tClass = 'table-warning';

                    @endphp
                    <tr>
                        <td class="tdMain {{ $tClass }}" style="{{ $tStyle }}">{{ $num }}</td>
                        <td class="tdMain {{ $tClass }}" style="{{ $tStyle }}"><img src="{{ $offer['of_img'] }}" width="85"></td>
                        <td class="tdMain {{ $tClass }}" style="{{ $tStyle }}">{{ $offer['of_article'] }}</td>
                        <td class="tdMain {{ $tClass }}" style="text-align: left; {{ $tStyle }}">{{ $offer['of_name'] }}</td>
                        <td class="tdMain {{ $tClass }}" style="{{ $tStyle }}"><b>{{ $offer['oo_qty'] }}</b></td>
                        <td class="tdMain {{ $tClass }}" style="{{ $tStyle }}"><b>{{ $offer['packed_qty'] }}</b></td>
                        @if($rest > 0)
                            <td class="tdMain {{ $tClass }}" style="{{ $tStyle }} color: #E30000;"><b>{{ $rest }}</b></td>
                        @else
                            <td class="tdMain {{ $tClass }}" style="{{ $tStyle }}"><b>{{ $rest }}</b></td>
                        @endif
                    </tr>
                @endforeach

                <tr>
                    <td colspan="7">
                        <h2 align="center">{{ CustomTranslator::get('Собранные товары') }}</h2>
                    </td>
                </tr>

                <tr>
                    <th class="tdMain">№</th>
                    <th class="tdMain">{{ CustomTranslator::get('Фото') }}</th>
                    <th class="tdMain">{{ CustomTranslator::get('Артикул') }}</th>
                    <th class="tdMain">{{ CustomTranslator::get('Название') }}</th>
                    <th class="tdMain">{{ CustomTranslator::get('Должно быть') }}</th>
                    <th class="tdMain">{{ CustomTranslator::get('Отсканировано') }}</th>
                    <th class="tdMain">{{ CustomTranslator::get('Осталось') }}</th>
                </tr>
                @php
                    $num = 0;
                @endphp
                @foreach($arPackedOffersList as $offer)
                    @php
                        $num++;
                        $tClass = 'table-success';
                        $tStyle = 'font-size: 20px;';

                        $rest = $offer['oo_qty'] - $offer['packed_qty'];

                    @endphp
                    <tr>
                        <td class="tdMain {{ $tClass }}" style="{{ $tStyle }}">{{ $num }}</td>
                        <td class="tdMain {{ $tClass }}" style="{{ $tStyle }}"><img src="{{ $offer['of_img'] }}" width="85"></td>
                        <td class="tdMain {{ $tClass }}" style="{{ $tStyle }}">{{ $offer['of_article'] }}</td>
                        <td class="tdMain {{ $tClass }}" style="text-align: left; {{ $tStyle }}">{{ $offer['of_name'] }}</td>
                        <td class="tdMain {{ $tClass }}" style="{{ $tStyle }}"><b>{{ $offer['oo_qty'] }}</b></td>
                        <td class="tdMain {{ $tClass }}" style="{{ $tStyle }}"><b>{{ $offer['packed_qty'] }}</b></td>
                        @if($rest > 0)
                            <td class="tdMain {{ $tClass }}" style="{{ $tStyle }} color: #E30000;"><b>{{ $rest }}</b></td>
                        @else
                            <td class="tdMain {{ $tClass }}" style="{{ $tStyle }}"><b>{{ $rest }}</b></td>
                        @endif
                    </tr>
                @endforeach
            </table>

        </div>
    </div>
</div>