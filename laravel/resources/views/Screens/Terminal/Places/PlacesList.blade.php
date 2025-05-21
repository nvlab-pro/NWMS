@php use App\Services\CustomTranslator; @endphp
@if(count($arRests) > 0)
    <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
        <div style="text-align: center; padding: 10px 10px 10px 10px; margin-top: 5px; margin-bottom: 5px;">
            <table class="table table-striped">
                <tr>
                    <th>{{ CustomTranslator::get('Место') }}</th>
                    <th>{{ CustomTranslator::get('Остаток') }}</th>
                    <th>{{ CustomTranslator::get('Срок') }}</th>
                </tr>
            @foreach($arRests['arRests'] as $Place)
                <tr>
                    <td style="font-size: 18px;">{{ $Place['placeName'] }}</td>
                    <td style="font-size: 18px;">{{ $Place['count'] }}</td>
                    <td style="font-size: 14px;">@php
                        if ($Place['production_date'] !== null) echo $Place['production_date'] . '<br>';
                        if ($Place['expiration_date'] !== null) echo $Place['expiration_date'] . '<br>';
                        if ($Place['batch'] !== null) echo $Place['batch'] . '<br>';
                    @endphp</td>
                </tr>
            @endforeach
            </table>
        </div>
    </div>
@endif
