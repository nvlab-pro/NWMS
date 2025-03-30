@php
    use App\Services\CustomSiteTranslator;
    $header = CustomSiteTranslator::get('Launching WMS in a warehouse', $lang);
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
                        <img src="/img/docs/aboutWMS.jpg" alt="">
                    </div>
                    <div class="blog-details-content">
                        <div class="blog-details-meta">
                            <div class="techo-blog-meta-left">
                                <span>theory</span>
                                30.03.2025
                            </div>
                        </div>
                        <div class="blog-details-content-text">
                            <h2>{{ CustomSiteTranslator::get("Let's start with the theory", $lang) }}</h2>
                            <br>
                            <p>{{ CustomSiteTranslator::get("So, as I already mentioned in the", $lang) }} <a href="{{ $lang_str }}/about_wms">{{ CustomSiteTranslator::get("About WMS", $lang) }}</a>, {{ CustomSiteTranslator::get("warehouse automation is not as scary or complicated as it might seem. All warehouse processes remain the same as they were before automation began.", $lang) }}</p>
                        </div>

                        <div class="blog-details-content-text-inner">
                            <p>{{ CustomSiteTranslator::get("You still:", $lang) }}</p>
                            <ol>
                                <li>{{ CustomSiteTranslator::get("Receive goods into the warehouse", $lang) }}</li>
                                <li>{{ CustomSiteTranslator::get("Place them on shelves", $lang) }}</li>
                                <li>{{ CustomSiteTranslator::get("Pick items for orders", $lang) }}</li>
                                <li>{{ CustomSiteTranslator::get("Ship them to partners, clients, and even your beloved grandma", $lang) }}</li>
                            </ol>
                        </div>

                        <div class="blog-details-content-text-inner">
                            <p>{{ CustomSiteTranslator::get("Maybe you’re already using some kind of inventory software for this!", $lang) }}</p>
                            <p>{{ CustomSiteTranslator::get("Only one key thing changes: you start using barcodes at EVERY stage of working with goods.", $lang) }}</p>
                            <p>{{ CustomSiteTranslator::get("That's it! Period! You could say I’ve just revealed the whole essence! ;)", $lang) }}</p>
                            <p>{{ CustomSiteTranslator::get("If we look at the process in more detail, then:", $lang) }}</p>

                            <div class="d-flex justify-content-between" style="border-top: 1px dotted #000000; margin-top: 10px; padding-top: 10px;">
                                <br>
                                <button type="button" class="btn btn-outline-primary" onClick="window.location.href='{{ $lang_str }}/docs/theory/receiving_goods'">
                                    {{ CustomSiteTranslator::get("Start with receiving goods", $lang) }}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                                    </svg>
                                </button>
                            </div>
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