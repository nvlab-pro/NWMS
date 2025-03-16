@php use App\Services\CustomTranslator; @endphp
@if($action == 'cancelOrder')
    <style>
        .button-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .warningDIV {
            font-size: 16px;
            margin-top: 10px;
        }
    </style>
    <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
        <div style="padding: 10px 10px 10px 10px; text-align: center;">

            <div class="alert alert-danger warningDIV" role="alert">
                {{ CustomTranslator::get('Заказ был привязан к месту для отмененных заказов!') }}<br><br>
                {{ CustomTranslator::get('Не забудьте разнести оставшиеся товары по местам хранения!') }}<br>
            </div>
            <hr>
            <div class="button-container">
                <button type="button" class="btn btn-primary"
                        onClick="location.href='{{ route('platform.terminal.soam.order', [$soaId, 0]) }}'">
                    {{ CustomTranslator::get('Перейти к сборке следующего') }}
                </button>
            </div>
            <hr>
            <img src="/img/assembly_cancel.jpg" width="250" style="border-radius: 10px;">
            <br><br>
        </div>
    </div>

@endif