@php
    use App\Services\CustomTranslator;
@endphp
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="margin: 20px 20px 20px 20px; text-align: left;">

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
                    <th style="text-align: center;">{{ CustomTranslator::get('Итого') }}</th>
                </tr>
                </thead>
                <tbody>


                @foreach($arUsersList as $userId => $userName)
                    <tr>
                        <td style="text-align: center;">{{ $userName }}</td>
                        <td style="text-align: center;">
                            @if(isset($arUserStats[$userId][1]))
                                {{ $arUserStats[$userId][1] }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if(isset($arUserStats[$userId][2]))
                                {{ $arUserStats[$userId][2] }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if(isset($arUserStats[$userId][3]))
                                {{ $arUserStats[$userId][3] }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if(isset($arUserStats[$userId][4]))
                                {{ $arUserStats[$userId][4] }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="text-align: center;">
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