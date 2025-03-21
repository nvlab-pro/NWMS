@php use App\Services\CustomTranslator; @endphp
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="padding: 20px 20px 20px 20px; padding: 20px;">
            <h4>{{ CustomTranslator::get('В вашем файле должны быть следующие заголовки полей (первая строка, последовательность не важна)') }}:</h4>
            <br>
            <table class="table table-striped">
                <tr>
                    <th style="text-align: center;">{{ CustomTranslator::get('Имя поля') }}</th>
                    <th style="text-align: center">{{ CustomTranslator::get('Тип') }}</th>
                    <th style="text-align: center">{{ CustomTranslator::get('Значение по умолчанию') }}</th>
                    <th style="text-align: center">{{ CustomTranslator::get('Возможные значения') }}</th>
                    <th style="text-align: center">{{ CustomTranslator::get('Описание') }}</th>
                    <th></th>
                </tr>
            @foreach($importDescriptions as $desc)
                <tr>
                    <td style="padding-left: 30px;"><b>{{ $desc['name'] }}</b></td>
                    <td style="padding-left: 10px; text-align: center;">{{ CustomTranslator::get($desc['type']) }}</td>
                    <td style="padding-left: 10px; text-align: center;">{{ CustomTranslator::get($desc['defaultValue']) }}</td>
                    <td style="padding-left: 10px;">{{ CustomTranslator::get($desc['description']) }}</td>
                </tr>
            @endforeach
            </table>
        </div>
    </div>
</div>