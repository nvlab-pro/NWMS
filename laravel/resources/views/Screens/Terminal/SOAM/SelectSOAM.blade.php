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
    .warningDIV {
        font-size: 15px;
        margin-top: 10px;
    }
</style>
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div style="padding: 10px 10px 10px 10px; text-align: center;">

            <h4>@lang('Выберите очередь сборки:')</h4>

            @php($countQueue = 0)

                @foreach($dbSettingsList as $dbQueue)

                    <button type="button" class="acceptButton"
                            onClick="window.location.href='{{ route('platform.terminal.soam.order', [$dbQueue->ssoa_id, 0]) }}'">
                        <b>@lang('Очередь №:') {{ $dbQueue->ssoa_id }}</b><br>
                        <div class="acceptButtonText">{{ $dbQueue->ssoa_name }}<br>
                            <span class="acceptButtonText2">@lang('Заказов к сборке:') {{ $dbQueue->ssoa_count_ready }}
                    </span>
                        </div>
                    </button><br>
                    @php($countQueue++)

                @endforeach

            @if($countQueue == 0)

            <div class="alert alert-danger warningDIV" role="alert">
                @lang("Нет ни очереди с заказами доступными для сборке!")</div>

            @endif

    </div>
</div>