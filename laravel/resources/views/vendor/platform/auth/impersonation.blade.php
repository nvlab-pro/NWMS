@extends('platform::auth')
@section('title',__('Доступ запрещен: просмотр от имени другого пользователя'))

@section('content')
    <h1 class="h4 text-body-emphasis mb-4">{{__('Ограниченный доступ')}}</h1>

    <form role="form"
          method="POST"
          data-controller="form"
          data-form-need-prevents-form-abandonment-value="false"
          data-action="form#submit"
          action="{{ route('platform.switch.logout') }}">
        @csrf

        <p>
            {{ __("В настоящее время вы просматриваете эту страницу от имени пользователя, у которого нет к ней доступа. Чтобы вернуться к просмотру от своего имени, нажмите кнопку «Перейти на мою учетную запись». Возможно, страница будет отображаться корректно при просмотре из вашей учетной записи.") }}
        </p>

        <button id="button-login" type="submit" class="btn btn-default btn-block" tabindex="2">
            <x-orchid-icon path="bs.box-arrow-in-right" class="small me-2"/> {{__('Перейти на мою учетную запись')}}
        </button>

    </form>
@endsection
