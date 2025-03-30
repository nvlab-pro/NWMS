@php
    use App\Services\CustomSiteTranslator;
    $header = CustomSiteTranslator::get("A bit of theory again", $lang);
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
                        <img src="/img/docs/sheme_eng2.jpg" alt="">
                    </div>
                    <div class="blog-details-content">
                        <div class="blog-details-meta">
                            <div class="techo-blog-meta-left">
                                <span>{{ CustomSiteTranslator::get('theory', $lang) }}</span>
                                30.03.2025
                            </div>
                        </div>
                        <div class="blog-details-content-text">
                            {!! CustomSiteTranslator::get("
                                <h2>A bit of theory again</h2>
                                <br>
                                <p>Once we’ve received an order for shipment in the WMS, we need to pick the goods for that order and (possibly) pack them.</p>
                                <p>Let’s now take a look at the following diagram:</p>
                                <p>This diagram illustrates the main warehouse processes:</p>
                                <ol>
                                    <li><b>Receiving goods</b> — we’ve already covered this step earlier.</li>
                                    <li><b>Putaway of goods on shelves</b> — placing goods on storage shelves. Also covered earlier.</li>
                                    <li><b>Picking</b> — this step appears when we have a specific order. Warehouse staff must pick goods from shelves and prepare them for shipment.</li>
                                    <li><b>Sorting</b> — this process is only needed when multiple orders are picked at once. In this stage, the total batch of goods is sorted by individual orders. We’ll explain this in more detail later.</li>
                                    <li><b>Packing</b> — if the order needs to be packed (e.g. into a box or bag), this process handles it.</li>
                                    <li><b>Labeling</b> — if the order needs to be prepared for a delivery service (weighing, measuring, applying a shipping label, etc.), this step is added.</li>
                                    <li><b>Shipping</b> — finally, we ship the prepared orders to the client, delivery service, or transport company.</li>
                                </ol>
                            ", $lang) !!}
                            {!! CustomSiteTranslator::get("<p>In short, this is exactly how it looks.</p>
                                <p>And here it’s important to understand that all of these are modular processes. You can build a workflow that’s ideal for your specific warehouse. Not every warehouse, scenario, or business needs all of them.</p>
                                <p>For example, if you have a small warehouse, ship in bulk, and have just a few suppliers — you might not need sorting, packing, or labeling. Only picking and shipping may be enough.</p>
                                <p>I won’t go over every possible scenario here — there are simply too many — but hopefully the idea is clear. Now let’s move on to each process block individually and figure out when you need them — and when you don’t.</p>
                            ", $lang) !!}
                        </div>

                        <br>
                        <div class="d-flex justify-content-between" style="border-top: 1px dotted #000000; margin-top: 10px; padding-top: 10px;">
                            <button type="button" class="btn btn-outline-primary"
                                    onClick="window.location.href='{{ $lang_str }}/docs/theory/orders'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                                </svg>
                                {{ CustomSiteTranslator::get("What's next?", $lang) }}
                            </button>

                            <button type="button" class="btn btn-outline-dark"
                                    >
                                {{ CustomSiteTranslator::get("In development...", $lang) }}
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
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