@php
    use App\Services\CustomSiteTranslator;
    $header = CustomSiteTranslator::get("Assembling the order", $lang);
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
                        <img src="/img/docs/assembling_orders_mult.jpg" alt="">
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
<h2>Assembling the order</h2>
<br>
<p>Once we’ve received a shipment order in the WMS, a warehouse worker needs to go and pick the goods for this order. And here we have two options:</p>
<ol>
    <li><h5>Single-order picking</h5>
    <br>
    <div><p>The warehouse worker (or several workers) picks one order at a time. Pretty straightforward, right? You take a data collection terminal, go into the warehouse, and pick all the items for one specific order.</p>

    <div style="text-align: center;"><img src="/img/docs/assembling_one_orders_wave.jpg" style="border-radius: 12px; box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.3); max-width: 100%; height: auto;"></div>
    <br>

    <div class="alert alert-success" role="alert">
        <p><b style="color: green;">Pros:</b> the simplest type of picking. Perfectly suited for small warehouses or large orders with many items.</p>
    </div>

    <div class="alert alert-danger" role="alert">
        <p><b style="color: red;">Cons:</b> if the orders are small (contain few items) but numerous, warehouse workers will spend a lot of time walking back and forth. They’ll often have to visit the same shelves multiple times a day and carry the same items repeatedly.
        This can significantly increase the amount of work required to complete picking.</p>

        <p>Therefore, if you’re dealing with many small orders, it’s worth considering wave picking. It greatly optimizes walking distance and increases picking efficiency.</p>
    </div>
    </div>
    </li>
                            ', $lang) !!}
                            {!! CustomSiteTranslator::get('
<li><h5>Wave Picking</h5>
<br>
<div><p>The warehouse worker (or a group of workers) picks multiple orders at once.</p>

    <div style="text-align: center;"><img src="/img/docs/assembling_by_wave.jpg" style="border-radius: 12px; box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.3); max-width: 100%; height: auto;"></div>
    <br>

<p><b>How does it work?</b></p>

<p>The WMS selects specific orders from the entire pool using a set of configurable filters — for example, by delivery service, the number of items in each order, and many other criteria. It then generates a combined list of items that need to be picked. We call this combined list a wave.</p>

<p>The more orders included in a wave, the less walking is required by the warehouse workers. They no longer need to visit the same aisles and shelves repeatedly to pick the same items for different orders.</p>

<p>Now, a single walk down an aisle is enough to gather goods for dozens — or even hundreds — of orders.</p>
</div>
</li>
                            ', $lang) !!}
                            {!! CustomSiteTranslator::get('
<div class="alert alert-success" role="alert">
<p><b style="color: green;">Pros:</b> Significantly reduces walking time for workers and increases the speed of order picking.</p>
</div>

<div class="alert alert-danger" role="alert">
<p><b style="color: red;">Cons:</b></p>
<ol>
    <li>This picking method is not optimal for all situations. It works best when you have: a large volume of orders, a small number of items per order, and relatively compact goods.</li>
    <li>After the system generates the wave (a combined list of items), you still need to sort these items back into their respective orders — after all, you need to pack exactly what each customer ordered.</li>
</ol>
</div>

<p>And this brings us to the next stage — sorting — which we’ll cover in the next section!</p>
</li>
</ol>
                            ', $lang) !!}
                        </div>

                        <br>
                        <div class="d-flex justify-content-between"
                             style="border-top: 1px dotted #000000; margin-top: 10px; padding-top: 10px;">
                            <button type="button" class="btn btn-outline-primary"
                                    onClick="window.location.href='{{ $lang_str }}/docs/theory/theory_again'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-arrow-left" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                          d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                                </svg>
                                {{ CustomSiteTranslator::get("A bit of theory again", $lang) }}
                            </button>

                            <button type="button" class="btn btn-outline-primary"
                                    onClick="window.location.href='{{ $lang_str }}/docs/theory/sorting_goods'">
                                {{ CustomSiteTranslator::get("Sorting goods", $lang) }}
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