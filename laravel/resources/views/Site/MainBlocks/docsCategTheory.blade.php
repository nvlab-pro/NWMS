@php
    use App\Services\CustomSiteTranslator;
@endphp
<div class="widget-sidebar-box">
    <h4 class="sidebar-title">Categories</h4>
    <br>
    <ul class="sidebar-menu">
        <li class="cate-item-one"><a href="{{ $lang_str }}/docs/theory"><b>{{ CustomSiteTranslator::get('Theory', $lang) }}</b></a></li>
        <li class="cate-item-one"><a href="{{ $lang_str }}/docs/theory">{{ CustomSiteTranslator::get('For the warehouse owner', $lang) }}</a></li>
        <li class="cate-item-one"><a href="{{ $lang_str }}/docs/theory">{{ CustomSiteTranslator::get('For the fulfillment owner', $lang) }}</a></li>
        <li class="cate-item-one"><a href="{{ $lang_str }}/docs/theory">{{ CustomSiteTranslator::get('For the fulfillment client', $lang) }}</a></li>
    </ul>
</div>