@php use App\Services\CustomTranslator; @endphp

<script type="text/javascript" src="https://code.jquery.com/jquery-1.3.1.min.js"></script>

<script type="text/javascript">
    function PrintElem(elem) {
        Popup($(elem).html());
    }

    function Popup(data) {
        var mywindow = window.open('', 'Print Act', 'height=400,width=600');
        mywindow.document.write('<html><head><title>Print Barcodes</title>');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10
        mywindow.print();
        mywindow.close();
        return true;
    }
</script>
<style>
    .input-error {
        border: 2px solid red;
        background-color: #ffe5e5;
    }
</style>
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0" style="margin: 10px 10px 10px 10px; padding: 20px 10px 20px 10px;">

        <div class="d-flex justify-content-end align-items-center gap-2" style="padding-bottom: 10px;">
            <button type="button" class="btn btn-secondary" onclick="PrintElem('#printAct')">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-printer" viewBox="0 0 16 16">
                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                    <path
                            d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>
                </svg>
                &nbsp;
                {{ CustomTranslator::get('Напечатать лист подбора') }}
            </button>
        </div>
        <hr>

        <div>
            <h1>{{ CustomTranslator::get('Подбор товара для заказа') }} №: {{ $orderId }}</h1>
        </div>
        <H4>{{ CustomTranslator::get('Укажите места хранения с которых вы взяли товар для данного заказа и его количество:') }}</H4>
        <br>
        <br>

        <form method="POST" action="{{ route('platform.tables.packing.assembling.print', [$queueId, $tableId, $orderId, 1]) }}/saveDocument">

            <input type="hidden" name="queueId" value="{{ $queueId }}">
            <input type="hidden" name="tableId" value="{{ $tableId }}">
            <input type="hidden" name="orderId" value="{{ $orderId }}">

            @csrf

            <table border="1" width="100%" style="margin-bottom: 20px;">
                <tr>
                    <th class="tdPrint">№</th>
                    <th class="tdPrint">Фото</th>
                    <th class="tdPrint">Артикул</th>
                    <th class="tdPrint">Название</th>
                    <th class="tdPrint">Подобрать</th>
                    <th class="tdPrint">Подобрано</th>
                    <th class="tdPrint">Осталось</th>
                </tr>
                @php
                    $num = 0;
                @endphp
                @foreach($arOffersList as $offer)
                    @php
                        $num++;
                    @endphp
                    <tr>
                        <td class="tdPrint">{{ $num }}</td>
                        <td class="tdPrint"><img src="{{ $offer['of_img'] }}" width="85"></td>
                        <td class="tdPrint">{{ $offer['of_article'] }}</td>
                        <td class="tdPrint"><b style="font-size: 20px;">{{ $offer['of_name'] }}</b>
                            <table border="1" style="margin-top: 10px;" width="100%">
                                @foreach($offer['offerPlaces'] as $place)
                                    <tr>
                                        @if(isset($place['pl_room']))
                                            <td style="background-color: #fffbd8; border-bottom: 1px dotted #999999; font-size: 16px;">{{ $place['pl_room'] }}</td>
                                        @endif
                                        @if(isset($place['pl_floor']))
                                            <td style="background-color: #fffbd8; border-bottom: 1px dotted #999999; font-size: 16px;">{{ $place['pl_floor'] }}</td>
                                        @endif
                                        @if(isset($place['pl_section']))
                                            <td style="background-color: #fffbd8; border-bottom: 1px dotted #999999; font-size: 16px;">{{ $place['pl_section'] }}</td>
                                        @endif
                                        @if(isset($place['pl_row']))
                                            <td style="background-color: #fffbd8; border-bottom: 1px dotted #999999; font-size: 16px;">{{ $place['pl_row'] }}</td>
                                        @endif
                                        @if(isset($place['pl_rack']))
                                            <td style="background-color: #fffbd8; border-bottom: 1px dotted #999999; font-size: 16px;">{{ $place['pl_rack'] }}</td>
                                        @endif
                                        @if(isset($place['pl_shelf']))
                                            <td style="background-color: #fffbd8; border-bottom: 1px dotted #999999; font-size: 16px;">{{ $place['pl_shelf'] }}</td>
                                        @endif
                                        @if(isset($place['whcr_count']))
                                            <td style="background-color: #fffbd8; border-bottom: 1px dotted #999999; font-size: 16px;">
                                                <b>{{ CustomTranslator::get('Остаток') }}
                                                    : {{ $place['whcr_count'] }}</b></td>
                                        @endif
                                        <td style="background-color: #fffbd8; border-bottom: 1px dotted #999999; font-size: 16px;">
                                            <input
                                                    type="number"
                                                    min="0"
                                                    max="{{ $place['whcr_count'] }}"
                                                    data-max="{{ $place['whcr_count'] }}"
                                                    class="offer-input"
                                                    data-offer-id="{{ $offer['oo_offer_id'] }}"
                                                    name="offerCount[{{ $offer['oo_offer_id'] }}][{{ $place['pl_id'] }}]"
                                                    placeholder="{{ CustomTranslator::get('Укажите количество') }}"
                                                    value="{{ $place['picked_count'] }}"
                                                    style="width: 200px;">
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                        <td class="tdPrint">{{ $offer['oo_qty'] }}</td>
                        <td class="tdPrint picked" data-offer-id="{{ $offer['oo_offer_id'] }}">0</td>
                        <td class="tdPrint remaining" data-offer-id="{{ $offer['oo_offer_id'] }}" data-target-qty="{{ $offer['oo_qty'] }}">0</td>

                    </tr>
                @endforeach

            </table>

            <div class="d-flex justify-content-center align-items-center gap-2" style="padding-bottom: 10px;">
                <div id="save-container" style="margin-top: 20px; display: none; text-align: center;">
                    <button type="submit" class="btn btn-success" style="padding: 10px 40px; font-size: 18px;">
                        {{ CustomTranslator::get('СОХРАНИТЬ') }}
                    </button>
                </div>
            </div>
        </form>

        <div class="alert alert-info" role="alert" id="info-message" style="display: none; font-size: 24px;"></div>
        <div class="alert alert-danger" role="alert" id="error-message" style="display: none; font-size: 24px;"></div>




        <div id="printAct" style="display: none;">
            <style>
                .tdPrint {
                    text-align: center;
                    border: 1px solid #000000;
                    font-size: 18px;
                }
            </style>

            <h1>{{ CustomTranslator::get('Лист подбора для заказа') }} №: {{ $orderId }}</h1>

            <table border="1" width="100%">
                <tr>
                    <th class="tdPrint">№</th>
                    <th class="tdPrint">{{ CustomTranslator::get('Фото') }}</th>
                    <th class="tdPrint">{{ CustomTranslator::get('Артикул') }}</th>
                    <th class="tdPrint">{{ CustomTranslator::get('Название') }}</th>
                    <th class="tdPrint">{{ CustomTranslator::get('Количество') }}</th>
                </tr>
                @php
                    $num = 0;
                @endphp
                @foreach($arOffersList as $offer)
                    @php
                        $num++;
                    @endphp
                    <tr>
                        <td class="tdPrint">{{ $num }}</td>
                        <td class="tdPrint"><img src="{{ $offer['of_img'] }}" width="85"></td>
                        <td class="tdPrint">{{ $offer['of_article'] }}</td>
                        <td class="tdPrint"><b style="font-size: 20px;">{{ $offer['of_name'] }}</b>
                            <table border="1" style="margin-top: 10px;" width="100%">
                                @foreach($offer['offerPlaces'] as $place)
                                    <tr>
                                        @if(isset($place['pl_room']))
                                            <td style="background-color: #fffbd8; border-bottom: 1px dotted #999999; font-size: 16px;">{{ $place['pl_room'] }}</td>
                                        @endif
                                        @if(isset($place['pl_floor']))
                                            <td style="background-color: #fffbd8; border-bottom: 1px dotted #999999; font-size: 16px;">{{ $place['pl_floor'] }}</td>
                                        @endif
                                        @if(isset($place['pl_section']))
                                            <td style="background-color: #fffbd8; border-bottom: 1px dotted #999999; font-size: 16px;">{{ $place['pl_section'] }}</td>
                                        @endif
                                        @if(isset($place['pl_row']))
                                            <td style="background-color: #fffbd8; border-bottom: 1px dotted #999999; font-size: 16px;">{{ $place['pl_row'] }}</td>
                                        @endif
                                        @if(isset($place['pl_rack']))
                                            <td style="background-color: #fffbd8; border-bottom: 1px dotted #999999; font-size: 16px;">{{ $place['pl_rack'] }}</td>
                                        @endif
                                        @if(isset($place['pl_shelf']))
                                            <td style="background-color: #fffbd8; border-bottom: 1px dotted #999999; font-size: 16px;">{{ $place['pl_shelf'] }}</td>
                                        @endif
                                        @if(isset($place['whcr_count']))
                                            <td style="background-color: #fffbd8; border-bottom: 1px dotted #999999; font-size: 16px;">
                                                <b>{{ CustomTranslator::get('Остаток') }}
                                                    : {{ $place['whcr_count'] }}</b></td>
                                        @endif
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                        <td class="tdPrint">{{ $offer['oo_qty'] }}</td>
                    </tr>
                @endforeach

            </table>
        </div>

        <hr>

        <div style="text-align: center; font-size: 20px;">
            <a href="{{ route('platform.tables.packing.scan', [$queueId, $tableId, $orderId]) }}"> &#9668; Вернуться к упаковке</a>
        </div>


    </div>
