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
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="margin: 10px 10px 10px 10px; padding: 10px 10px 10px 10px;">

            <table class="table table-striped" style="width: 99%">
                <tr>
                    <th class="tdMain">№</th>
                    <th class="tdMain">{{ CustomTranslator::get('Фото') }}</th>
                    <th class="tdMain">{{ CustomTranslator::get('Артикул') }}</th>
                    <th class="tdMain">{{ CustomTranslator::get('Название') }}</th>
                    @if($queueStartPlaceType == 102)
                        <th class="tdMain">{{ CustomTranslator::get('Собрано') }}</th>
                    @else
                        <th class="tdMain">{{ CustomTranslator::get('Место') }}</th>
                    @endif
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
                        <td class="tdMain {{ $tClass }}" style="{{ $tStyle }}">
                            @if($offer['of_datamatrix'] == 1)
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-qr-code-scan" viewBox="0 0 16 16">
                                    <path d="M0 .5A.5.5 0 0 1 .5 0h3a.5.5 0 0 1 0 1H1v2.5a.5.5 0 0 1-1 0zm12 0a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0V1h-2.5a.5.5 0 0 1-.5-.5M.5 12a.5.5 0 0 1 .5.5V15h2.5a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1 0-1H15v-2.5a.5.5 0 0 1 .5-.5M4 4h1v1H4z" style="color: blue;"/>
                                    <path d="M7 2H2v5h5zM3 3h3v3H3zm2 8H4v1h1z" style="color: blue;"/>
                                    <path d="M7 9H2v5h5zm-4 1h3v3H3zm8-6h1v1h-1z" style="color: blue;"/>
                                    <path d="M9 2h5v5H9zm1 1v3h3V3zM8 8v2h1v1H8v1h2v-2h1v2h1v-1h2v-1h-3V8zm2 2H9V9h1zm4 2h-1v1h-2v1h3zm-4 2v-1H8v1z" style="color: blue;"/>
                                    <path d="M12 9h2V8h-2z" style="color: blue;"/>
                                </svg><br>
                            @endif
                            {{ $offer['of_article'] }}</td>
                        <td class="tdMain {{ $tClass }}" style="text-align: left; {{ $tStyle }}">{{ $offer['of_name'] }}
                        </td>
                        @if($queueStartPlaceType == 102)
                            @if($offer['accepted_count'] == $offer['oo_qty'] )
                                <td class="tdMain {{ $tClass }}" style="{{ $tStyle }}; font-size: 30px; color: green;"><b>{{ $offer['accepted_count'] }}</b></td>
                            @else
                                <td class="tdMain {{ $tClass }}" style="{{ $tStyle }}; font-size: 30px; color: red;"><b>{{ $offer['accepted_count'] }}</b></td>
                            @endif
                        @else
                            <td class="tdMain {{ $tClass }}" style="{{ $tStyle }}"><b>{{ $offer['oo_qty'] }}</b></td>
                        @endif
                        <td class="tdMain {{ $tClass }}" style="font-size: 30px;"><b>{{ $offer['oo_qty'] }}</b></td>
                        <td class="tdMain {{ $tClass }}" style="font-size: 30px;"><b>{{ $offer['packed_qty'] }}</b></td>
                        @if($rest > 0)
                            <td class="tdMain {{ $tClass }}" style="font-size: 30px; color: #E30000;"><b>{{ $rest }}</b></td>
                        @else
                            <td class="tdMain {{ $tClass }}" style="font-size: 30px;"><b>{{ $rest }}</b></td>
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