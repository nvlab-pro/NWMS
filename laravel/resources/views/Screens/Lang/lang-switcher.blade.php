@php
    use App\Services\CustomTranslator;
@endphp
<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div class="p-3 m-2 d-flex flex-wrap align-items-center">
            <H4 class="me-2">{{ CustomTranslator::get('Выберите язык (система):') }}</h4>
            <div class="d-flex flex-wrap gap-2">
                @foreach($available_locales as $locale => $name)
                    @if (!strpos($name, '.SITE'))
                    <button type="button"
                            onClick="window.location.href = '{{ route('platform.settings.lang.editor', ['locale' => $locale]) }}'"
                            class="btn btn-sm {{ request('locale', config('app.locale')) === $locale ? 'btn-primary' : 'btn-outline-primary' }}"
                            style="min-width: 80px;">
                        {{ strtoupper($locale) }}
                    </button>
                    @endif
                @endforeach
            </div>
        </div>
        <hr>
        <div class="p-3 m-2 d-flex flex-wrap align-items-center">
            <H4>{{ CustomTranslator::get('Выберите язык (сайт):') }}</h4>
            <div class="d-flex flex-wrap gap-2">
                @foreach($available_locales as $locale => $name)
                    @if (strpos($name, '.SITE'))
                    <button type="button"
                            onClick="window.location.href = '{{ route('platform.settings.lang.editor', ['locale' => $locale]) }}'"
                            class="btn btn-sm {{ request('locale', config('app.locale')) === $locale ? 'btn-primary' : 'btn-outline-primary' }}"
                            style="min-width: 80px;">
                        {{ strtoupper($locale) }}
                    </button>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
