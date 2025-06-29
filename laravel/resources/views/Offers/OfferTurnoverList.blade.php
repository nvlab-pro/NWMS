@php use App\Services\CustomTranslator; @endphp
<style>
    .turnoverTD {
        text-align: center; !
    }
    .place-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px; /* Add some space between the place info and the arrow */
        width: 100%;
        text-align: center;
    }
    .place-box{
        border: 1px solid #999999;
        padding: 5px;
        text-align: center;
    }

    .place-box span {
        color: #999999;
        font-size: 10px;
        display: block; /* Ensure the type name is on a new line */
        white-space: nowrap;
    }
</style>
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="padding: 20px 20px 20px 20px;">

            <table class="table table-hover table-striped-columns">
                <thead>
                <tr>
                    <th class="table-secondary turnoverTD">№</th>
                    <th class="table-secondary turnoverTD">{{ CustomTranslator::get('Резерв') }}</th>
                    <th class="table-secondary turnoverTD">{{ CustomTranslator::get('Дата') }}</th>
                    <th class="table-secondary turnoverTD">{{ CustomTranslator::get('Тип') }}</th>
                    <th class="table-secondary turnoverTD">{{ CustomTranslator::get('№ документа') }}</th>
                    <th class="table-secondary turnoverTD">{{ CustomTranslator::get('Статус') }}</th>
                    @if($dbWh->wh_set_production_date == 1)
                        <th class="table-secondary turnoverTD">{{ CustomTranslator::get('Дата производства') }}</th>
                    @endif
                    @if($dbWh->wh_set_expiration_date == 1)
                        <th class="table-secondary turnoverTD">{{ CustomTranslator::get('Срок годности') }}</th>
                    @endif
                    @if($dbWh->wh_set_batch == 1)
                        <th class="table-secondary turnoverTD">{{ CustomTranslator::get('Батч') }}</th>
                    @endif
                    <th class="table-secondary turnoverTD">{{ CustomTranslator::get('Место') }}</th>
                    <th class="table-secondary turnoverTD">{{ CustomTranslator::get('Количество') }}</th>
                    <th class="table-secondary turnoverTD">{{ CustomTranslator::get('Итого') }}</th>
                </tr>
                </thead>
                @php
                    $num=0;
                    $sumCount = 0;
                @endphp
                @foreach ($dbOffersTurnover as $item)
                    @php
                        $num++;
                        $count = $item->whci_count * $item->whci_sign;
                        $sumCount += $count;

                        $className = 'table-success';
                        $typeName = CustomTranslator::get('Приемка');
                        if ($item->whci_doc_type == 2) {
                            $className = 'table-danger';
                            $typeName = CustomTranslator::get('Отгрузка');
                        }
                    @endphp
                    <tr>
                        <td class="{{ $className }} turnoverTD">
                            {{ $num }}
                        </td>
                        <td class="{{ $className }} turnoverTD">
                            @if($item->whci_doc_type == 2)
                                @if($item->whci_status == 0)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16" style="color: #d60000;">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check-square-fill" viewBox="0 0 16 16" style="color: #009900;">
                                        <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm10.03 4.97a.75.75 0 0 1 .011 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.75.75 0 0 1 1.08-.022z"/>
                                    </svg>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="{{ $className }} turnoverTD">
                            {{ $item->whci_date == '0000-00-00 00:00:00' ? '00.00.0000 (00:00:00)' : \Carbon\Carbon::parse($item->whci_date)->format('d.m.Y (H:i:s)') }}
                        </td>
                        <td class="{{ $className }} turnoverTD">
                            {{ $typeName }}
                        </td>
                        <td class="{{ $className }} turnoverTD">
                            {{ $item->whci_doc_id }}
                        </td>
                        <td class="{{ $className }} turnoverTD">
                            {!! $docStatus[$item->whci_id] !!}
                        </td>
                        @if($dbWh->wh_set_production_date == 1)
                            <td class="{{ $className }} turnoverTD">
                                {{ $item->whci_production_date }}
                            </td>
                        @endif
                        @if($dbWh->wh_set_expiration_date == 1)
                            <td class="{{ $className }} turnoverTD">
                                {{ $item->whci_expiration_date }}
                            </td>
                        @endif
                        @if($dbWh->wh_set_batch == 1)
                            <td class="{{ $className }} turnoverTD">
                                {{ $item->whci_batch }}
                            </td>
                        @endif
                        <td class="{{ $className }} turnoverTD">
                            <div class="place-container">
                                @if($docPlace[$item->whci_id] == '')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-sign-no-parking" viewBox="0 0 16 16" style="color: #d60000;">
                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m5.29-3.416L9.63 8.923C10.5 8.523 11 7.66 11 6.586c0-1.482-.955-2.584-2.538-2.584H5.5v.79L3.416 2.71a7 7 0 0 1 9.874 9.874m-.706.707A7 7 0 0 1 2.71 3.417l2.79 2.79V12h1.283V9.164h1.674zM8.726 8.019 6.777 6.07v-.966H8.27c.893 0 1.419.539 1.419 1.482 0 .769-.35 1.273-.963 1.433m-1.949-.534.59.59h-.59z"/>
                                    </svg>
                                @else
                                    <div class="place-box">{!! $docPlace[$item->whci_id] !!}</div>
                                @endif
                                @if($docPlace2[$item->whci_id] != '')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                                    </svg>
                                    <div class="place-box">{!! $docPlace2[$item->whci_id] !!}</div>
                                @endif
                            </div>
                        </td>
                        <td class="{{ $className }} turnoverTD">
                            {{ $count }}
                        </td>
                        <td class="{{ $className }} turnoverTD">
                            {{ $sumCount }}
                        </td>

                    </tr>
                @endforeach
            </table>
            <br>
            <b>{{ CustomTranslator::get('Текущий остаток') }}:</b> {{ $sumCount }}

        </div>
    </div>
</div>