<style>
    .tdMain {
        text-align: center;
    }
</style>

<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">
    <div class="row g-0">
        <div style="margin: 20px 20px 20px 20px;">

            <h3>@lang('Информация по сборке заказа')</h3>

            <table class="table">
                <tr>
                    <th class="tdMain">@lang('Дата')</th>
                    <th class="tdMain">@lang('Кладовщик')</th>
                    <th class="tdMain">@lang('Артикул')</th>
                    <th class="tdMain">@lang('Название')</th>
                    <th class="tdMain">@lang('Заказано')</th>
                    <th class="tdMain">@lang('Собрано')</th>
                    <th class="tdMain">@lang('Место подбора')</th>
                    <th class="tdMain">@lang('Место завершения сборки')</th>
                </tr>
            @foreach($arPickingOffersList as $offer)
                <tr>
                    <td class="tdMain">{{ $offer['oa_data'] }}</td>
                    <td class="tdMain">{{ $offer['oa_user_name'] }}</td>
                    <td class="tdMain">{{ $offer['of_article'] }}</td>
                    <td>{{ $offer['of_name'] }}</td>
                    <td class="tdMain">{{ $offer['oo_qty'] }}</td>
                    <td class="tdMain">{{ $offer['oa_qty'] }}</td>
                    <td class="tdMain">{{ $offer['picking_place'] }}</td>
                    <td class="tdMain">{{ $offer['end_place'] }}</td>
                </tr>
            @endforeach
            </table>
        </div>
    </div>
</div>