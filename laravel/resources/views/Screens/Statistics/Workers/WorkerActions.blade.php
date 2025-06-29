@php
    use App\Services\CustomTranslator;
    use App\WhPlaces\WhPlaces;
    use Carbon\Carbon;
@endphp

<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="padding: 20px 20px 20px 20px; text-align: left;">

            <table class="table table-striped">
                <thead>
                <tr>
                    <th style="text-align: center;">{{ CustomTranslator::get('Дата') }}</th>
                    <th style="text-align: center;">{{ CustomTranslator::get('Время') }}</th>
                    <th style="text-align: center;">{{ CustomTranslator::get('Затрачено времени') }}</th>
                    <th style="text-align: center;">{{ CustomTranslator::get('Действие') }}</th>
                    <th style="text-align: center;">{{ CustomTranslator::get('Кол-во товара') }}</th>
                    <th style="text-align: center;">{{ CustomTranslator::get('Сумма товара') }}</th>
                    <th style="text-align: center;">{{ CustomTranslator::get('Место') }}</th>
                    <th style="text-align: center;">{{ CustomTranslator::get('ШК') }}</th>
                    <th>{{ CustomTranslator::get('Товар') }}</th>
                </tr>
                </thead>
                <tbody>

                @php
                    $previousTime = null;
                    $actionTime = 0;
                    $offerSum = 0;
                @endphp

                @foreach($dbActionsList as $dbAction)
                    @php
                        $actionTime = Carbon::parse($previousTime)->diffInSeconds(Carbon::parse($dbAction->ua_time_start));
                        $offerSum += $dbAction->ua_quantity;
                    @endphp
                    <tr>
                        <td style="text-align: center;"><nobr>{{ Carbon::parse($dbAction->ua_time_start)->format('d.m.Y') }}</nobr></td>
                        <td style="text-align: center;"><nobr>{{ Carbon::parse($dbAction->ua_time_start)->format('H:i:s') }}</nobr></td>
                        <td style="text-align: center;
                            @if ($actionTime > 3600)
                                background-color: #FF0000;
                            @elseif ($actionTime > 600)
                                background-color: #ff7c3f;
                            @elseif ($actionTime > 300)
                                background-color: #ffc268;
                            @elseif ($actionTime > 60)
                                background-color: #d6ff68;
                            @else
                                background-color: #7aff68;
                            @endif
                        "><nobr>
                            @if ($previousTime)
                                {{ Carbon::parse($dbAction->ua_time_start)->diff(Carbon::parse($previousTime))->format('%H:%I:%S') }}
                            @else
                                —
                        @endif
                            </nobr></td>
                        <td style="text-align: center; background-color: {{ $dbAction->actionType->lat_bgcolor }}; color: {{ $dbAction->actionType->lat_color }};">
                            <b><nobr>
                                @if($dbAction->ua_lat_id == 1)
                                    {{ CustomTranslator::get('Приемка товара') }}
                                @endif
                                @if($dbAction->ua_lat_id == 2)
                                    {{ CustomTranslator::get('Привязка товара') }}
                                @endif
                                @if($dbAction->ua_lat_id == 3)
                                    {{ CustomTranslator::get('Подбор товара') }}
                                @endif
                                @if($dbAction->ua_lat_id == 4)
                                    {{ CustomTranslator::get('Упаковка товара') }}
                                @endif
                                @if($dbAction->ua_lat_id == 5)
                                    {{ CustomTranslator::get('Маркировка товара') }}
                                @endif

                            </b></nobr></td>
                        <td style="text-align: center;">{{ $dbAction->ua_quantity }}</td>
                        <td style="text-align: center;">{{ $offerSum }}</td>
                        <td style="text-align: center;"><nobr>
                            @if(isset($dbAction->place->pl_id))
                                @php

                                    $str = WhPlaces::getPlaceStr($dbAction->place);
                                    echo $str;

                                @endphp
                            @else
                                —
                            @endif
                            </nobr></td>
                        <td style="text-align: center;">

                            @if(isset($dbAction->ua_barcode))
                                {{ $dbAction->ua_barcode }}
                            @else
                                —
                            @endif
                        </td>
                        <td style="text-align: left;">
                            @if(isset($dbAction->offer->of_id))
                                {{ $dbAction->offer->of_name }}
                            @else
                                —
                            @endif
                        </td>
                    </tr>

                    @php
                        $previousTime = $dbAction->ua_time_start;
                    @endphp
                @endforeach

                </tbody>
            </table>

        </div>
    </div>
</div>