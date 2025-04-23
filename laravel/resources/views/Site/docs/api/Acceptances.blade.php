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
                                23.04.2025
                            </div>
                        </div>
                        <div class="blog-details-content-text">

                            <!-- AcceptanceController -->
                            <h3>NWMS API - Acceptance Management</h3>
                            <br>
                            <p>This section describes how to manage acceptances (goods receipts) using the NWMS API. It includes retrieving, creating, updating, and deleting acceptance documents.</p>

                            <h4>Available Endpoints</h4>
                            <br>
                            <div style="background-color: #fff7d4; padding: 10px; border-radius: 5px; border: 1px solid #efde9d;">
                                <ul style="padding-left: 30px;">

                                <li><strong>GET /api/acceptances</strong> – List acceptances (with filters for status, warehouse, supplier, etc., sorting, pagination)</li>
                                <li><strong>POST /api/acceptances</strong> – Create a new acceptance document</li>
                                <li><strong>GET /api/acceptances/{id}</strong> – Get acceptance document by ID</li>
                                <li><strong>PUT /api/acceptances/{id}</strong> – Update an existing acceptance document (e.g., change status, comment)</li>
                                <li><strong>DELETE /api/acceptances/{id}</strong> – Soft delete an acceptance document</li>
                                </ul>
                            </div>

                            <br>
                            <h4>Example: Get acceptances with filters</h4>
                            <br>
                            <pre><code class="php">GET /api/acceptances?status=1&warehouse_id=5&sort_by=created_at&sort_dir=desc
Authorization: Bearer YOUR_ACCESS_TOKEN</code></pre>

                            <h4>Example: Create acceptance</h4>
                            <br>
                            <pre><code class="php">POST /api/acceptances
Authorization: Bearer YOUR_ACCESS_TOKEN
Content-Type: application/json

{
  "warehouse_id": 5,
  "supplier_id": 12,
  "comment": "Scheduled delivery for Monday"
}</code></pre>

                            <h4>Response Format (AcceptanceResource)</h4>
                            <br>
                            <p>Responses for individual acceptances typically use the <code>AcceptanceResource</code>:</p>
                            <pre><code class="language-json">{
  "id": 101,
  "warehouse_id": 5,
  "supplier_id": 12,
  "status": 1, // e.g., 1: New, 2: In Progress, 3: Completed, 4: Cancelled
  "comment": "Scheduled delivery for Monday",
  "created_at": "2025-04-18T10:00:00.000000Z",
  "updated_at": "2025-04-18T10:00:00.000000Z"
}</code></pre>

                            <hr style="margin: 30px 0;">

                            <!-- AcceptanceOfferController -->
                            <h3>NWMS API - Acceptance Offer Management</h3>
                            <br>
                            <p>This section describes how to manage individual product offers within a specific acceptance document using the NWMS API. It includes listing, adding, updating, and removing offers from an acceptance.</p>

                            <h4>Available Endpoints</h4>
                            <br>
                            <ol>
                                <li><strong>GET /api/acceptances/{acceptance_id}/offers</strong> – List offers within a specific acceptance (with filters, sorting, pagination)</li>
                                <li><strong>POST /api/acceptances/{acceptance_id}/offers</strong> – Add an offer (product) to an acceptance</li>
                                <li><strong>GET /api/acceptances/{acceptance_id}/offers/{acceptance_offer_id}</strong> – Get a specific offer within an acceptance by its ID</li>
                                <li><strong>PUT /api/acceptances/{acceptance_id}/offers/{acceptance_offer_id}</strong> – Update an offer within an acceptance (e.g., update received quantity)</li>
                                <li><strong>DELETE /api/acceptances/{acceptance_id}/offers/{acceptance_offer_id}</strong> – Remove an offer from an acceptance</li>
                            </ol>

                            <h4>Example: List offers for an acceptance</h4>
                            <br>
                            <pre><code class="language-http">GET /api/acceptances/101/offers?sku=PROD456
Authorization: Bearer YOUR_ACCESS_TOKEN</code></pre>

                            <h4>Example: Add offer to acceptance</h4>
                            <pre><code class="language-http">POST /api/acceptances/101/offers
Authorization: Bearer YOUR_ACCESS_TOKEN
Content-Type: application/json

{
  "offer_id": 55, // ID of the product (offer) being added
  "quantity_expected": 100,
  "quantity_received": 0 // Initially 0 or can be set if known
}</code></pre>

                            <h4>Example: Update received quantity for an offer</h4>
                            <pre><code class="language-http">PUT /api/acceptances/101/offers/205
Authorization: Bearer YOUR_ACCESS_TOKEN
Content-Type: application/json

{
  "quantity_received": 98
}</code></pre>

                            <h4>Response Format (AcceptanceOfferResource)</h4>
                            <p>Responses for offers within an acceptance typically use the <code>AcceptanceOfferResource</code>:</p>
                            <pre><code class="language-json">{
  "id": 205, // ID of the acceptance_offers record
  "acceptance_id": 101,
  "offer_id": 55,
  "offer_sku": "PROD456", // Included for convenience
  "offer_name": "Example Product", // Included for convenience
  "quantity_expected": 100,
  "quantity_received": 98,
  "created_at": "2025-04-18T10:05:00.000000Z",
  "updated_at": "2025-04-18T11:30:00.000000Z"
}</code></pre>

                            <hr style="margin: 30px 0;">

                            <!-- Common Section -->
                            <h2>Authorization</h2>
                            <p>All endpoints require a valid Bearer token obtained via the login API. The token must be included in the <code>Authorization</code> header.</p>
                            <pre><code class="language-http">Authorization: Bearer YOUR_ACCESS_TOKEN</code></pre>

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