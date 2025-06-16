@php use App\Services\CustomTranslator; @endphp
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="padding: 20px 20px 20px 20px; padding: 20px;">
            <h4>{{ CustomTranslator::get('В вашем файле должны быть следующие заголовки полей (первая строка, последовательность не важна)') }}
                :</h4>
            <br>
            <table class="table table-striped">
                <tr>
                    <th style="text-align: center;">{{ CustomTranslator::get('Имя поля') }}</th>
                    <th style="text-align: center">{{ CustomTranslator::get('Тип') }}</th>
                    <th style="text-align: center">{{ CustomTranslator::get('Значение по умолчанию') }}</th>
                    <th style="text-align: center">{{ CustomTranslator::get('Описание') }}</th>
                    <th></th>
                </tr>
                @foreach($importDescriptions as $desc)
                    @if(isset($desc['field']) && ($desc['field'] == 'title'))
                        <tr>
                            <td style="padding-left: 30px; background-color: #D0D0D0;" colspan="5">
                                <H4>{{ $desc['name'] }}</H4></td>
                        </tr>
                    @else
                        <tr>
                            <td style="padding-left: 30px;">
                                <nobr><b>{{ $desc['name'] }}</b></nobr>
                            </td>
                            <td style="padding-left: 10px; text-align: center;">{{ CustomTranslator::get($desc['type']) }}</td>
                            <td style="padding-left: 10px; text-align: center;">{{ CustomTranslator::get($desc['defaultValue']) }}</td>
                            <td style="padding-left: 10px;">{{ CustomTranslator::get($desc['description']) }}</td>
                        </tr>
                    @endif
                @endforeach
            </table>

            <b style="color: red;">{{ CustomTranslator::get('Внимание') }}</b>:
            <ul>
                <li>
                    {{ CustomTranslator::get('Если в файле не будет указано поле') }}
                    <b>order_ext_id</b>
                    {{ CustomTranslator::get('то будет создан только один заказ и весь товар попадет в него.') }}
                </li>
                <li>
                    {{ CustomTranslator::get('Если заказ с аналогичным значением поля') }}
                    <b>order_ext_id</b>,
                    {{ CustomTranslator::get('у данного клиента, уже есть. То либо весь товар будет добавлен в него (если статус позволяет), либо данный заказ будет пропущен (если статус не позволяет).') }}
                </li>
            </ul>
            <hr>
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-info"
                        onClick="window.location.href='/downloads/import_order_tmp.xlsx'">
                    {{ CustomTranslator::get('Скачать шаблон') }}
                </button>
            </div>
        </div>
    </div>
</div>