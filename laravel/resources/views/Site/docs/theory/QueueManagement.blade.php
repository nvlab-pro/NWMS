@php
    use App\Services\CustomSiteTranslator;
    $header = CustomSiteTranslator::get("Queue management", $lang);
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
                        <img src="/img/docs/QueueManagement_mult.jpg" alt="">
                    </div>
                    <div class="blog-details-content">
                        <div class="blog-details-meta">
                            <div class="techo-blog-meta-left">
                                <span>{{ CustomSiteTranslator::get('theory', $lang) }}</span>
                                10.04.2025
                            </div>
                        </div>
                        <div class="blog-details-content-text">
                            {!! CustomSiteTranslator::get("
<h2>Queue Management</h2>
<br>
<p>
So, you've received goods at the warehouse, assigned them to storage locations, received orders, and are ready to start picking and shipping — but how do you manage the picking process?
Which warehouse worker will get which order? In what order?
</p>

<p>
Let’s take a look at the tools that allow a warehouse manager to control the processes of picking, packing, labeling, and shipping goods and orders.
</p>

<p>
First of all, why might you need this?
</p>
                            ", $lang) !!}
                            {!! CustomSiteTranslator::get("
<p>
<ol>
    <li>
        You may need to ship orders with a specific shipping date (e.g., only today's orders).
    </li>
    <li>
        A specific delivery service. For example, a particular courier service is arriving in an hour to collect orders, while others will come later—so you urgently need to pick orders for them first.
    </li>
    <li>
        You may need to pick specific orders with known order numbers.
    </li>
</ol>
</p>

                            ", $lang) !!}
                            {!! CustomSiteTranslator::get("
<p>And of course, there are many other possible scenarios!</p>

<p>
For example, in my own experience, there was a case where we needed to pick only orders with a small number of items to quickly boost picking performance and present better metrics to the client.
The warehouse was receiving far more orders than we could physically handle, and we needed to calm the client down—at least temporarily.
We had to bend the rules a little! ;)
</p>

<p>
This is exactly where <b>queues</b> come into play to help manage such processes.
</p>

                            ", $lang) !!}
                            {!! CustomSiteTranslator::get("
<p>
A queue is essentially a set of filters that acts like a sieve—allowing certain orders to pass through while blocking others.
You can have multiple queues, each with its own unique configuration.
</p>

<p>
By managing these queues (activating or deactivating them), you can control the flow of orders within the warehouse.
</p>

<p>
We'll take a closer look at how to do this in practice when we get to working directly in <b>NWMS</b>.
</p>

                            ", $lang) !!}
                        </div>

                        <br>
                        <div class="d-flex justify-content-between" style="border-top: 1px dotted #000000; margin-top: 10px; padding-top: 10px;">
                            <button type="button" class="btn btn-outline-primary"
                                    onClick="window.location.href='{{ $lang_str }}/docs/theory/dispatch_of_orders'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                                </svg>
                                {{ CustomSiteTranslator::get("Order Shipment", $lang) }}
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