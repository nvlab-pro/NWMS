@php use App\Services\CustomSiteTranslator; @endphp
@include('Site.MainBlocks.header')

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
                        <h2>{{ CustomSiteTranslator::get('SUPPORT', $lang) }}</h2>
                    </div>
                    <div class="breadcumb-content-menu wow fadeInRight animated">
                        <ul>
                            <li><a href="/">{{ CustomSiteTranslator::get('HOME', $lang) }}<i
                                            class="fas fa-angle-right"></i></a></li>
                            <li><span>{{ CustomSiteTranslator::get('SUPPORT', $lang) }}</span></li>
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
            <div class="col-lg-12 col-md-12">
                <div class="single-blog-details-box wow fadeInLeft animated">
                    <div class="blog-details-content">

                        <h5>{{ CustomSiteTranslator::get('Support for our WMS is provided in our Telegram community, both by our team and the community itself:', $lang) }}</h5>
                        <br>
                        @if($lang == 'rus')
                            <a href="https://t.me/+xKtGpy-evygyNzQ6"><i class="fab fa-telegram"></i> Telegram</a>
                        @else
                            <a href="https://t.me/+UJ5If6slQXZjYjMy"><i class="fab fa-telegram"></i> Telegram</a>
                        @endif
                        <div style="border-top: 1px solid #AAAAAA; padding-top: 20px; margin-top: 20px;">
                            <h5>{{ CustomSiteTranslator::get('There is also a YouTube channel where training videos about the system are regularly uploaded:', $lang) }}</h5>
                            <br>
                            @if($lang == 'rus')
                                <a href="https://www.youtube.com/@nwms_ru" target="_blank"><i class="fab fa-youtube"></i> YouTube</a>
                            @else
                                <a href="https://www.youtube.com/@nwms_en" target="_blank"><i class="fab fa-youtube"></i> YouTube</a>
                            @endif
                            <br><br>
                            {{ CustomSiteTranslator::get('Don’t forget to subscribe!', $lang) }}
                        </div>

                        <div style="border-top: 1px solid #AAAAAA; padding-top: 20px; margin-top: 20px;">
                            {{ CustomSiteTranslator::get('If the free community-based support is not sufficient for your needs, you can contact us via our Telegram channel to discuss priority support options.', $lang) }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!--==================================================-->
<!-- End techo blog-details-section -->
<!--==================================================-->


@include('Site.MainBlocks.footer')