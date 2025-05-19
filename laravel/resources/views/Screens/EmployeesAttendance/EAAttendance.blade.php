@php
    use Carbon\Carbon;
    use App\Services\CustomTranslator;
@endphp
<style>
    .circle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        font-size: 11px;
        background-color: green;
        color: white;
        border-radius: 50%;
        font-weight: bold;
    }
    .circleSkip {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        background-color: #A30000;
        color: white;
        border-radius: 50%;
        font-weight: bold;
    }
    .circleSickLeave {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        font-weight: bold;
        color: white;
    }

    .circleSickLeave::before,
    .circleSickLeave::after {
        content: "";
        position: absolute;
        background-color: red;
    }

    .circleSickLeave::before {
        width: 30px; /* Горизонтальная полоса */
        height: 12px; /* Толщина креста */
    }

    .circleSickLeave::after {
        width: 12px; /* Вертикальная полоса */
        height: 30px; /* Длина креста */
    }
    /* Фиксация верхней строки (заголовок с датами) */
    thead tr th {
        position: sticky;
        top: 0;
        z-index: 3;
        background: #fff;
    }
    /* Фиксация первого столбца */
    tbody tr td:first-child,
    thead tr th:first-child {
        position: sticky;
        left: 0;
        z-index: 2;
        background: white;
    }
    .marginTD {
        padding: 5px 5px 5px 5px;
        margin: 0px 0px 0px 0px;
        text-align: center;
        align-content: center;
    }

