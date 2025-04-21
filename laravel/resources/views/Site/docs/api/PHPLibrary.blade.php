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
                        <img src="/img/docs/give_me_finish_mult.jpg" alt="">
                    </div>
                    <div class="blog-details-content">
                        <div class="blog-details-meta">
                            <div class="techo-blog-meta-left">
                                <span>API</span>
                                17.04.2025
                            </div>
                        </div>
                        <div class="blog-details-content-text">
                            <section id="nwms-api-client">
                                <h3>NWMS PHP API Client</h3>
                                <br>
                                <p>This PHP class allows clients to interact with the NWMS API using a simple and straightforward interface. It supports authentication, data submission, and receiving responses from your NWMS system.</p>

                                <h4>Installation</h4>
                                <br>
                                <p>Download the <code>NWMSApi.php</code> file and include it in your project:</p>

                                <pre><code class="language-php">&lt;?php
require 'NWMSApi.php';
</code></pre>
                                <br>
                                <h4>Usage Example</h4>
                                <br>
                                <p>Create an instance of the client using your login credentials and API base URL:</p>

                                <pre><code class="language-php">&lt;?php
$api = new NWMSApi('your_email@example.com', 'your_password', 'https://nwms.cloud/api');

$api-&gt;setRequest([
    'sku' =&gt; 'TEST-001',
    'name' =&gt; 'Sample Product',
    'quantity' =&gt; 10,
])-&gt;request('POST', '/products');

$response = $api-&gt;getDecodedResult();
print_r($response);
</code></pre>
                                <br>
                                <h4>Full Client Code</h4>
                                <br>
                                <p>Here's the full source code of the <code>NWMSApi</code> class:</p>

                                <pre><code class="language-php">&lt;?php

class NWMSApi
{
    protected string $token = '';
    protected string $baseUrl;
    protected string $json = '';
    protected string $result = '';

    public function __construct(string $email, string $password, string $baseUrl = null)
    {
        $this-&gt;baseUrl = rtrim($baseUrl ?? 'https://nwms.cloud/api', '/');
        $this-&gt;authenticate($email, $password);
    }

    protected function authenticate(string $email, string $password): void
    {
        $data = json_encode([
            'email' =&gt; $email,
            'password' =&gt; $password,
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this-&gt;baseUrl . '/login');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $decoded = json_decode($response, true);

        if (isset($decoded['access_token'])) {
            $this-&gt;token = $decoded['access_token'];
        } else {
            throw new Exception('Authentication failed: ' . ($decoded['message'] ?? 'Unknown error'));
        }
    }

    public function setRequest(array $data): static
    {
        $this-&gt;json = json_encode($data);
        return $this;
    }

    public function setJsonRequest(string $json): static
    {
        $this-&gt;json = $json;
        return $this;
    }

    public function getRequest(): string
    {
        return $this-&gt;json;
    }

    public function request(string $method, string $url): static
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this-&gt;baseUrl . $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

        if (!empty($this-&gt;json)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this-&gt;json);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this-&gt;token,
            'Content-Type: application/json',
            'Accept: application/json',
        ]);

        $this-&gt;result = curl_exec($ch);
        curl_close($ch);

        return $this;
    }

    public function getResult(): string
    {
        return $this-&gt;result;
    }

    public function getDecodedResult(): mixed
    {
        return json_decode($this-&gt;result, true);
    }
}
</code></pre>
                                <br>
                                <h4>Need help?</h4>
                                <br>
                                <p>If you encounter any issues using the NWMS API client, please contact our support team or refer to the full API documentation.</p>
                            </section>

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