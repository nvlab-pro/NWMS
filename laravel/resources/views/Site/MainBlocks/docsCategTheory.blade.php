@php
    use App\Services\CustomSiteTranslator;
@endphp
<div class="widget-sidebar-box">
    <h4 class="sidebar-title">{{ CustomSiteTranslator::get('Categories', $lang) }}</h4>
    <br>
    <ul class="sidebar-menu">
        <li class="cate-item-one"><a href="{{ $lang_str }}/docs/theory" class="active"><b style="color: #0D5ADB;">{{ CustomSiteTranslator::get('Theory', $lang) }}</b></a></li>
        <li class="cate-item-one"><a href="{{ $lang_str }}/docs/theory">{{ CustomSiteTranslator::get('For the warehouse owner', $lang) }}</a></li>
        <li class="cate-item-one"><a href="{{ $lang_str }}/docs/theory">{{ CustomSiteTranslator::get('For the fulfillment owner', $lang) }}</a></li>
        <li class="cate-item-one"><a href="{{ $lang_str }}/docs/theory">{{ CustomSiteTranslator::get('For the fulfillment client', $lang) }}</a></li>
    </ul>
</div>