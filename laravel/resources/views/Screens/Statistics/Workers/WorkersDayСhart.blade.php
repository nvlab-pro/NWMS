@php
    use App\Services\CustomTranslator;
@endphp
@if($currentDateIsDay == 0)
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="padding: 20px 20px 20px 20px;  margin: 10px 10px 10px 10px; display: table;">

            <h2>{{ CustomTranslator::get('Статистика товарооборота внутри дня') }}:</h2>

            <div class="scrollable-window">
                <table border="0" cellpadding=10 cellspacing=0>
                    <tr>
                        <td align="right"></td>
                        @for($hour = 1; $hour <= 24; $hour++)
                            @php

                                $bgcolor = 'background-color: #FFFFFF;';
                                $charColor = '#AAAAAA';

                                if ($maxHour == $hour)
                                    $charColor = '#009900';

                                if ($minHour == $hour)
                                    $charColor = '#DD0000';

                                echo '<td align="center" valign="bottom" style="border: 1px dotted #A0A0A0; font-size: 11px; line-height: 0; '.$bgcolor.'"><img src="/img/1x1.png" width="25" height="'.$arHours[$hour].'" style="background-color: '.$charColor.'; margin: 0; padding: 0;"></td>'."\n";

                            @endphp
                        @endfor
                    </tr>
                    <tr>
                        <td align="right" style="border: 1px dotted #A0A0A0; font-size: 11px;"><b>{{ CustomTranslator::get('Количество') }}</b></td>
                        @for($hour = 1; $hour <= 24; $hour++)
                            @php
                                $bgcolor = 'background-color: #FFFFFF;';
                                echo '<td align="center" valign="bottom" style="border: 1px dotted #A0A0A0; font-size: 11px; '.$bgcolor.'"><nobr><b>'.number_format($arHours[$hour], 0, ',', ' ').'</b></nobr></td>';
                            @endphp
                        @endfor
                    </tr>
                    <tr>
                        <td align="right" style="border: 1px dotted #A0A0A0; font-size: 11px;"><b>{{ CustomTranslator::get('Часы') }}</b></td>
                        @for($hour = 1; $hour <= 24; $hour++)
                            @php
                                $bgcolor = 'background-color: #FFFFFF;';
                                echo '<td align="center" valign="bottom" style="border: 1px dotted #A0A0A0; font-size: 11px; '.$bgcolor.'"><nobr><b>'.$hour.'</b></nobr></td>';
                            @endphp
                        @endfor
                    </tr>
                </table>
            </div>

        </div>
    </div>
</div>
@endif
