@php
    use App\Services\CustomSiteTranslator;
    $header = CustomSiteTranslator::get("NWMS PHP API Client", $lang);
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
                        <img src="/img/docs/programmer_mult.jpg" alt="">
                    </div>
                    <div class="blog-details-content">
                        <div class="blog-details-meta">
                            <div class="techo-blog-meta-left">
                                <span>API</span>
                                17.04.2025
                            </div>
                        </div>
                        <div class="blog-details-content-text">
                            <div class="box">
                                <h3>NWMS API Authentication Guide</h3>
                                <br>
                                <p>The NWMS API uses <strong>Bearer Token Authentication</strong> based on <strong>Laravel Sanctum</strong>. You must include a valid token with each request to protected endpoints.</p>
                            </div>

                            <div class="box">
                                <h4>Step 1: Obtain Access Token</h4>
                                <br>
                                <p>Send a <code>POST</code> request to <code>/api/login</code>:</p>
                                <pre><code>POST /api/login
Content-Type: application/json

{
  "email": "your@email.com",
  "password": "your_password"
}
</code></pre>

                                <p><strong>Successful response:</strong></p>
                                <pre><code>{
  "access_token": "1|your_generated_token",
  "token_type": "Bearer"
}</code></pre>
                            </div>

                            <div class="box">
                                <br>
                                <h4>Step 2: Use the Token</h4>
                                <br>
                                <p>For all authenticated requests, add the following HTTP header:</p>
                                <pre><code>Authorization: Bearer 1|your_generated_token</code></pre>
                                <p>In Swagger UI, click the <strong>Authorize</strong> button and paste the token like this:</p>
                                <pre><code>Bearer 1|your_generated_token</code></pre>
                            </div>

                            <div class="box">
                                <br>
                                <h4>Logout</h4>
                                <br>
                                <p>To revoke the current token:</p>
                                <pre><code>POST /api/logout
Authorization: Bearer 1|your_generated_token</code></pre>
                            </div>

                            <div class="box">
                                <br>
                                <h4>Notes</h4>
                                <br>
                                <ol>
                                    <li>Tokens are tied to the authenticated user.</li>
                                    <li>Stored in <code>personal_access_tokens</code> table.</li>
                                    <li>Revocable and secure per-session tokens.</li>
                                    <li>Access to data is restricted by user role and domain/shop scope.</li>
                                </ol>
                            </div>

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