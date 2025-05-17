@php
    use Carbon\Carbon;
    use App\Services\CustomTranslator;
@endphp
<form action="{{ route('platform.ea.main')  }}" method="get"
      style="text-align: center; padding: 0px 0px 0px 0px; margin-top: 0px; margin-bottom: 0px;">
    <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">

        <div style="text-align: center; padding: 10px 10px 10px 10px; margin-top: 5px; margin-bottom: 5px;">
            <div class="btn-group">
                <input type="text" name="barcode" id="barcode" size="25" autofocus onkeyup="handleKeyPress(event)">
                <input type="submit" value="Scan" id="btn">
            </div>

        </div>
        <script>
            function handleKeyPress(event) {
                if (event.key === 'Enter') {
                    document.getElementById('btn').click();
                }
            }
        </script>
    </div>
</form>

@if(isset($message))
    <div class="alert alert-danger" role="alert">
        {{$message}}
    </div>
@endif

@if($type == -1)
    <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
        <div style="text-align: center; padding: 20px 10px 20px 10px; margin-top: 5px; margin-bottom: 5px; background-color: #FFCCCC; border-top: 1px solid #CCCCCC; border-bottom: 1px solid #CCCCCC;">
            <h2>{{ CustomTranslator::get('Упс! Что-то пошло не так и я не знаю этого штрих-кода!') }}</h2>
        </div>
    </div>
@endif

@if($type == 1)
    <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
        <div style="text-align: center; padding: 20px 10px 20px 10px; margin-top: 5px; margin-bottom: 5px; background-color: #CCFFCC; border-top: 1px solid #CCCCCC; border-bottom: 1px solid #CCCCCC;">
            <h2>{{ CustomTranslator::get('Привет') }} {{ $user->name }}!</h2>
            <h2>{{ CustomTranslator::get('Большой Брат следит за тобой!') }}</h2>
            <br>
            {{ CustomTranslator::get('Фиксирую время') }}: {{ Carbon::now('Europe/Sofia')->format('H:i:s') }}
            <br><br>
            <h1 style="font-size: 75px;">&#9749;</h1>
        </div>
    </div>
@endif

@if($type == 2)
    <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
        <div style="text-align: center; padding: 20px 10px 20px 10px; margin-top: 5px; margin-bottom: 5px; background-color: #FFEEEE; border-top: 1px solid #CCCCCC; border-bottom: 1px solid #CCCCCC;">
            <h2>{{ CustomTranslator::get('Пока') }} {{ $user->name }}!</h2>
            <h2>{{ CustomTranslator::get('Большой Брат все-равно следит за тобой!') }}</h2>
            <br>
            {{ CustomTranslator::get('Фиксирую время') }}: {{ Carbon::now('Europe/Sofia')->format('H:i:s') }}
            <br><br>
            <h1 style="font-size: 75px;">&#9786;</h1>

        </div>
    </div>
@endif

