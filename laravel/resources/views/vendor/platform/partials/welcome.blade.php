<div class="bg-white rounded-top shadow-sm mb-4 rounded-bottom">

    <div class="row g-0">
        <div class="col col-lg-7 mt-6 p-4">

            <h2 class="text-body-emphasis fw-light lh-lg">
                @lang('С чего начать?')
            </h2>

            <p class="text-balance">
                @lang('Если вы только начинаете работать с нашей системой, вам стоит выполнить следующие шаги:')
            </p>
        </div>
    </div>

    <div class="bg-light m-0 p-md-4 p-3 border-top rounded-bottom g-md-5 text-balance">

        <div class="col-md-6 my-2">
            <h3 class="text-muted fw-light lh-lg d-flex align-items-center">
                <x-orchid-icon path="bs.stack"/>

                <span class="ms-3 text-body-emphasis"><a href="/admin/offers">@lang('Заведите товары в системе')</a></span>
            </h3>
            <p class="ms-md-5 ps-md-1">
                @lang('Перейдите в раздел') <a href="/admin/offers"><u>"@lang('Список товаров')"</u></a> @lang('и заведите вашу номенклатуру').
            </p>
        </div>

        <div class="col-md-6 my-2" style="padding-top: 20px;">
            <h3 class="text-muted fw-light lh-lg d-flex align-items-center">
                <x-orchid-icon path="bs.building-add"/>

                <span class="ms-3 text-body-emphasis"><a href="/admin/offers">@lang('Создайте первую приемку')</a></span>
            </h3>
            <p class="ms-md-5 ps-md-1">
                @lang('Перейдите в раздел') <a href="/admin/offers"><u>"@lang('Приемка товара')"</u></a>, @lang('создайте перевую приходную
                накладную и добавьте в нее товар, который вы планируете поставить на фулфилмент-склад').
            </p>
        </div>

        <div class="col-md-6 my-2" style="padding-top: 20px;">
            <h3 class="text-muted fw-light lh-lg d-flex align-items-center">
                <x-orchid-icon path="bs.box-seam-fill"/>

                <span class="ms-3 text-body-emphasis">@lang('Создайте заказ для отгрузки товара со склада')</span>
            </h3>
            <p class="ms-md-5 ps-md-1">
                @lang('Перейдите в раздел') <a href="/admin/orders"><u>"@lang('Список заказов')"</u></a> @lang('и создайте новый заказ для
                отгрузки товара со склада').
            </p>
        </div>

    </div>
</div>
