@php
    use App\Services\CustomTranslator;
@endphp
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="padding: 20px 20px 20px 20px; text-align: left;">

            <h3>{{ CustomTranslator::get('Статистика по работникам склада с ') }}: {{ $startDate }}
                до {{ $endDate }}</h3>

            <table class="table table-striped">
                <thead>
                <tr>
                    <th style="text-align: center;">{{ CustomTranslator::get('ФИО') }}</th>
                    <th style="text-align: center;">{{ CustomTranslator::get('Принято') }}</th>
                    <th style="text-align: center;">{{ CustomTranslator::get('Привязано') }}</th>
                    <th style="text-align: center;">{{ CustomTranslator::get('Собрано') }}</th>
                    <th style="text-align: center;">{{ CustomTranslator::get('Упаковано') }}</th>
                    <th style="text-align: center;">{{ CustomTranslator::get('Промаркировано') }}</th>
                    <th style="text-align: center;">{{ CustomTranslator::get('Итого') }}</th>
                </tr>
                </thead>
                <tbody>


                @foreach($arUsersList as $userId => $userName)
                    <tr>
                        <td style="text-align: center; cursor: pointer; transition: background-color 0.2s;"
                            onmouseover="this.style.backgroundColor='#f0f0f0';"
                            onmouseout="this.style.backgroundColor='';"
                            onClick="window.location.href='{{ route('platform.statistics.current.worker', $userId) }}?startDate={{ $startDate }}&endDate={{ $endDate }}&type=0';">
                            {{ $userName }}
                        </td>
                        <td style="text-align: center; cursor: pointer; transition: background-color 0.2s;"
                            onmouseover="this.style.backgroundColor='#f0f0f0';"
                            onmouseout="this.style.backgroundColor='';"
                            onClick="window.location.href='{{ route('platform.statistics.current.worker', $userId) }}?startDate={{ $startDate }}&endDate={{ $endDate }}&type=1';">
                            @if(isset($arUserStats[$userId][1]))
                                {{ $arUserStats[$userId][1] }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align: center; cursor: pointer; transition: background-color 0.2s;"
                            onmouseover="this.style.backgroundColor='#f0f0f0';"
                            onmouseout="this.style.backgroundColor='';"
                            onClick="window.location.href='{{ route('platform.statistics.current.worker', $userId) }}?startDate={{ $startDate }}&endDate={{ $endDate }}&type=2';">
                            @if(isset($arUserStats[$userId][2]))
                                {{ $arUserStats[$userId][2] }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align: center; cursor: pointer; transition: background-color 0.2s;"
                            onmouseover="this.style.backgroundColor='#f0f0f0';"
                            onmouseout="this.style.backgroundColor='';"
                            onClick="window.location.href='{{ route('platform.statistics.current.worker', $userId) }}?startDate={{ $startDate }}&endDate={{ $endDate }}&type=3';">
                            @if(isset($arUserStats[$userId][3]))
                                {{ $arUserStats[$userId][3] }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align: center; cursor: pointer; transition: background-color 0.2s;"
                            onmouseover="this.style.backgroundColor='#f0f0f0';"
                            onmouseout="this.style.backgroundColor='';"
                            onClick="window.location.href='{{ route('platform.statistics.current.worker', $userId) }}?startDate={{ $startDate }}&endDate={{ $endDate }}&type=4';">
                            @if(isset($arUserStats[$userId][4]))
                                {{ $arUserStats[$userId][4] }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align: center; cursor: pointer; transition: background-color 0.2s;"
                            onmouseover="this.style.backgroundColor='#f0f0f0';"
                            onmouseout="this.style.backgroundColor='';"
                            onClick="window.location.href='{{ route('platform.statistics.current.worker', $userId) }}?startDate={{ $startDate }}&endDate={{ $endDate }}&type=5;">
                            @if(isset($arUserStats[$userId][5]))
                                {{ $arUserStats[$userId][5] }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align: center; cursor: pointer; transition: background-color 0.2s;"
                            onmouseover="this.style.backgroundColor='#f0f0f0';"
                            onmouseout="this.style.backgroundColor='';"
                            onClick="window.location.href='{{ route('platform.statistics.current.worker', $userId) }}?startDate={{ $startDate }}&endDate={{ $endDate }}&type=0';">
                            @php
                                $userOperationSum = 0;
                                for($n = 1; $n <= 4; $n++)
                                    if (isset($arUserStats[$userId][$n])) $userOperationSum += $arUserStats[$userId][$n];
                            @endphp
                            {{ $userOperationSum }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>