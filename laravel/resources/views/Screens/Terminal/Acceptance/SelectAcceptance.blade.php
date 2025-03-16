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
        font-size: 11px;
    }
</style>
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="margin: 10px 0px 10px 10px;">

            @foreach($dbAcceptList as $Acceptance)

                @php
                    $bgColor = $Acceptance->getAccStatus->las_bgcolor;
                    $textColor = $Acceptance->getAccStatus->las_color;
                @endphp

                <button type="button" class="acceptButton"
                        style="background-color: {{ $bgColor }}; color: {{ $textColor }}"
                        onClick="window.location.href='{{ route('platform.terminal.acceptance.scan', $Acceptance->acc_id) }}'">
                    <b>{{ CustomTranslator::get('Накладная') }} №: {{ $Acceptance->acc_id }}</b><br>
                    <div class="acceptButtonText">{{ $Acceptance->getWarehouse->wh_name }}
                    <span class="acceptButtonText2">{{ CustomTranslator::get('Ожидается') }}: {{ $Acceptance->acc_count_expected }} / {{ CustomTranslator::get('Принято') }}: {{ $Acceptance->acc_count_accepted }} / {{ CustomTranslator::get('Размещено') }}: {{ $Acceptance->acc_count_placed }}
                    </span>
                    </div>
                </button><br>

            @endforeach

        </div>
    </div>
</div>