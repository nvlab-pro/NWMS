@php use App\Services\CustomTranslator; @endphp
<style>
    .acceptButton {
        border-bottom: 2px solid #999999;
        border-right: 2px solid #999999;
        border-top: 1px solid #DDDDDD;
        border-left: 1px solid #DDDDDD;
        width: 95%; text-align: left;
        padding: 15px 15px 15px 15px;
        font-size: 20px;
        background-color: #D1E7DD;
        margin-bottom: 10px;
    }
    .acceptButtonText {
        font-size: 16px;
        border-top: 1px solid #DDDDDD;
        margin-bottom: 0px;
        margin-top: 5px;
        padding-top: 5px;
    }
    .acceptButtonText2 {
        font-size: 14px;
    }
</style>
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="margin: 10px 0px 10px 10px;">

            <h4>{{ CustomTranslator::get('Выберите очередь сборки') }}:</h4>

            @foreach($dbSettingsList as $dbQueue)

                    <button type="button" class="acceptButton"
                            onClick="window.location.href='{{ route('platform.terminal.soa.location', [$dbQueue->ssoa_id, 0]) }}'">
                        <b>{{ CustomTranslator::get('Очередь') }} №: {{ $dbQueue->ssoa_id }}</b><br>
                        <div class="acceptButtonText">{{ $dbQueue->ssoa_name }}<br>
                            <span class="acceptButtonText2">{{ CustomTranslator::get('Заказов к сборке') }}: {{ $dbQueue->ssoa_count_ready }}
                    </span>
                        </div>
                    </button><br>

            @endforeach

        </div>
    </div>
</div>