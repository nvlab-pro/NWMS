@php
    use App\Services\CustomTranslator;
@endphp
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="margin: 20px 20px 20px 20px; text-align: left;">

            <h3>{{ CustomTranslator::get('Дата') }}: {{ $fltDate }}</h3>

            <table class="table table-striped">
                <thead>
                <tr>
                    <th style="text-align: center;">{{ CustomTranslator::get('ФИО') }}</th>
                    <th style="text-align: center;">{{ CustomTranslator::get('Пришел') }}</th>
                    <th style="text-align: center;">{{ CustomTranslator::get('Ушел') }}</th>
                    <th style="text-align: center;">{{ CustomTranslator::get('Отработал') }}</th>
                </tr>
                </thead>
                <tbody>

                @foreach($arUsersDifferent as $userId => $name)
                    @if(isset( $arUsersName[$userId]))
                        @php
                            $styleBgColor = '';
                            if (!isset($arUsersStartTime[$userId])) $arUsersStartTime[$userId] = '-';
                            if ($arUsersStartTime[$userId] == '-') $styleBgColor = 'background-color: #F8D7DA;';
                        @endphp
                        <tr>
                            <td style="text-align: center; {{ $styleBgColor }}">{{ $arUsersName[$userId] }}</td>
                            <td style="text-align: center; {{ $styleBgColor }}" data-action="edit-time"
                                data-user-id="{{ $userId }}"
                                data-time-start="{{ isset($arUsersStartTime[$userId]) ? $arUsersStartTime[$userId] : '0' }}"
                                data-time-end="{{ isset($arUsersEndTime[$userId]) ? $arUsersEndTime[$userId] : '-' }}"
                                id="start{{ $userId }}">{{ isset($arUsersStartTime[$userId]) ? $arUsersStartTime[$userId] : '-' }}</td>
                            <td style="text-align: center; {{ $styleBgColor }}" data-action="edit-time"
                                data-user-id="{{ $userId }}"
                                data-time-start="{{ isset($arUsersStartTime[$userId]) ? $arUsersStartTime[$userId] : '0' }}"
                                data-time-end="{{ isset($arUsersEndTime[$userId]) ? $arUsersEndTime[$userId] : '-' }}"
                                id="end{{ $userId }}">{{ isset($arUsersEndTime[$userId]) ? $arUsersEndTime[$userId] : '-' }}</td>
                            <td style="text-align: center; {{ $styleBgColor }}">{{ isset($arUsersDifferent[$userId]) ? $arUsersDifferent[$userId] : '-' }}</td>
                            <td style="padding-left: 10px; padding-bottom: 5px; white-space: nowrap; {{ $styleBgColor }}">
                                <img src="/img/1x1.png" width="{{ $arUsersCount[$userId] }}" height="25"
                                     style="background-color: #A0A0A0;"></td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="editTimeModal" tabindex="-1" role="dialog" aria-labelledby="editTimeModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTimeModalLabel">{{ CustomTranslator::get('Изменить время') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 15px;">
                <div class="form-group">
                    <label for="startTime">{{ CustomTranslator::get('Пришел') }}:</label>
                    <input class="form-control" id="startTime">
                </div>
                <div class="form-group">
                    <label for="endTime">{{ CustomTranslator::get('Ушел') }}:</label>
                    <input class="form-control" id="endTime">
                </div>
                <input type="hidden" id="modalUserId">
                <input type="hidden" id="modalDate" value="{{ $fltDate }}">
                <input type="hidden" id="modalType">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ CustomTranslator::get('Закрыть') }}</button>
                <button type="button" class="btn btn-primary" id="saveTimeBtn">{{ CustomTranslator::get('Сохранить') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Открытие модального окна
        $('td[data-action="edit-time"]').click(function () {
            const userId = $(this).data('user-id');
            const type = $(this).data('type');
            const startTime = document.getElementById('start' + userId).innerHTML;
            const endTime = document.getElementById('end' + userId).innerHTML;

            $('#modalUserId').val(userId);
            $('#modalType').val(type);
            $('#startTime').val(startTime);
            $('#endTime').val(endTime);

            $('#editTimeModal').modal('show');
        });

        // Отправка данных через AJAX
        $('#saveTimeBtn').click(function (event) {
            event.preventDefault(); // Предотвращаем стандартное поведение кнопки

            const userId = $('#modalUserId').val();
            const date = $('#modalDate').val();
            const type = $('#modalType').val();
            const startTime = $('#startTime').val();
            const endTime = $('#endTime').val();

            $.ajax({
                url: "{{ route('platform.ea.users') }}/saveTime",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    userId: userId,
                    date: date,
                    type: type,
                    startTime: startTime,
                    endTime: endTime
                },
                success: function (response) {
                    $('#editTimeModal').modal('hide'); // Закрываем модалку
                    updateTableRow(userId, type, startTime, endTime);
//                    window.location.href = "{{ route('platform.ea.users') }}";
//                    location.reload(); // Обновляем страницу (или обновите данные на лету)
                },
                error: function (xhr) {
                    console.log("{{ CustomTranslator::get('Ошибка') }}:", xhr.responseText);
                    alert("{{ CustomTranslator::get('Ошибка сохранения, попробуйте еще раз') }}'.");
                }
            });
        });

        // Функция для обновления данных в таблице
        function updateTableRow(userId, type, startTime, endTime) {
            document.getElementById('start' + userId).innerHTML = startTime;
            document.getElementById('end' + userId).innerHTML = endTime;
        }
    });

</script>