</div>

<script>
    function initAssemblyScript() {
        const inputs = document.querySelectorAll('.offer-input');
        const saveContainer = document.getElementById('save-container');
        const errorMessage = document.getElementById('error-message');
        const infoMessage = document.getElementById('info-message');

        if (!inputs.length) return;

        function recalculateForOffer(offerId) {
            const relatedInputs = document.querySelectorAll(`.offer-input[data-offer-id="${offerId}"]`);
            let pickedTotal = 0;

            relatedInputs.forEach(inp => {
                const max = parseFloat(inp.dataset.max) || 0;
                let val = parseFloat(inp.value);

                if (val > max) {
                    inp.value = max;
                    val = max;
                    inp.classList.add('input-error');
                } else {
                    inp.classList.remove('input-error');
                }

                if (!isNaN(val)) {
                    pickedTotal += val;
                }
            });

            const pickedCell = document.querySelector(`.picked[data-offer-id="${offerId}"]`);
            if (pickedCell) pickedCell.textContent = pickedTotal;

            const remainingCell = document.querySelector(`.remaining[data-offer-id="${offerId}"]`);
            if (remainingCell) {
                const targetQty = parseFloat(remainingCell.dataset.targetQty) || 0;
                const remaining = targetQty - pickedTotal;

                if (remaining < 0) {
                    remainingCell.innerHTML = `<span style="color: red; font-weight: bold;">${remaining.toFixed(2)}</span>`;
                } else if (remaining === 0) {
                    remainingCell.innerHTML = `<span style="color: blue; font-weight: bold;">${remaining.toFixed(2)}</span>`;
                } else {
                    remainingCell.innerHTML = remaining.toFixed(2);
                }
            }
        }

        function evaluateFormState(isInitialLoad = false) {
            let hasPickedAnything = false;
            let hasNegativeRemaining = false;
            let hasRemainingGreaterThanZero = false;

            document.querySelectorAll('.remaining').forEach(cell => {
                const val = parseFloat(cell.innerText);
                if (val > 0) {
                    hasRemainingGreaterThanZero = true;
                    hasPickedAnything = true;
                }
                if (val < 0) {
                    hasNegativeRemaining = true;
                }
            });

            if (hasNegativeRemaining) {
                saveContainer.style.display = 'none';
                infoMessage.style.display = 'none';
                errorMessage.style.display = 'block';
                errorMessage.textContent = "Вы взяли слишком много товара!";
            } else if (!hasPickedAnything && hasRemainingGreaterThanZero) {
                saveContainer.style.display = 'none';
                infoMessage.style.display = 'block';
                errorMessage.style.display = 'none';
                infoMessage.textContent = "Пожалуйста укажите количество товара подобранно с полок, напротив этих полок";
            } else {
                saveContainer.style.display = 'block';
                infoMessage.style.display = 'none';
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';

                // ✅ Автопереход только при полной комплектации и первичной загрузке
                @if($action == 1)
                    if (isInitialLoad && !hasRemainingGreaterThanZero && !hasNegativeRemaining) {
                        setTimeout(() => {
                            window.location.href = "{{ route('platform.tables.packing.scan', [$queueId, $tableId, $orderId]) }}";
                        }, 500);
                    }
                @endif
            }
        }

        // Обработка ввода
        inputs.forEach(input => {
            input.addEventListener('input', function () {
                recalculateForOffer(this.dataset.offerId);
                evaluateFormState(false); // Без автоперехода
            });
        });

        // Первичный пересчёт
        const offerIds = new Set();
        inputs.forEach(input => offerIds.add(input.dataset.offerId));
        offerIds.forEach(recalculateForOffer);

        evaluateFormState(true); // С автопереходом
    }

    // При полной загрузке
    document.addEventListener('DOMContentLoaded', initAssemblyScript);

    // При переходе в Orchid (Turbo/SPA)
    document.addEventListener('turbo:load', initAssemblyScript);
</script>

<style>
    .input-error {
        border: 2px solid red;
        background-color: #ffe5e5;
    }
</style>
