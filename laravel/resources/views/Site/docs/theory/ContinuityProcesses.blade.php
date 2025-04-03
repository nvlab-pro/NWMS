@php
    use App\Services\CustomSiteTranslator;
    $header = CustomSiteTranslator::get("Continuity of Warehouse Processes", $lang);
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
                            <li><a href="/">{{ CustomSiteTranslator::get('HOME', $lang) }}<i
                                            class="fas fa-angle-right"></i></a></li>
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
                        <img src="/img/docs/continuity_processes_mult.jpg" alt="">
                    </div>
                    <div class="blog-details-content">
                        <div class="blog-details-meta">
                            <div class="techo-blog-meta-left">
                                <span>{{ CustomSiteTranslator::get('theory', $lang) }}</span>
                                02.04.2025
                            </div>
                        </div>
                        <div class="blog-details-content-text">
                            {!! CustomSiteTranslator::get('
<h2>Continuity of Warehouse Processes</h2>
<br>
<p>It’s important to understand that the sorting stage isn’t required if you’ve been using single-order picking (i.e., picking one order at a time). In this case,
there’s no need to split the picked goods into multiple orders — but a sorting wall might still be useful!</p>

<p>When automating a warehouse, one of the key responsibilities of the person managing warehouse operations (warehouse manager or their assistants) is to avoid so-called bottlenecks in the workflow — that is,
points where the entire process slows down.</p>

                            ', $lang) !!}
                            {!! CustomSiteTranslator::get('
<p>In our example, if there are pickers (employees who pick goods) and packers (employees who pack the picked goods), then with a high degree of certainty, sooner or later (more likely sooner),
you’ll face a situation where some stages begin to idle.</p>

<p>For instance, pickers are still busy collecting orders, while the packers have nothing to do because there are no goods ready for packing. As a result, they just stand around waiting.</p>
                            ', $lang) !!}
                            {!! CustomSiteTranslator::get('
<p>Setting up processes so that the workflow runs continuously is only possible through goods buffering. In our case, this means that pickers don’t hand their orders directly to the packers — instead,
they assign them to the sorting wall, gradually filling the sorting bins, even if they are picking orders one by one.</p>

<p>This creates a buffer of already picked orders ready for packing, which ensures uninterrupted workflows for both picking and packing.</p>

<p>We should apply similar mechanisms at every stage of handling goods and orders in the warehouse. On one hand, this keeps all processes flowing continuously throughout their "lifecycle." On the other hand,
it frees up the warehouse manager from constant monitoring and micromanagement. A well-tuned system continues to operate smoothly even over extended periods!</p>
                            ', $lang) !!}
                        </div>

                        <br>
                        <div class="d-flex justify-content-between"
                             style="border-top: 1px dotted #000000; margin-top: 10px; padding-top: 10px;">
                            <button type="button" class="btn btn-outline-primary"
                                    onClick="window.location.href='{{ $lang_str }}/docs/theory/sorting_goods'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-arrow-left" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                          d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                                </svg>
                                {{ CustomSiteTranslator::get("Sorting goods", $lang) }}
                            </button>

                            <button type="button" class="btn btn-outline-primary"
                                    onClick="window.location.href='{{ $lang_str }}/docs/theory/packing_orders'">
                                {{ CustomSiteTranslator::get("Order Packaging", $lang) }}
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