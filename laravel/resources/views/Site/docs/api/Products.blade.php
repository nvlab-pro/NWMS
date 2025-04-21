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
                                <span>API</span>
                                17.04.2025
                            </div>
                        </div>
                        <div class="blog-details-content-text">

                            <h3>NWMS API - Product Management</h3>
                            <br>
                            <p>This section describes how to manage products (offers) using the NWMS API. It includes retrieving, creating, updating, and deleting products.</p>

                            <h4>Available Endpoints</h4>
                            <br>
                            <ul>
                                <li><strong>GET /api/products</strong> – List products (with filters, sorting, pagination)</li>
                                <li><strong>POST /api/products</strong> – Create a product</li>
                                <li><strong>GET /api/products/{id}</strong> – Get product by ID</li>
                                <li><strong>PUT /api/products/{id}</strong> – Update product</li>
                                <li><strong>DELETE /api/products/{id}</strong> – Soft delete product</li>
                            </ul>

                            <h4>Example: Get products with filters and sorting</h4>
                            <br>
                            <pre><code class="language-http">GET /api/products?sku=ABC123&sort_by=of_price&sort_dir=desc&per_page=10
Authorization: Bearer YOUR_ACCESS_TOKEN</code></pre>

                            <h2>Example: Create product</h2>
                            <pre><code class="language-http">POST /api/products
Authorization: Bearer YOUR_ACCESS_TOKEN
Content-Type: application/json

{
  "of_name": "Test Product",
  "of_status": 1,
  "of_sku": "SKU123",
  "of_price": 199.99
}</code></pre>

                            <h2>Response Format</h2>
                            <p>All responses use the <code>OfferResource</code> to format output:</p>
                            <pre><code class="language-json">{
  "id": 1,
  "name": "Test Product",
  "sku": "SKU123",
  "article": null,
  "price": 199.99,
  "status": 1,
  "shop_id": 2,
  "domain_id": 1,
  "dimensions": {
    "x": null,
    "y": null,
    "z": null
  },
  "weight": null,
  "image": null,
  "comment": null,
  "created_at": "2025-04-17T14:00:00.000000Z",
  "updated_at": "2025-04-17T14:00:00.000000Z"
}</code></pre>

                            <h2>Authorization</h2>
                            <p>All endpoints require a valid Bearer token obtained via the login API.</p>

                        </div>

                        <!-- Highlight CSS -->
                        <link rel="stylesheet"
                              href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/default.min.css">

                        <!-- Highlight JS -->
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/php.min.js"></script>
                        <script>hljs.highlightAll();</script>




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