@php use App\Services\CustomTranslator; @endphp

@if($order->getCompany)
    <div class="bg-white rounded shadow-sm mb-4 p-4">
        <h5 class="mb-3">{{ $order->getCompany->co_name ?? '-' }}</h5>
        <div class="row mb-2">
            <div class="col-sm-4 text-muted">{{ CustomTranslator::get('Юридическое название') }}</div>
            <div class="col-sm-8">{{ $order->getCompany->co_legal_name ?? '-' }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-sm-4 text-muted">{{ CustomTranslator::get('ИНН') }}</div>
            <div class="col-sm-8">{{ $order->getCompany->co_vat_number ?? '-' }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-sm-4 text-muted">{{ CustomTranslator::get('ОГРН') }}</div>
            <div class="col-sm-8">{{ $order->getCompany->co_registration_number ?? '-' }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-sm-4 text-muted">{{ CustomTranslator::get('Адрес') }}</div>
            <div class="col-sm-8">{{ $order->getCompany->co_postcode }} {{ $order->getCompany->co_address }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-sm-4 text-muted">{{ CustomTranslator::get('Телефон') }}</div>
            <div class="col-sm-8">{{ $order->getCompany->co_phone ?? '-' }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-sm-4 text-muted">{{ CustomTranslator::get('Email') }}</div>
            <div class="col-sm-8">{{ $order->getCompany->co_email ?? '-' }}</div>
        </div>
    </div>
@else
    <div class="alert alert-warning m-3">
        {{ CustomTranslator::get('Юридическое лицо не выбрано') }}
    </div>
@endif
