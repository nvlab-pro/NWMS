<style>
    .th_title {
        text-align: center;
        border: 1px solid #000000;
        font-size: 12px;
        color: #FFFFFF;
        background-color: dimgrey;
    }
    .td_offer {
        text-align: center;
        border: 1px solid #000000;
        font-size: 12px;
        padding: 5px 5px 5px 5px;
    }
    .td_offer_recived_more {
        background-color: #CFE2FF;
    }
    .td_offer_expected_more {
        background-color: #FFF3CD;
    }
    .td_offer_equally {
        background-color: #D1E7DD;
    }
    .td_offer_not_accepted {
        background-color: #F8D7DA;
    }
</style>
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="margin: 10px 10px 10px 10px;">

            <table border="1" style="border: 1px solid #000000; " width="95%">
                <tr>
                    <th class="th_title">@lang('Артикул')</th>
                    <th class="th_title">@lang('Название')</th>
                    <th class="th_title">@lang('Ожид.')</th>
                    <th class="th_title">@lang('Принято')</th>
                </tr>

                {{-- Выводим список несобранных товаров (красный) --}}
                @foreach($offersList as $Offer)

                    @php
                        $tdClass = 'td_offer td_offer_recived_more';

                        if ($Offer->ao_expected > $Offer->ao_accepted)
                            $tdClass = 'td_offer td_offer_expected_more';

                        if ($Offer->ao_expected == $Offer->ao_accepted)
                            $tdClass = 'td_offer td_offer_equally';

                        if ($Offer->ao_accepted == 0)
                            $tdClass = 'td_offer td_offer_not_accepted';
                    @endphp

                    @if($Offer->ao_accepted == 0)
                        <tr>
                            <td class="{{ $tdClass }}" onClick="location.href='?offerId={{ $Offer->ao_wh_offer_id }}'">{{ $Offer->ao_article }}</td>
                            <td class="{{ $tdClass }}" style="text-align: left;" onClick="location.href='?offerId={{ $Offer->ao_wh_offer_id }}'">{{ $Offer->ao_name }}
                                @php

                                    if ($Offer->ao_expiration_date != NULL && $Offer->ao_expiration_date != '0000-00-00') {
                                        echo '<div style="border-top: 1px dotted #000000;">Exept. date: '.date('d.m.Y', strtotime($Offer->ao_expiration_date)).'</div>';
                                    }

                                @endphp
                            </td>
                            <td class="{{ $tdClass }}" onClick="location.href='?offerId={{ $Offer->ao_wh_offer_id }}'">{{ $Offer->ao_expected }}</td>
                            <td class="{{ $tdClass }}" onClick="location.href='?offerId={{ $Offer->ao_wh_offer_id }}'">{{ $Offer->ao_accepted }}</td>
                        </tr>
                    @endif

                @endforeach

                {{-- Выводим список собраных но не до конца товаров (розовый) --}}
                @foreach($offersList as $Offer)

                    @php
                        $tdClass = 'td_offer td_offer_recived_more';

                        if ($Offer->ao_expected > $Offer->ao_accepted)
                            $tdClass = 'td_offer td_offer_expected_more';

                        if ($Offer->ao_expected == $Offer->ao_accepted)
                            $tdClass = 'td_offer td_offer_equally';

                        if ($Offer->ao_accepted == 0)
                            $tdClass = 'td_offer td_offer_not_accepted';
                    @endphp

                    @if($Offer->ao_accepted > 0 && $Offer->ao_expected > $Offer->ao_accepted)
                        <tr>
                            <td class="{{ $tdClass }}" onClick="location.href='?offerId={{ $Offer->ao_wh_offer_id }}'">{{ $Offer->ao_article }}</td>
                            <td class="{{ $tdClass }}" style="text-align: left;" onClick="location.href='?offerId={{ $Offer->ao_wh_offer_id }}'">{{ $Offer->ao_name }}
                                @php

                                    if ($Offer->ao_expiration_date != NULL && $Offer->ao_expiration_date != '0000-00-00') {
                                        echo '<div style="border-top: 1px dotted #000000;">Exept. date: '.date('d.m.Y', strtotime($Offer->ao_expiration_date)).'</div>';
                                    }

                                @endphp
                            </td>
                            <td class="{{ $tdClass }}" onClick="location.href='?offerId={{ $Offer->ao_wh_offer_id }}'">{{ $Offer->ao_expected }}</td>
                            <td class="{{ $tdClass }}" onClick="location.href='?offerId={{ $Offer->ao_wh_offer_id }}'">{{ $Offer->ao_accepted }}</td>
                        </tr>
                    @endif

                @endforeach

                {{-- Выводим список лишних собраных (синий) --}}
                @foreach($offersList as $Offer)

                    @php
                        $tdClass = 'td_offer td_offer_recived_more';

                        if ($Offer->ao_expected > $Offer->ao_accepted)
                            $tdClass = 'td_offer td_offer_expected_more';

                        if ($Offer->ao_expected == $Offer->ao_accepted)
                            $tdClass = 'td_offer td_offer_equally';

                        if ($Offer->ao_accepted == 0)
                            $tdClass = 'td_offer td_offer_not_accepted';
                    @endphp

                    @if($Offer->ao_accepted > 0 && $Offer->ao_expected < $Offer->ao_accepted)
                        <tr>
                            <td class="{{ $tdClass }}" onClick="location.href='?offerId={{ $Offer->ao_wh_offer_id }}'">{{ $Offer->ao_article }}</td>
                            <td class="{{ $tdClass }}" style="text-align: left;" onClick="location.href='?offerId={{ $Offer->ao_wh_offer_id }}'">{{ $Offer->ao_name }}
                                @php

                                    if ($Offer->ao_expiration_date != NULL && $Offer->ao_expiration_date != '0000-00-00') {
                                        echo '<div style="border-top: 1px dotted #000000;">Exept. date: '.date('d.m.Y', strtotime($Offer->ao_expiration_date)).'</div>';
                                    }

                                @endphp
                            </td>
                            <td class="{{ $tdClass }}" onClick="location.href='?offerId={{ $Offer->ao_wh_offer_id }}'">{{ $Offer->ao_expected }}</td>
                            <td class="{{ $tdClass }}" onClick="location.href='?offerId={{ $Offer->ao_wh_offer_id }}'">{{ $Offer->ao_accepted }}</td>
                        </tr>
                    @endif

                @endforeach

                {{-- Выводим список полносьтю собранных (зеленый) --}}
                @foreach($offersList as $Offer)

                    @php
                        $tdClass = 'td_offer td_offer_recived_more';

                        if ($Offer->ao_expected > $Offer->ao_accepted)
                            $tdClass = 'td_offer td_offer_expected_more';

                        if ($Offer->ao_expected == $Offer->ao_accepted)
                            $tdClass = 'td_offer td_offer_equally';

                        if ($Offer->ao_accepted == 0)
                            $tdClass = 'td_offer td_offer_not_accepted';
                    @endphp

                    @if($Offer->ao_accepted > 0 && $Offer->ao_expected == $Offer->ao_accepted)
                        <tr>
                            <td class="{{ $tdClass }}" onClick="location.href='?offerId={{ $Offer->ao_wh_offer_id }}'">{{ $Offer->ao_article }}</td>
                            <td class="{{ $tdClass }}" style="text-align: left;" onClick="location.href='?offerId={{ $Offer->ao_wh_offer_id }}'">{{ $Offer->ao_name }}
                                @php

                                    if ($Offer->ao_expiration_date != NULL && $Offer->ao_expiration_date != '0000-00-00') {
                                        echo '<div style="border-top: 1px dotted #000000;">Exept. date: '.date('d.m.Y', strtotime($Offer->ao_expiration_date)).'</div>';
                                    }

                                @endphp
                            </td>
                            <td class="{{ $tdClass }}" onClick="location.href='?offerId={{ $Offer->ao_wh_offer_id }}'">{{ $Offer->ao_expected }}</td>
                            <td class="{{ $tdClass }}" onClick="location.href='?offerId={{ $Offer->ao_wh_offer_id }}'">{{ $Offer->ao_accepted }}</td>
                        </tr>
                    @endif

                @endforeach

            </table>

        </div>
    </div>
</div>
<div style="text-align: center;">
    <button style="font-size: 26px; background-color: #3eb058;" onclick="confirmAction()"> &#10004; @lang('Приемка заверешна!')</button>

    <script>
        function confirmAction() {
            // Появляется окно подтверждения
            var result = confirm("Вы уверены что хотите закрыть накладную?");

            // Если пользователь подтвердил (нажал "ОК")
            if (result) {
                // Переход по ссылке
//                window.location.href = "{{ route('platform.terminal.acceptance.select') }}?action=close&docId={{ $docId }}";
            }
        }
    </script>
</div>
<hr>
<div style="text-align: center;">
    <a href="{{ route('platform.terminal.acceptance.select') }}"> &#9668; @lang('Вернуться к списку')</a>
</div>
