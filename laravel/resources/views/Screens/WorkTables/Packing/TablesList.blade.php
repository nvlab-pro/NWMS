<style>
    .tableButton {
        border-bottom: 2px solid #999999;
        border-right: 2px solid #999999;
        border-top: 1px solid #DDDDDD;
        border-left: 1px solid #DDDDDD;
        width: 95%;
        text-align: left;
        padding: 15px 15px 15px 15px;
        font-size: 20px;
        background-color: #D1E7DD;
        margin-bottom: 10px;
    }

    .tableButtonNullOffers {
        border-bottom: 2px solid #999999;
        border-right: 2px solid #999999;
        border-top: 1px solid #DDDDDD;
        border-left: 1px solid #DDDDDD;
        width: 95%;
        text-align: left;
        padding: 15px 15px 15px 15px;
        font-size: 20px;
        background-color: #f8d7da;
        margin-bottom: 10px;
    }

    .tableButtonCurrentUser {
        border-bottom: 2px solid #999999;
        border-right: 2px solid #999999;
        border-top: 1px solid #DDDDDD;
        border-left: 1px solid #DDDDDD;
        width: 95%;
        text-align: left;
        padding: 15px 15px 15px 15px;
        font-size: 20px;
        background-color: #55ff55;
        margin-bottom: 10px;
    }

    .tableButtonClose {
        border-bottom: 2px solid #999999;
        border-right: 2px solid #999999;
        border-top: 1px solid #DDDDDD;
        border-left: 1px solid #DDDDDD;
        width: 95%;
        text-align: left;
        padding: 15px 15px 15px 15px;
        font-size: 20px;
        background-color: #edb100;
        margin-bottom: 10px;
    }

    .tableButtonText {
        font-size: 16px;
        border-top: 1px solid #DDDDDD;
        margin-bottom: 0px;
        margin-top: 5px;
        padding-top: 5px;
    }

    .tableButtonText2 {
        font-size: 14px;
    }

    .icon-container {
        width: 50px;
        height: 50px;
        position: relative;
    }

    .icon {
        position: absolute;
        width: 50px;
        height: 50px;
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
    }

    .icon.active {
        opacity: 1;
    }
</style>

@if(isset($currentOrder) && $currentOrder > 0)
    <script>
        window.location.href = '{{ route('platform.tables.packing.scan', [$queueId, $tableId, $currentOrder]) }}';
    </script>
@endif

