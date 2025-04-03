@php
    use App\Services\CustomSiteTranslator;
    $header = CustomSiteTranslator::get("Packing orders", $lang);
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
                        <img src="/img/docs/packing_orders_mult.jpg" alt="">
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
<h2>Order Packaging</h2>
<br>
<p>The packaging process is optional — it is only required if the item truly needs additional packaging. This is usually the case for courier delivery, i.e., for retail orders.</p>
<p>Orders can arrive for packing either one by one (order-based picking) or from sorting carts or cells.</p>
<p>It’s important to note that the packer usually scans each item in the order again, so packaging also serves as another step in verifying the correctness of the picked order.</p>
<p>After packaging, we end up with a box or bag without any identifying marks, so the WMS must automatically print a label with the order ID and barcode, which the packer then attaches to the packaged order.</p>

                            ", $lang) !!}
                            {!! CustomSiteTranslator::get("
<p>There is also a scenario where the packer assembles the order themselves, without using picking and sorting processes.
In this case, the screen displays not only the list of items to be packed, but also their storage locations in the warehouse.
The packer goes to the warehouse and retrieves the required items manually.
This approach is suitable for very small warehouses or when there is a need to pack a large number of identical items or similar orders.</p>

<p>On the other hand, if you're assembling a large wholesale order directly into boxes or onto a pallet, then sorting and packaging processes are simply not necessary.</p>

<p>But don’t worry — we’ll go through all of these scenarios in detail once we get to working directly in the program.</p>

<p>For now, let’s take a look at the order labeling process…</p>

                            ", $lang) !!}

                        </div>

                        <br>
                        <div class="d-flex justify-content-between" style="border-top: 1px dotted #000000; margin-top: 10px; padding-top: 10px;">
                            <button type="button" class="btn btn-outline-primary"
                                    onClick="window.location.href='{{ $lang_str }}/docs/theory/continuity_processes'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                                </svg>
                                {{ CustomSiteTranslator::get("Continuity of processes", $lang) }}
                            </button>

                            <button type="button" class="btn btn-outline-primary"
                                    onClick="window.location.href='{{ $lang_str }}/docs/theory/order_labeling'">
                                {{ CustomSiteTranslator::get("Order Labeling", $lang) }}
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