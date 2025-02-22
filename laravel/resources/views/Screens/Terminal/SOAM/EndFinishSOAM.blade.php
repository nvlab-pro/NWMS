@if($action == 'finishOrder')
    <style>
        .button-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
    <div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
        <div style="padding: 10px 10px 10px 10px; text-align: center;">

            <div class="alert alert-success warningDIV" role="alert">
                @lang('Отлично! Заказ привязан к конечному месту хранения!')<br><br>
                @lang('Можете приступить к сборке следующего!')<br>
            </div>
            <hr>
            <div class="button-container">
                <button type="button" class="btn btn-primary"
                        onClick="location.href='{{ route('platform.terminal.soam.order', [$soaId, 0]) }}'">
                    @lang('Перейти к сборке следующего')
                </button>
            </div>
            <hr>
            <img src="/img/assembly_finish.jpg" width="250" style="border-radius: 10px;">
            <br><br>
        </div>
    </div>

@endif