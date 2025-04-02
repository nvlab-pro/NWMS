@php
    use App\Services\CustomSiteTranslator;
    $header = CustomSiteTranslator::get('Putaway of goods on shelves', $lang);
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
                        <img src="/img/docs/putaway_of_goods_mult.jpg" alt="">
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
                                <h2>Putaway of goods on shelves</h2>
                                <br>
                                <p>After your goods have been received, they must be placed on the warehouse shelves. At this stage, it's no longer you or your employees, but your WMS that must precisely track which shelf holds what quantity of which product.</p>
                                <p>This is one of the core foundations of warehouse automation!</p>
                                <p><strong>And here we come to the second axiom:</strong></p>
                                <div class='alert alert-warning' role='alert'>There must NEVER be unbound goods in the warehouse!</div>
                                <p>In other words: once the product enters the warehouse (is received), it must ALWAYS be linked to something or someone!</p>
                                <p>Received goods? Link them to a shelf.</p>
                                <p>A picker took goods from a shelf for an order? The goods are now linked to the picker.</p>
                                <p>Canceled the order during packing? The goods must be linked to a special buffer zone for redistribution.</p>
                                <p>Started packing the order? The goods are linked to the packing table.</p>
                                <p>And so on...</p>", $lang) !!}
                            {!! CustomSiteTranslator::get("<p>At any moment, for any unit of goods, you must be able to see exactly where it is located. Whether it’s on a shelf, in an employee’s hands, or inside a not-yet-shipped or already shipped order.</p>
                                <p><strong>This is the foundation! Only this approach can ensure accurate stock levels!</strong></p>
                                <p>In any other case, chaos begins. Someone takes an item off a shelf to pack it, gets a call, is asked to help unload a delivery, gets distracted, puts the item on the wrong shelf — and that's it! The item is still physically in the warehouse, but no one knows where exactly.</p>
                                <p>And that’s just one example of how goods get 'lost' in the warehouse — not even talking about actual theft.</p>
                                <p>There’s a brutally simple theory: if something can be stolen — it eventually will be.</p>
                                <p>Knowing exactly what you have, where it is at this very moment, and how many units there are won’t completely solve the problem (people will still get distracted or steal), but it will significantly (even SIGNIFICANTLY) reduce it. And most importantly — it gives you tools to solve the issues that arise.</p>
                                <p>Convinced? No?<br>It's your warehouse — you decide...</p>
                                <p>To learn how this process works specifically in NWMS, visit:</p>
                            ", $lang) !!}

                            <button type="button" class="btn btn-outline-dark" onClick="window.location.href='{{ $lang_str }}/docs/putaway_of_goods'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link-45deg" viewBox="0 0 16 16">
                                    <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
                                    <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/>
                                </svg>
                                {{ CustomSiteTranslator::get("Putaway of goods on shelves", $lang) }}
                            </button>
                        </div>
                        <br>
                        <div class="d-flex justify-content-between" style="border-top: 1px dotted #000000; margin-top: 10px; padding-top: 10px;">
                            <button type="button" class="btn btn-outline-primary"
                                    onClick="window.location.href='{{ $lang_str }}/docs/theory/receiving_goods'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                                </svg>
                                {{ CustomSiteTranslator::get("Receiving goods", $lang) }}
                            </button>

                            <button type="button" class="btn btn-outline-primary"
                                    onClick="window.location.href='{{ $lang_str }}/docs/theory/warehouse_labeling'">
                                {{ CustomSiteTranslator::get("Warehouse labeling", $lang) }}
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