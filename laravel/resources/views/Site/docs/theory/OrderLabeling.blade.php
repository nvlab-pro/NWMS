@php
    use App\Services\CustomSiteTranslator;
    $header = CustomSiteTranslator::get("Order Labeling", $lang);
@endphp
@include('Site.MainBlocks.header')

@php
    $lang_str = '/' . $lang;
    if ($lang == 'en') $lang_str = '';
@endphp

<!--==================================================-->
<!-- Start techo Main Menu  -->
<!--==================================================-->
@include('Site.MainBlocks.menu')
<!--==================================================-->
<!-- End techo Main Menu  -->
<!--==================================================-->


<!--==================================================-->
<!-- Start techo Breadcumb Area -->
<!--==================================================-->
<div class="breadcumb-area align-items-center d-flex">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcumb-content">
                    <div class="breadcumb-title wow fadeInLeft animated">
                        <h2>{{ $header }}</h2>
                    </div>
                    <div class="breadcumb-content-menu wow fadeInRight animated">
                        <ul>
                            <li><a href="/">{{ CustomSiteTranslator::get('HOME', $lang) }}<i class="fas fa-angle-right"></i></a></li>
                            <li><span>{{ $header }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--==================================================-->
<!-- End techo Breadcumb Area -->
<!--==================================================-->


<!--==================================================-->
<!-- start techo blog-details-section -->
<!--==================================================-->
<div class="blog-details-section pt-90 pb-70">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-12">
                <div class="single-blog-details-box wow fadeInLeft animated">
                    <div class="details-thumb">
                        <img src="/img/docs/order_labeling_mult.jpg" alt="">
                    </div>
                    <div class="blog-details-content">
                        <div class="blog-details-meta">
                            <div class="techo-blog-meta-left">
                                <span>{{ CustomSiteTranslator::get('theory', $lang) }}</span>
                                03.04.2025
                            </div>
                        </div>
                        <div class="blog-details-content-text">
                            {!! CustomSiteTranslator::get("
<h2>Order Labeling</h2>
<br>
<p>This process is also optional and is only required if the order needs to be labeled with a unique tag from a delivery service or marketplace.</p>
<p>The labeling can be combined with the packaging process — instead of printing a WMS-specific label with the order ID, you immediately get the label from the required delivery service.
And in fact, that seems more logical: why have an extra step, a separate table, or an additional employee if it can all be done right during packaging?</p>
                            ", $lang) !!}
                            {!! CustomSiteTranslator::get("
<p>Labeling as a separate process may be necessary in the following cases:</p>
<ol>
    <li>Each order needs to be measured and weighed (although this can often be integrated into the packaging process — plus, it makes sense to standardize box sizes, which can eliminate the need for measuring most orders).</li>

    <li>The size and format of labels vary between different delivery services, which may require frequent changes of printer rolls and adjustments to printer settings.
    However, this can also be solved by organizing multiple packing stations with different settings and distributing order flows across them using queue configurations
    (we’ll talk more about queues a bit later).</li>

    <li>If WMS interacts with the delivery service’s API in real time during the labeling process, the labeling speed can drop significantly if the delivery service’s server is slow —
    or even halt entirely if the server goes down.
    This is a more serious argument in favor of separating labeling into its own process, since it allows packaging to continue smoothly regardless of third-party server availability,
    while buffering the orders for later labeling.</li>
</ol>

                            ", $lang) !!}
                            {!! CustomSiteTranslator::get("
<p>In any case, labeling is required as a concept — and whether it should be a separate process or integrated into packaging depends on the specific needs of your warehouse.
The WMS should provide the flexibility to support as many scenarios as possible.</p>

<p>That’s it for now — let’s move on to the shipping process for orders that have already been picked, packed, and labeled.</p>
                            ", $lang) !!}

                        </div>

                        <br>
                        <div class="d-flex justify-content-between" style="border-top: 1px dotted #000000; margin-top: 10px; padding-top: 10px;">
                            <button type="button" class="btn btn-outline-primary"
                                    onClick="window.location.href='{{ $lang_str }}/docs/theory/packing_orders'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                                </svg>
                                {{ CustomSiteTranslator::get("Packing orders", $lang) }}
                            </button>

                            <button type="button" class="btn btn-outline-dark"
                            >
                                В разработке...
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-arrow-right" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                          d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="sidebar-content-box wow fadeInRight animated">

                    @include('Site.MainBlocks.docsBlocksTheory')

                    @include('Site.MainBlocks.docsCategTheory')

                </div>
            </div>
        </div>
    </div>
</div>
<!--==================================================-->
<!-- End techo blog-details-section -->
<!--==================================================-->




@include('Site.MainBlocks.footer')