@php
    use App\Services\CustomTranslator;
@endphp
<style>
    TD {
        FONT-SIZE: 10pt;
        FONT-FAMILY: arial;
        padding: 5px 5px 5px 5px;
    }
</style>
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="padding: 20px 20px 20px 20px;  margin: 10px 10px 10px 10px; display: table;">

            <h2>{{ CustomTranslator::get('Статистика товарооборота склада') }}:</h2>

            <div class="scrollable-window">
                <table border="0" cellpadding=10 cellspacing=0>
                    <tr>
                        <td align="right"></td>
                        @for($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                if ($day < 10) $day = '0'.$day;

                                $week = date("w", mktime(0,0,0,$currentMonth, $day, $currentYear));
                                $bgcolor = 'background-color: #FFFFFF;';
                                if ($week == 6) $bgcolor = 'background-color: #ffe3df;';
                                if ($week == 0) $bgcolor = 'background-color: #ffe3df; border-right: 2px solid #000000;';
                                $charColor = '#AAAAAA';

                                if ($currentDateIsDay == 0 && $currentDay == $day)
                                    $charColor = '#009900';

                                echo '<td align="center" valign="bottom" style="border: 1px dotted #A0A0A0; font-size: 11px; line-height: 0; '.$bgcolor.'"><img src="/img/1x1.png" width="25" height="'.$arDaysCount[$day].'" style="background-color: '.$charColor.'; margin: 0; padding: 0;"></td>'."\n";

                            @endphp
                        @endfor
                    </tr>
                    <tr>
                        <td align="right" style="border: 1px dotted #A0A0A0; font-size: 11px;"><b>{{ CustomTranslator::get('Часы') }}</b></td>
                        @for($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                if ($day < 10) $day = '0'.$day;

                                $week = date("w", mktime(0,0,0,$currentMonth, $day, $currentYear));
                                $bgcolor = 'background-color: #FFFFFF;';
                                if ($week == 6) $bgcolor = 'background-color: #ffe3df;';
                                if ($week == 0) $bgcolor = 'background-color: #ffe3df; border-right: 2px solid #000000;';

                                echo '<td align="center" valign="bottom" style="border: 1px dotted #A0A0A0; font-size: 11px; '.$bgcolor.'"><nobr><b>'.number_format($arDaysStats[$day], 0, ',', ' ').'</b></nobr></td>';

                            @endphp
                        @endfor
                    </tr>
                    <tr>
                        <td align="right" style="border: 1px dotted #A0A0A0; font-size: 11px;">{{ CustomTranslator::get('Число') }}</td>
                        @for($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                if ($day < 10) $day = '0'.$day;

                                $week = date("w", mktime(0,0,0,$currentMonth, $day, $currentYear));
                                $bgcolor = 'background-color: #FFFFFF;';
                                if ($week == 6) $bgcolor = 'background-color: #ffe3df;';
                                if ($week == 0) $bgcolor = 'background-color: #ffe3df; border-right: 2px solid #000000;';

                                $textColor = '';

                                echo '<td align="center" valign="bottom" style="border: 1px dotted #A0A0A0; font-size: 11px; '.$bgcolor.'"><b'.$textColor.'>'.$day.'</b></td>';
                            @endphp
                        @endfor
                    </tr>
                    <tr>
                        <td align="right" style="border: 1px dotted #A0A0A0; font-size: 11px;"><b>{{ CustomTranslator::get('Д.нед.') }}</b></td>
                        @for($day = 1; $day <= $daysInMonth; $day++)
                            @php

                                if ($day < 10) $day = '0'.$day;

                                $week = date("w", mktime(0,0,0,$currentMonth, $day, $currentYear));

                                $str_week = '';
                                if ($week == 0) $str_week = CustomTranslator::get('Вск');
                                if ($week == 1) $str_week = CustomTranslator::get('Пн');
                                if ($week == 2) $str_week = CustomTranslator::get('Вт');
                                if ($week == 3) $str_week = CustomTranslator::get('Ср');
                                if ($week == 4) $str_week = CustomTranslator::get('Чт');
                                if ($week == 5) $str_week = CustomTranslator::get('Пт');
                                if ($week == 6) $str_week = CustomTranslator::get('Суб');

                                $bgcolor = 'background-color: #FFFFFF;';
                                if ($week == 6) $bgcolor = 'background-color: #ffe3df;';
                                if ($week == 0) $bgcolor = 'background-color: #ffe3df; border-right: 2px solid #000000;';

                                $textColor = '';

                                echo '<td align="center" style="border: 1px dotted #A0A0A0; font-size: 11px; '.$bgcolor.'"><b'.$textColor.'>'.$str_week.'</b></td>';
                            @endphp

                        @endfor
                    </tr>
                </table>
            </div>

        </div>
    </div>
</div>