</style>
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="padding: 20px 20px 20px 10px;  margin: 10px 10px 10px 0px; display: table;">

            <h2>{{ CustomTranslator::get('Статистика посещаемости склада') }}:</h2>

            <div class="scrollable-window">
            <table class="">
                <thead>
                <tr>
                    <th style="text-align: center;"></th>
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            if ($day < 10) $day = "0" . $day;
                                    $week = date("w", mktime(0,0,0,$currentMonth, $day, $currentYear));
                                    $bgcolor = 'background-color: #FFFFFF;';
                                    if ($week == 6) $bgcolor = 'background-color: #ffbfb7;';
                                    if ($week == 0) $bgcolor = 'background-color: #ffbfb7; border-right: 2px solid #000000;';
                        @endphp
                            <th style="text-align: center; {{ $bgcolor }}" class="marginTD"><b><nobr>{{ $day }}</nobr></b></th>
                    @endfor
                    <th style="text-align: center; background-color: #61d1e8;" class="marginTD">{{ CustomTranslator::get('В будни') }}:</th>
                    <th style="text-align: center; background-color: #ff9389;" class="marginTD">{{ CustomTranslator::get('В выходные') }}:</th>
                    <th style="text-align: center; background-color: #ff8379;" class="marginTD">{{ CustomTranslator::get('Вск') }}:</th>
                    <th style="text-align: center; background-color: #9de0c0;" class="marginTD">{{ CustomTranslator::get('В среднем') }}:</th>
                    <th style="text-align: center; background-color: #A30000; color: #FFFFFF;" class="marginTD">{{ CustomTranslator::get('Прогулы') }}:</th>
                    <th style="text-align: center; background-color: #d88900; color: #FFFFFF;" class="marginTD">{{ CustomTranslator::get('Отгулы') }}:</th>
                    <th style="text-align: center; background-color: #0000FF; color: #FFFFFF;" class="marginTD">{{ CustomTranslator::get('Отпуски') }}:</th>
                    <th style="text-align: center; background-color: red; color: #FFFFFF;" class="marginTD">{{ CustomTranslator::get('Больничные') }}:</th>
                </tr>
                </thead>
                <tbody>
                @foreach($arUsersName as $userId => $userName)
                    @php
                        $lostDays = 0;
                        $freeDays = 0;
                        $holyDays = 0;
                        $halthyDays = 0;
                        $workDays = 0;
                        $weekendDays = 0;
                        $weekendDaysCount = 0;
                    @endphp
                    <tr>
                        <td><nobr><b>{!! str_replace(' ', '</nobr> <nobr>', $userName)  !!}</b></nobr></td>
                        @php $sum = 0; @endphp
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            if ($day < 10) $day = "0" . $day;
                                    $week = date("w", mktime(0,0,0,$currentMonth, $day, $currentYear));
                                    $bgcolor = 'background-color: #FFFFFF;';
                                    if ($week == 6) $bgcolor = 'background-color: #ffe3df;';
                                    if ($week == 0) $bgcolor = 'background-color: #ffe3df; border-right: 2px solid #000000;';
                        @endphp
                            @if(isset($arUsersRests[$userId][$day]))
                                @if($arUsersRests[$userId][$day] == 0)
                                    <td style="{{ $bgcolor }}"><div class="circleSkip"><nobr>П</nobr></div></td>
                                    @php
                                        $lostDays++;
                                    @endphp
                                @endif
                                @if($arUsersRests[$userId][$day] == 1)
                                    <td style="{{ $bgcolor }}" class="marginTD">
                                        <div class="circleSkip" style="background-color: orange;" title="{{ CustomTranslator::get('отпросился') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-grin-fill" viewBox="0 0 16 16">
                                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16M6.488 7c-.23-.598-.661-1-1.155-1-.493 0-.924.402-1.155 1A2.8 2.8 0 0 1 4 6c0-1.105.597-2 1.333-2 .737 0 1.334.895 1.334 2 0 .364-.065.706-.179 1m5.334 0c-.23-.598-.662-1-1.155-1-.494 0-.925.402-1.155 1a2.8 2.8 0 0 1-.179-1c0-1.105.597-2 1.334-2C11.403 4 12 4.895 12 6c0 .364-.065.706-.178 1M2.696 8.756a.48.48 0 0 1 .382-.118C4.348 8.786 6.448 9 8 9c1.553 0 3.653-.214 4.922-.362a.48.48 0 0 1 .383.118.3.3 0 0 1 .096.29c-.09.47-.242.921-.445 1.342-.263.035-.576.075-.929.115A37 37 0 0 1 8 10.75c-1.475 0-2.934-.123-4.027-.247-.353-.04-.666-.08-.93-.115A5.5 5.5 0 0 1 2.6 9.045a.3.3 0 0 1 .097-.29ZM8 13.5a5.49 5.49 0 0 1-4.256-2.017l.116.014c1.115.126 2.615.253 4.14.253s3.025-.127 4.14-.253l.117-.014A5.49 5.49 0 0 1 8 13.5"/>
                                            </svg>
                                        </div>
                                    </td>
                                        @php
                                            $freeDays++;
                                        @endphp
                                @endif
                                @if($arUsersRests[$userId][$day] == 2)
                                    <td style="{{ $bgcolor }}" class="marginTD"><div class="circleSkip" style="background-color: blue;" title="{{ CustomTranslator::get('отпуск') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-luggage-fill" viewBox="0 0 16 16">
                                                <path d="M2 1.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V5h.5A1.5 1.5 0 0 1 8 6.5V7H7v-.5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .5.5H4v1H2.5v.25a.75.75 0 0 1-1.5 0v-.335A1.5 1.5 0 0 1 0 13.5v-7A1.5 1.5 0 0 1 1.5 5H2zM3 5h2V2H3z"/>
                                                <path d="M2.5 7a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-1 0v-5a.5.5 0 0 1 .5-.5m10 1v-.5A1.5 1.5 0 0 0 11 6h-1a1.5 1.5 0 0 0-1.5 1.5V8H8v8h5V8zM10 7h1a.5.5 0 0 1 .5.5V8h-2v-.5A.5.5 0 0 1 10 7M5 9.5A1.5 1.5 0 0 1 6.5 8H7v8h-.5A1.5 1.5 0 0 1 5 14.5zm9 6.5V8h.5A1.5 1.5 0 0 1 16 9.5v5a1.5 1.5 0 0 1-1.5 1.5z"/>
                                            </svg>
                                        </div></td>
                                        @php
                                            $holyDays++;
                                        @endphp
                                @endif
                                @if($arUsersRests[$userId][$day] == 3)
                                    <td style="{{ $bgcolor }}" class="marginTD"><div class="circleSickLeave"><nobr>Б</nobr></div></td>
                                        @php
                                            $halthyDays++;
                                        @endphp
                                @endif
                            @else
                                @if(isset($arUsersHours[$day][$userId]))
                                    @php
                                        $sum += $arUsersHours[$day][$userId];
                                        if ($week == 0 || $week == 6) {
                                            $weekendDays += $arUsersHours[$day][$userId];
                                            $weekendDaysCount++;
                                        }
                                        $workDays++;
                                    @endphp
                                    <td style="{{ $bgcolor }};" class="marginTD"><div class="circle"><nobr>{{ $arUsersHours[$day][$userId] }}</nobr></div></td>
                                @else
                                    @if($week != 0 && $week != 6)
                                        @php
                                            $currentDate = Carbon::create($currentYear, $currentMonth, $day);
                                            $today = Carbon::now();
                                        @endphp
                                        @if($currentDate->lt($today))
                                            <td style="{{ $bgcolor }}" class="marginTD"><div class="circleSkip"><nobr>П</nobr></div></td>
                                        @else
                                            <td style="{{ $bgcolor }}" class="marginTD">-</td>
                                        @endif
                                        @php
                                            $lostDays++;
                                        @endphp
                                    @else
                                        <td style="{{ $bgcolor }}" class="marginTD">-</td>
                                    @endif
                            @endif
                        @endif
                    @endfor
                        <td  style="text-align: center; background-color: #CFF4FC;" class="marginTD"><nobr><b>{{ $sum-$weekendDays  }}</b></nobr></td>
                        <td  style="text-align: center; background-color: #FFBFB7;" class="marginTD"><nobr><b>{{ $weekendDays  }}</b></nobr></td>
                        <td  style="text-align: center; background-color: #FFAFA7;" class="marginTD"><nobr><b>{{ $weekendDaysCount  }}</b></nobr></td>
                        @if($workDays > 0)
                            <td  style="text-align: center; background-color: #D1E7DD;" class="marginTD"><nobr><b>{{ round($sum/$workDays, 2) }}</b></nobr></td>
                            @else
                            <td  style="text-align: center; background-color: #D1E7DD;" class="marginTD"><nobr><b>0</b></nobr></td>
                        @endif
                        <td  style="text-align: center; background-color: #e6a1a1; color: #FFFFFF;" class="marginTD"><nobr><b>{{ $lostDays  }}</b></nobr></td>
                        <td  style="text-align: center; background-color: #f2c16e;" class="marginTD"><nobr><b>{{ $freeDays  }}</b></nobr></td>
                        <td  style="text-align: center; background-color: #a4a4ff;" class="marginTD"><nobr><b>{{ $holyDays  }}</b></nobr></td>
                        <td  style="text-align: center; background-color: #ffb3b3;" class="marginTD"><nobr><b>{{ $halthyDays  }}</b></nobr></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            </div>

        </div>
    </div>
</div>
