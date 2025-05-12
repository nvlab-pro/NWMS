@php use App\Services\CustomTranslator; @endphp
<style>
    .tableButton {
        border-bottom: 2px solid #999999;
        border-right: 2px solid #999999;
        border-top: 1px solid #DDDDDD;
        border-left: 1px solid #DDDDDD;
        width: 95%;
        text-align: left;
        padding: 15px 15px 15px 15px;
        font-size: 20px;
        background-color: #fff39e;
        margin-bottom: 10px;
    }

    .tableButtonText {
        font-size: 16px;
        border-top: 1px solid #DDDDDD;
        margin-bottom: 0px;
        margin-top: 5px;
        padding-top: 5px;
    }

    .tableButtonText2 {
        font-size: 11px;
    }
</style>
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="margin: 10px 0px 10px 10px;">

            @if (count($queuesList) > 0)
                @foreach($queuesList as $queue)

                    <button type="button" class="tableButton"
                            onClick="window.location.href='{{ route('platform.tables.marking.scan', $queue->sm_id) }}'">
                        <table>
                            <tr>
                                <td>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                                        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"
                                              style="color: #95a5a6;"/>
                                    </svg>
                                </td>
                                <td style="padding-left: 20px;">
                                    <span style="font-size: 16px">{{ CustomTranslator::get('Очередь упаковки') }} №: {{ $queue->sm_id }}</span><br>
                                    <b>{{ $queue->sm_name }}</b><br>
                                    <span style="font-size: 14px;"><b>{{ CustomTranslator::get('Служба доставки') }}:</b> {{ $queue->getDS->ds_name }}</span>
                                </td>
                            </tr>
                        </table>
                    </button><br>

                @endforeach
            @else

                <div class="alert alert-warning" role="alert">
                    <a href="{{ route('platform.whmanagement.marking-settings.index') }}">{{ CustomTranslator::get('Для начала упаковки создайте хотя-бы одну очередь!') }}</a>
                </div>

            @endif

        </div>
    </div>
</div>