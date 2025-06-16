@php use App\Services\CustomTranslator; @endphp
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="padding: 20px 20px 20px 20px; padding: 20px;">
            <h4>{{ CustomTranslator::get('Cоздайте шаблон в формате эксел или html, который может содержать следующие поля (система подставит нужные параметры)') }}
                :</h4>
            <br>
            <b>{{ CustomTranslator::get('Демонстрация') }}:</b><br>
            <br>
            <img src="/img/importPrintDemo.png" style="width: 800px;"><br>
            <br>
            <b>{{ CustomTranslator::get('Доступные поля') }}:</b><br>
            <br>

            <table class="table table-striped">
                <tr>
                    <th style="text-align: center;"></th>
                    <th style="text-align: center;">{{ CustomTranslator::get('Имя поля') }}</th>
                    <th style="text-align: center">{{ CustomTranslator::get('Тип') }}</th>
                    <th style="text-align: center">{{ CustomTranslator::get('Пример') }}</th>
                    <th style="text-align: center">{{ CustomTranslator::get('Описание') }}</th>
                    <th></th>
                </tr>
                @foreach($printImportDescriptions as $desc)
                    @if(isset($desc['field']) && ($desc['field'] == 'title'))
                        <tr>
                            <td style="padding-left: 30px; background-color: #EEEEEE;" colspan="5"><H4>{{ $desc['name'] }}</H4></td>
                        </tr>
                    @else
                        <tr>
                            <td style="padding-left: 5px;">&nbsp;</td>
                            <td style="padding-left: 30px;"><nobr><b>{{ $desc['name'] }}</b></nobr></td>
                            <td style="padding-left: 10px; text-align: center;"><nobr>{{ CustomTranslator::get($desc['type']) }}</nobr></td>
                            <td style="padding-left: 10px; text-align: center;"><nobr>{{ CustomTranslator::get($desc['example']) }}</nobr></td>
                            <td style="padding-left: 10px;">{{ CustomTranslator::get($desc['description']) }}</td>
                        </tr>
                    @endif
                @endforeach
            </table>
            <hr>
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-info" onClick="window.location.href='/downloads/import_order_tmp.xlsx'">
                    {{ CustomTranslator::get('Скачать шаблон') }}
                </button>
            </div>
            <br>
            <b>{{ CustomTranslator::get('Правила именования шаблонных переменных') }}:</b><br>
            <br>
            <UL>
                <LI>{{ CustomTranslator::get('{var_name} - для строковых значений (номер заказа, дата и т.д.).') }}</LI>
                <LI>{{ CustomTranslator::get('[var_name] - массив (вывод строк).') }}</LI>
            </UL>
<br>
</div>
</div>
</div>