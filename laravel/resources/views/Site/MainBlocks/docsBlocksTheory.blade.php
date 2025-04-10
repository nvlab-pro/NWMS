@php
    use App\Services\CustomSiteTranslator;
    $currentPath = '/' . Request::path();
@endphp
<style>
    .widget-recent-post.active {
        border: 2px solid #0D5ADB;
        border-radius: 6px;
        padding: 5px;
        background-color: #eaf5ff;
        transition: 0.3s;
    }
</style>
<div class="widget-sidebar-box">
    <div class="widget-recent-post {{ $currentPath === $lang_str.'/docs/theory' ? 'active' : '' }}">
        <div class="rpost-thumb">
            <a href="{!! $lang_str !!}/docs/theory">
                <img src="/img/docs/aboutWMS.jpg" alt="{!! CustomSiteTranslator::get('Theory, about WMS', $lang) !!}" style="width: 75px;">
            </a>
        </div>
        <div class="rpost-content">
            <div class="rpost-title">
                <h4><a href="{!! $lang_str !!}/docs/theory">{!! CustomSiteTranslator::get('Theory (beginning)', $lang) !!}</a></h4>
                <span>30.03.2025</span>
            </div>
        </div>
    </div>

    <!-- widget recent post -->
    <div class="widget-recent-post {{ $currentPath === $lang_str.'/docs/theory/receiving_goods' ? 'active' : '' }}">
        <div class="rpost-thumb">
            <a href="{!! $lang_str !!}/docs/theory/receiving_goods"><img src="/img/docs/receivingGoods_mult.jpg" alt="{!! CustomSiteTranslator::get('Receiving goods', $lang) !!}" style="width: 75px;"></a>
        </div>
        <div class="rpost-content">
            <div class="rpost-title">
                <h4><a href="{!! $lang_str !!}/docs/theory/receiving_goods">{!! CustomSiteTranslator::get('Receiving goods', $lang) !!}</a></h4>
                <span>30.03.2025</span>
            </div>
        </div>
    </div>
    <!-- widget recent post -->
    <div class="widget-recent-post {{ $currentPath === $lang_str.'/docs/theory/putaway_of_goods' ? 'active' : '' }}">
        <div class="rpost-thumb">
            <a href="{!! $lang_str !!}/docs/theory/putaway_of_goods"><img src="/img/docs/putaway_of_goods_mult.jpg" alt="{!! CustomSiteTranslator::get('Putaway of goods on shelves', $lang) !!}" style="width: 75px;"></a>
        </div>
        <div class="rpost-content">
            <div class="rpost-title">
                <h4><a href="{!! $lang_str !!}/docs/theory/putaway_of_goods">{!! CustomSiteTranslator::get('Putaway of goods on shelves', $lang) !!}</a></h4>
                <span>30.03.2025</span>
            </div>
        </div>
    </div>
    <!-- widget recent post -->
    <div class="widget-recent-post {{ $currentPath === $lang_str.'/docs/theory/warehouse_labeling' ? 'active' : '' }}">
        <div class="rpost-thumb">
            <a href="{!! $lang_str !!}/docs/theory/warehouse_labeling"><img src="/img/docs/warehouse_labeling_mult.jpg" alt="{!! CustomSiteTranslator::get('Warehouse Labeling', $lang) !!}" style="width: 75px;"></a>
        </div>
        <div class="rpost-content">
            <div class="rpost-title">
                <h4><a href="{!! $lang_str !!}/docs/theory/warehouse_labeling">{!! CustomSiteTranslator::get('Warehouse Labeling', $lang) !!}</a></h4>
                <span>30.03.2025</span>
            </div>
        </div>
    </div>
    <!-- widget recent post -->
    <div class="widget-recent-post {{ $currentPath === $lang_str.'/docs/theory/whats_next' ? 'active' : '' }}">
        <div class="rpost-thumb">
            <a href="{!! $lang_str !!}/docs/theory/whats_next"><img src="/img/docs/whats_next_mult.jpg" alt="{!! CustomSiteTranslator::get("What's next?", $lang) !!}" style="width: 75px;"></a>
        </div>
        <div class="rpost-content">
            <div class="rpost-title">
                <h4><a href="{!! $lang_str !!}/docs/theory/whats_next">{!! CustomSiteTranslator::get("What's next?", $lang) !!}</a></h4>
                <span>30.03.2025</span>
            </div>
        </div>
    </div>
    <!-- widget recent post -->
    <div class="widget-recent-post {{ $currentPath === $lang_str.'/docs/theory/orders' ? 'active' : '' }}">
        <div class="rpost-thumb">
            <a href="{!! $lang_str !!}/docs/theory/orders"><img src="/img/docs/orders_mult.jpg" alt="{!! CustomSiteTranslator::get('Orders', $lang) !!}" style="width: 75px;"></a>
        </div>
        <div class="rpost-content">
            <div class="rpost-title">
                <h4><a href="{!! $lang_str !!}/docs/theory/orders">{!! CustomSiteTranslator::get("Orders", $lang) !!}</a></h4>
                <span>30.03.2025</span>
            </div>
        </div>
    </div>
    <!-- widget recent post -->
    <div class="widget-recent-post {{ $currentPath === $lang_str.'/docs/theory/theory_again' ? 'active' : '' }}">
        <div class="rpost-thumb">
            <a href="{!! $lang_str !!}/docs/theory/theory_again"><img src="/img/docs/sheme_eng4.jpg" alt="{!! CustomSiteTranslator::get('A bit of theory again', $lang) !!}" style="width: 75px;"></a>
        </div>
        <div class="rpost-content">
            <div class="rpost-title">
                <h4><a href="{!! $lang_str !!}/docs/theory/theory_again">{!! CustomSiteTranslator::get("A bit of theory again", $lang) !!}</a></h4>
                <span>30.03.2025</span>
            </div>
        </div>
    </div>
    <!-- widget recent post -->
    <div class="widget-recent-post {{ $currentPath === $lang_str.'/docs/theory/assembling_the_order' ? 'active' : '' }}">
        <div class="rpost-thumb">
            <a href="{!! $lang_str !!}/docs/theory/assembling_the_order"><img src="/img/docs/assembling_orders_mult.jpg" alt="{!! CustomSiteTranslator::get('Assembling the order', $lang) !!}" style="width: 75px;"></a>
        </div>
        <div class="rpost-content">
            <div class="rpost-title">
                <h4><a href="{!! $lang_str !!}/docs/theory/assembling_the_order">{!! CustomSiteTranslator::get("Assembling the order", $lang) !!}</a></h4>
                <span>02.04.2025</span>
            </div>
        </div>
    </div>
    <!-- widget recent post -->
    <div class="widget-recent-post {{ $currentPath === $lang_str.'/docs/theory/sorting_goods' ? 'active' : '' }}">
        <div class="rpost-thumb">
            <a href="{!! $lang_str !!}/docs/theory/sorting_goods"><img src="/img/docs/sorting_wall.jpg" alt="{!! CustomSiteTranslator::get('Sorting goods', $lang) !!}" style="width: 75px;"></a>
        </div>
        <div class="rpost-content">
            <div class="rpost-title">
                <h4><a href="{!! $lang_str !!}/docs/theory/sorting_goods">{!! CustomSiteTranslator::get("Sorting goods", $lang) !!}</a></h4>
                <span>02.04.2025</span>
            </div>
        </div>
    </div>
    <!-- widget recent post -->
    <div class="widget-recent-post {{ $currentPath === $lang_str.'/docs/theory/continuity_processes' ? 'active' : '' }}">
        <div class="rpost-thumb">
            <a href="{!! $lang_str !!}/docs/theory/continuity_processes"><img src="/img/docs/continuity_processes_mult.jpg" alt="{!! CustomSiteTranslator::get('Continuity of processes', $lang) !!}" style="width: 75px;"></a>
        </div>
        <div class="rpost-content">
            <div class="rpost-title">
                <h4><a href="{!! $lang_str !!}/docs/theory/continuity_processes">{!! CustomSiteTranslator::get("Continuity of processes", $lang) !!}</a></h4>
                <span>02.04.2025</span>
            </div>
        </div>
    </div>
    <!-- widget recent post -->
    <div class="widget-recent-post {{ $currentPath === $lang_str.'/docs/theory/packing_orders' ? 'active' : '' }}">
        <div class="rpost-thumb">
            <a href="{!! $lang_str !!}/docs/theory/packing_orders"><img src="/img/docs/packing_orders_mult.jpg" alt="{!! CustomSiteTranslator::get('Packing orders', $lang) !!}" style="width: 75px;"></a>
        </div>
        <div class="rpost-content">
            <div class="rpost-title">
                <h4><a href="{!! $lang_str !!}/docs/theory/packing_orders">{!! CustomSiteTranslator::get("Packing orders", $lang) !!}</a></h4>
                <span>03.04.2025</span>
            </div>
        </div>
    </div>
    <!-- widget recent post -->
    <div class="widget-recent-post {{ $currentPath === $lang_str.'/docs/theory/order_labeling' ? 'active' : '' }}">
        <div class="rpost-thumb">
            <a href="{!! $lang_str !!}/docs/theory/order_labeling"><img src="/img/docs/order_labeling_mult.jpg" alt="{!! CustomSiteTranslator::get('Order Labeling', $lang) !!}" style="width: 75px;"></a>
        </div>
        <div class="rpost-content">
            <div class="rpost-title">
                <h4><a href="{!! $lang_str !!}/docs/theory/order_labeling">{!! CustomSiteTranslator::get("Order Labeling", $lang) !!}</a></h4>
                <span>03.04.2025</span>
            </div>
        </div>
    </div>
    <!-- widget recent post -->
    <div class="widget-recent-post {{ $currentPath === $lang_str.'/docs/theory/dispatch_of_orders' ? 'active' : '' }}">
        <div class="rpost-thumb">
            <a href="{!! $lang_str !!}/docs/theory/dispatch_of_orders"><img src="/img/docs/DispatchOfOrders_mult.jpg" alt="{!! CustomSiteTranslator::get('Order Shipment', $lang) !!}" style="width: 75px;"></a>
        </div>
        <div class="rpost-content">
            <div class="rpost-title">
                <h4><a href="{!! $lang_str !!}/docs/theory/dispatch_of_orders">{!! CustomSiteTranslator::get("Order Shipment", $lang) !!}</a></h4>
                <span>10.04.2025</span>
            </div>
        </div>
    </div>
    <!-- widget recent post -->
    <div class="widget-recent-post {{ $currentPath === $lang_str.'/docs/theory/queue_management' ? 'active' : '' }}">
        <div class="rpost-thumb">
            <a href="{!! $lang_str !!}/docs/theory/queue_management"><img src="/img/docs/QueueManagement_mult.jpg" alt="{!! CustomSiteTranslator::get('Queue management', $lang) !!}" style="width: 75px;"></a>
        </div>
        <div class="rpost-content">
            <div class="rpost-title">
                <h4><a href="{!! $lang_str !!}/docs/theory/queue_management">{!! CustomSiteTranslator::get("Queue management", $lang) !!}</a></h4>
                <span>10.04.2025</span>
            </div>
        </div>
    </div>
</div>