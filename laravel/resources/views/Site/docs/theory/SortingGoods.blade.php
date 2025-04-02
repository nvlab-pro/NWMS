@php
    use App\Services\CustomSiteTranslator;
    $header = CustomSiteTranslator::get("Sorting goods", $lang);
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
                        <img src="/img/docs/sorting_wall.jpg" alt="">
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
<h2>Sorting Goods</h2>
<br>
<p>Sorting is typically done using a sorting wall — a special cart or rack where each individual bin is reserved for a specific order.
That is, a warehouse worker takes an item from the picked batch, scans it, and the WMS tells them: “Place this item in bin number 31.”
</p>

<p>By scanning each item in the wave, the system transforms N items into M distinct orders. Each bin will then contain the full contents of a single, specific order, making them ready for packing with no confusion.</p>

                            ', $lang) !!}
                            {!! CustomSiteTranslator::get('
<p>There are two main types of goods sorting:</p>
<ol>
    <li><h5>Sorting during picking using a sorting trolley</h5>
    <br>
    <div>
    <p>In this case, the worker brings a small sorting wall (trolley) with them. After picking an item from the shelf, they scan it, and the WMS immediately tells them which bin to place the item into.</p>
    <div style="text-align: center;"><img src="/img/docs/sorting_trolley.jpg" style="border-radius: 12px; box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.3); max-width: 100%; height: auto;"></div>
    <br>
    <div class="alert alert-success" role="alert">
    <p><b style="color: green;">Pros:</b> the picking and sorting processes are combined, which saves warehouse space (no need for a dedicated sorting area) and increases speed (one scan handles two processes).
    The sorted goods go directly from the picker to the packer.</p>
    </div>
    <div class="alert alert-danger" role="alert">
    <p><b style="color: red;">Cons:</b></p>
    <ol>
        <li>Only suitable for small-sized goods.</li>
        <li>The worker has to move around with a fairly large and often heavy structure.</li>
        <li>The number of orders in a single wave is strictly limited by the number of bins in the sorting trolley.</li>
        <li>Only one worker can handle picking for a single wave.</li>
    </ol>
    </div>
    </div>
    </li>
                            ', $lang) !!}
                            {!! CustomSiteTranslator::get('
<li><h5>Sorting as a separate process using a sorting wall</h5>
<br>
<div>
<p>In this case, all pickers bring the collected goods to a designated sorting area — a large shelving unit with bins sized to fit individual orders. A separate employee then performs the sorting process.</p>
<div style="text-align: center;"><img src="/img/docs/sorting_wall_2.jpg" style="border-radius: 12px; box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.3); max-width: 100%; height: auto;"></div>
<br>

    <div class="alert alert-success" role="alert">
<p><b style="color: green;">Pros:</b></p>
<ol>
    <li>The wave size (number of orders it includes) can be significantly larger.</li>
    <li>Multiple employees can work on picking a single wave simultaneously (e.g., each one responsible for their own aisle). This significantly speeds up the process!</li>
    <li>This introduces an additional layer of verification — allowing time to resolve any issues without blocking the workflow.</li>
    <li>Strategically placing the sorting wall can physically separate long-term storage from the processing zone, which benefits process clarity and product safety.</li>
    <li>It provides substantial buffering of goods — but more on that in the next section.</li>
</ol>
</div>
    <div class="alert alert-danger" role="alert">
<p><b style="color: red;">Cons:</b></p>
<ol>
    <li>It introduces another process, which means more staff and additional time spent on sorting. And time = money.</li>
    <li>Requires a dedicated area in the warehouse. In some cases, space can be a critical issue.</li>
</ol>
</div>
</div>
</div>
</li>
</ol>

                            ', $lang) !!}
                            {!! CustomSiteTranslator::get('
<p>As you can see, the sorting process has its own pros and cons — there is no universal solution!</p>

<p>The warehouse manager must act like a skilled juggler, using different WMS tools to ensure optimal and uninterrupted processes throughout the warehouse.</p>

<p>For some clients (based on product types or storage locations), single-order picking will be the best fit. In other cases, picking with a sorting trolley may be more efficient. And sometimes, sorting needs to be handled as a separate process altogether. The wave depth, along with other settings, should also be configured individually!</p>

<p>The WMS simply provides a set of flexible tools to build and customize these workflows — but it’s up to the warehouse management team to decide how to use them effectively.</p>

<p>So let’s now dive into that topic:</p>
                            ', $lang) !!}
                        </>

                        <br>
                        <div class="d-flex justify-content-between"
                             style="border-top: 1px dotted #000000; margin-top: 10px; padding-top: 10px;">
                            <button type="button" class="btn btn-outline-primary"
                                    onClick="window.location.href='{{ $lang_str }}/docs/theory/assembling_the_order'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-arrow-left" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                          d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                                </svg>
                                {{ CustomSiteTranslator::get("Assembling the order", $lang) }}
                            </button>

                            <button type="button" class="btn btn-outline-primary"
                                    onClick="window.location.href='{{ $lang_str }}/docs/theory/continuity_processes'">
                                {{ CustomSiteTranslator::get("Continuity of Warehouse Processes", $lang) }}
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