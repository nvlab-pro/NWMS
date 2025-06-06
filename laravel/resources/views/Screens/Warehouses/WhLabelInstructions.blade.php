@php use App\Services\CustomTranslator; @endphp
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="padding: 20px 20px 20px 20px; padding: 20px;">
            <h4>{{ CustomTranslator::get('При создании этикетки используйте следующие теги') }}:</h4>
            <br>
            <table class="table table-striped">
                <tr>
                    <th style="text-align: center;">{{ CustomTranslator::get('Имя поля') }}</th>
                    <th style="text-align: center">{{ CustomTranslator::get('Тип') }}</th>
                    <th style="text-align: center">{{ CustomTranslator::get('Описание') }}</th>
                    <th></th>
                </tr>
                <tr>
                    <td style="padding-left: 30px;"><b>{order_id}</b></td>
                    <td style="padding-left: 10px; text-align: center;">{{ CustomTranslator::get('число') }}</td>
                    <td style="padding-left: 10px;">{{ CustomTranslator::get(' - Номер заказа') }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 30px;"><b>{order_ext_id}</b></td>
                    <td style="padding-left: 10px; text-align: center;">{{ CustomTranslator::get('строка') }}</td>
                    <td style="padding-left: 10px;">{{ CustomTranslator::get(' - Номер заказа клиента') }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 30px;"><b>{barcode_order_id}</b></td>
                    <td style="padding-left: 10px; text-align: center;">{{ CustomTranslator::get('штрих-код') }}</td>
                    <td style="padding-left: 10px;">{{ CustomTranslator::get(' - Штрих-код в котором закодирован номер заказа') }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 30px;"><b>{barcode_order_ext_id}</b></td>
                    <td style="padding-left: 10px; text-align: center;">{{ CustomTranslator::get('штрих-код') }}</td>
                    <td style="padding-left: 10px;">{{ CustomTranslator::get(' - Штрих-код в котором закодирован номер заказа клиента') }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 30px;"><b>{customer_fio}</b></td>
                    <td style="padding-left: 10px; text-align: center;">{{ CustomTranslator::get('строка') }}</td>
                    <td style="padding-left: 10px;">{{ CustomTranslator::get(' - ФИО клиента') }}</td>
                </tr>
                <tr>
                    <td style="padding-left: 30px;"><b>{wh_name}</b></td>
                    <td style="padding-left: 10px; text-align: center;">{{ CustomTranslator::get('строка') }}</td>
                    <td style="padding-left: 10px;">{{ CustomTranslator::get(' - Название склада клиента') }}</td>
                </tr>
            </table>
            <hr>
        </div>
    </div>
</div>