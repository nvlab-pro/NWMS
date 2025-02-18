@extends('platform::auth')
@section('title',__('Войдите в свою учетную запись'))

@section('content')
    <h1 class="h4 text-body-emphasis mb-4">{{__('Войдите в свою учетную запись')}}:</h1>

    <form class="m-t-md"
          role="form"
          method="POST"
          data-controller="form"
          data-form-need-prevents-form-abandonment-value="false"
          data-action="form#submit"
          action="{{ route('platform.login.auth') }}">
        @csrf

        @includeWhen($isLockUser,'platform::auth.lockme')
        @includeWhen(!$isLockUser,'platform::auth.signin')
    </form>
@endsection
