@php use App\Services\CustomTranslator as CT; @endphp
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="padding: 20px 20px 20px 20px; padding: 20px;">

            <table class="table">
                <thead>
                <tr>
                    <th>Проводка</th>
                    <th>Сумма</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $totalSum = 0;
                @endphp
                @foreach($totalTransList as $trans)
                    <tr class="table-danger">
                        <td>{{ CT::get($trans->actionType->lat_name) }}</td>
                        <td> - {{ $trans->total_sum }}</td>
                    </tr>
                    @php
                        $totalSum -= $trans->total_sum;
                    @endphp
                @endforeach
                <tr class="table-success">
                    <td>{{ CT::get('Выставленные счета') }}</td>
                    <td> + {{ $totalInvoice }}</td>
                    @php
                        $totalSum += $totalInvoice;
                    @endphp
                </tr>
                <tr class="table-secondary">
                    <td style="text-align: right;"><b>{{ CT::get('ИТОГО:') }}</b></td>
                    @if($totalSum > 0)
                        <td style="background-color: #99DD99"><b> + {{ $totalSum }}</b></td>
                    @else
                        <td style="background-color: #FF9999"><b>{{ $totalSum }}</b></td>
                    @endif
                </tr>
                </tbody>
            </table>

        </div>
    </div>
</div>
