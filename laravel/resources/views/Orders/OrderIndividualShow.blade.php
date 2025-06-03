@php
    use App\Services\CustomTranslator;
    $contact = $order->getContact;
@endphp

@if($contact)
    <div class="bg-white rounded shadow-sm p-4 mb-4">
        <h5 class="mb-3">{{ CustomTranslator::get('Контактные данные получателя') }}</h5>
        <p><strong>{{ CustomTranslator::get('ФИО') }}:</strong>
            {{ $contact->oc_last_name }} {{ $contact->oc_first_name }} {{ $contact->oc_middle_name }}
        </p>
        <p><strong>{{ CustomTranslator::get('Телефон') }}:</strong> {{ $contact->oc_phone }}</p>
        <p><strong>{{ CustomTranslator::get('Email') }}:</strong> {{ $contact->oc_email }}</p>
        <p><strong>{{ CustomTranslator::get('Адрес') }}:</strong> {{ $contact->oc_full_address }}</p>
        <p><strong>{{ CustomTranslator::get('Индекс') }}:</strong> {{ $contact->oc_postcode }}</p>
    </div>
@else
    <div class="alert alert-warning">{{ CustomTranslator::get('Контактная информация не указана') }}</div>
@endif
