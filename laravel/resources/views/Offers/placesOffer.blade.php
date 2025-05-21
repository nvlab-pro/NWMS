@php use App\Services\CustomTranslator; @endphp
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="margin: 20px 20px 20px 20px;">

            @if($rests !== null)
                <h2>{{ CustomTranslator::get('Места размещения товара') }}:</h2>
                <br>
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>{{ CustomTranslator::get('Склад') }}</th>
                        <th style="text-align: center;">{{ CustomTranslator::get('Место') }}</th>
                        <th style="text-align: center;">{{ CustomTranslator::get('Дата производства') }}</th>
                        <th style="text-align: center;">{{ CustomTranslator::get('Срок годности') }}</th>
                        <th style="text-align: center;">{{ CustomTranslator::get('Партия') }}</th>
                        <th style="text-align: center;">{{ CustomTranslator::get('Остаток') }}</th>
                    </tr>
                    </thead>
                    @foreach($rests as $rest)
                        <tr>
                            <td style="padding-left: 30px; text-align: left;">
                                <b>{{ $rest['whName'] }} </b>
                            </td>
                            <td style="padding-left: 30px; text-align: center;">
                                @if($rest['placeName'] == '-')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                         class="bi bi-sign-no-parking" viewBox="0 0 16 16" style="color: #d60000;">
                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m5.29-3.416L9.63 8.923C10.5 8.523 11 7.66 11 6.586c0-1.482-.955-2.584-2.538-2.584H5.5v.79L3.416 2.71a7 7 0 0 1 9.874 9.874m-.706.707A7 7 0 0 1 2.71 3.417l2.79 2.79V12h1.283V9.164h1.674zM8.726 8.019 6.777 6.07v-.966H8.27c.893 0 1.419.539 1.419 1.482 0 .769-.35 1.273-.963 1.433m-1.949-.534.59.59h-.59z"/>
                                    </svg>
                                @else
                                    {{ $rest['placeName'] }}<br>
                                @endif
                            </td>
                            <td style="padding-left: 30px; text-align: center;">
                                {{ $rest['production_date'] }}<br>
                            </td>
                            <td style="padding-left: 30px; text-align: center;">
                                {{ $rest['expiration_date'] }}<br>
                            </td>
                            <td style="padding-left: 30px; text-align: center;">
                                {{ $rest['batch'] }}<br>
                            </td>
                            <td style="padding-left: 30px; text-align: center;">
                                {{ $rest['count'] }}<br>
                            </td>
                        </tr>
                    @endforeach
                </table>
            @endif

        </div>
    </div>
</div>