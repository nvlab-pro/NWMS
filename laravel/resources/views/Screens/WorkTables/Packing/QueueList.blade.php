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
        background-color: #99caff;
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

            @foreach($queuesList as $queue)

                <button type="button" class="tableButton"
                        onClick="window.location.href='{{ route('platform.tables.packing.select', $queue->spp_id) }}'">
                    <table>
                        <tr>
                            <td>
                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-collection" viewBox="0 0 16 16">
                                    <path d="M2.5 3.5a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1zm2-2a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1zM0 13a1.5 1.5 0 0 0 1.5 1.5h13A1.5 1.5 0 0 0 16 13V6a1.5 1.5 0 0 0-1.5-1.5h-13A1.5 1.5 0 0 0 0 6zm1.5.5A.5.5 0 0 1 1 13V6a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5z" style="color: #95a5a6;"/>
                                    <path d="M4 17s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" style="color: #5e5d63;"/>
                                </svg>
                            </td>
                            <td style="padding-left: 20px;">
                                <span style="font-size: 16px">@lang('Очередь упаковки №:') {{ $queue->spp_id }}</span><br>
                                <b>{{ $queue->spp_name }}</b>
                            </td>
                        </tr>
                    </table>
                </button><br>

            @endforeach

        </div>
    </div>
</div>