<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="margin: 10px 0px 10px 10px;">

            @foreach($dbTablesList as $table)

                @php
                    $userId = 0;
                    $userName = '';
                    if (isset($arBusyTables[$table->pl_id]) && $arBusyTables[$table->pl_id] !== false) {
                            $userId = $arBusyTables[$table->pl_id]['id'];
                            $userName = $arBusyTables[$table->pl_id]['name'];
                    }
                @endphp

                @if(count($arOrdersList[$table->pl_id]) > 0)
                    @php
                        $bgcolor = 'tableButton';
                    @endphp
                @else
                    @php
                        $bgcolor = 'tableButtonNullOffers';
                    @endphp
                @endif

                @if($userId > 0)
                    @if($userId == $currentUser->id)
                        @php
                            $bgcolor = 'tableButtonCurrentUser';
                        @endphp
                    @else
                        @php
                            $bgcolor = 'tableButtonClose';
                        @endphp
                    @endif
                @endif

                <button type="button" class="{{ $bgcolor }}"
                        @if($bgcolor != 'tableButtonNullOffers' && $bgcolor != 'tableButtonClose')
                            onClick="window.location.href='{{ route('platform.tables.packing.select', $queueId) }}?tableId={{ $table->pl_id }}'"
                        @endif
                >
                    <table>
                        <tr>
                            <td>
                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                                     class="bi bi-collection" viewBox="0 0 16 16">
                                    <path d="M2.5 3.5a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1zm2-2a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1zM0 13a1.5 1.5 0 0 0 1.5 1.5h13A1.5 1.5 0 0 0 16 13V6a1.5 1.5 0 0 0-1.5-1.5h-13A1.5 1.5 0 0 0 0 6zm1.5.5A.5.5 0 0 1 1 13V6a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5z"
                                          style="color: #95a5a6;"/>
                                    <path d="M4 17s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"
                                          style="color: #5e5d63;"/>
                                </svg>
                            </td>
                            <td>
                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                                     class="bi bi-chevron-double-right" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                          d="M3.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L9.293 8 3.646 2.354a.5.5 0 0 1 0-.708"
                                          style="color: #95a5a6;"/>
                                    <path fill-rule="evenodd"
                                          d="M7.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L13.293 8 7.646 2.354a.5.5 0 0 1 0-.708"
                                          style="color: #95a5a6;"/>
                                </svg>
                            </td>
                            <td>
                                @if($bgcolor == 'tableButton')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                                         class="bi bi-person-workspace" viewBox="0 0 16 16">
                                        <path d="M4 16s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"
                                              style="color: #5e5d63;"/>
                                        <path d="M2 1a2 2 0 0 0-2 2v9.5A1.5 1.5 0 0 0 1.5 14h.653a5.4 5.4 0 0 1 1.066-2H1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v9h-2.219c.554.654.89 1.373 1.066 2h.653a1.5 1.5 0 0 0 1.5-1.5V3a2 2 0 0 0-2-2z"
                                              style="color: #95a5a6;"/>
                                    </svg>
                                @endif
                                @if($bgcolor == 'tableButtonCurrentUser')
                                    <div class="icon-container">
                                        <svg id="icon1" class="icon active" xmlns="http://www.w3.org/2000/svg"
                                             width="50" height="50" fill="currentColor"
                                             class="bi bi-person-workspace" viewBox="0 0 16 16">
                                            <path d="M8 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3" style="color: #5e5d63;"/>
                                            <path d="m5.93 6.704-.846 8.451a.768.768 0 0 0 1.523.203l.81-4.865a.59.59 0 0 1 1.165 0l.81 4.865a.768.768 0 0 0 1.523-.203l-.845-8.451A1.5 1.5 0 0 1 10.5 5.5L13 2.284a.796.796 0 0 0-1.239-.998L9.634 3.84a.7.7 0 0 1-.33.235c-.23.074-.665.176-1.304.176-.64 0-1.074-.102-1.305-.176a.7.7 0 0 1-.329-.235L4.239 1.286a.796.796 0 0 0-1.24.998l2.5 3.216c.317.316.475.758.43 1.204Z" style="color: #5e5d63;"/>
                                        </svg>

                                        <svg id="icon2" class="icon" xmlns="http://www.w3.org/2000/svg" width="50"
                                             height="50" fill="currentColor"
                                             class="bi bi-person-standing" viewBox="0 0 16 16">
                                            <path d="M8 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3M6 6.75v8.5a.75.75 0 0 0 1.5 0V10.5a.5.5 0 0 1 1 0v4.75a.75.75 0 0 0 1.5 0v-8.5a.25.25 0 1 1 .5 0v2.5a.75.75 0 0 0 1.5 0V6.5a3 3 0 0 0-3-3H7a3 3 0 0 0-3 3v2.75a.75.75 0 0 0 1.5 0v-2.5a.25.25 0 0 1 .5 0" style="color: #5e5d63;"/>
                                        </svg>
                                    </div>
                                @endif
                                    @if($bgcolor == 'tableButtonClose')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-person-fill-x" viewBox="0 0 16 16">
                                            <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4" style="color: #5e5d63;"/>
                                            <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m-.646-4.854.646.647.646-.647a.5.5 0 0 1 .708.708l-.647.646.647.646a.5.5 0 0 1-.708.708l-.646-.647-.646.647a.5.5 0 0 1-.708-.708l.647-.646-.647-.646a.5.5 0 0 1 .708-.708" style="color: #f64747;"/>
                                        </svg>
                                    @endif
                                    @if($bgcolor == 'tableButtonNullOffers')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-person-fill-dash" viewBox="0 0 16 16">
                                            <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4" style="color: #5e5d63;"/>
                                            <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7M11 12h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1" style="color: #f64747;"/>
                                        </svg>
                                    @endif
                            </td>
                            <td style="padding-left: 20px;">
                                <b>@lang('Стол №:') {{ $table->pl_shelf }}</b>
                                <div class="tableButtonText">
                                    @if($userId == 0)
                                        <span class="tableButtonText2">@lang('Кол-во заказов готовых к упаковке:') {{ count($arOrdersList[$table->pl_id]) }}</span>
                                            @else
                                                <span class="tableButtonText2">@lang('Заказ упаковывается:') {{ $userName }}</span>
                                                    @endif
                                </div>
                            </td>
                        </tr>
                    </table>
                </button><br>

            @endforeach

        </div>
    </div>
</div>

<script>
    let currentIndex = 0;
    const icons = document.querySelectorAll('.icon');

    setInterval(() => {
        icons[currentIndex].classList.remove('active');
        currentIndex = (currentIndex + 1) % icons.length;
        icons[currentIndex].classList.add('active');
    }, 1000); // Меняем иконки каждую секунду
</script>