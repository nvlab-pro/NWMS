@php use App\Services\CustomSiteTranslator; @endphp
<div id="sticky-header" class="techo_nav_manu-two">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-3">
                <div class="logo">
                    @php
                        $lang_str = '/' . $lang;
                        if ($lang == 'en') $lang_str = '';
                    @endphp
                    <a class="logo_img" href="{{ $lang_str }}/" title="techo">
                        <img src="/img/cm_logo.png" alt="logo" style="width:130px">
                    </a>
                    <a class="main_sticky" href="{{ $lang_str }}/" title="techo">
                        <img src="/img/cm_logo.png" alt="logo" style="width:130px">
                    </a>
                </div>
            </div>
            <div class="col-lg-9">
                <nav class="techo_menu-two">
                    <ul class="nav_scroll">
                        <li><a href="{{ $lang_str }}/">{{ CustomSiteTranslator::get('Start', $lang) }}</a></li>
                        <li><a href="{{ $lang_str }}/about_wms">{{ CustomSiteTranslator::get('About WMS', $lang) }}</a></li>
                        <li><a href="{{ $lang_str }}/pricing">{{ CustomSiteTranslator::get('Pricing', $lang) }}</a></li>
                        <li><a href="{{ $lang_str }}/support">{{ CustomSiteTranslator::get('Support', $lang) }}</a></li>
                        <li><a href="{{ $lang_str }}/docs/theory">{{ CustomSiteTranslator::get('Docs', $lang) }}</a></li>
                        <!--
                        <li><a href="/">Pricing</a></li>
                        <li><a href="/">Education</a></li>
                        <li><a href="/">Support</a></li>
                        <li><a href="/">Privacy Policy</a></li>
                        <li><a href="/">Contact</a></li>
                        -->
                    </ul>
                    <!-- header button -->
                    <div class="header-src-btn">
                        <a href="/admin" target="_blank">
                            <div class="search-box-btn search-box-outer">
                                <i class="fas fa-user"></i>
                            </div>
                        </a>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- techo Mobile Menu  -->
<div class="mobile-menu-area sticky d-sm-block d-md-block d-lg-none ">
    <div class="mobile-menu">
        <nav class="techo_menu">
            <ul class="nav_scroll">
                <li><a href="/admin">WMS</a></li>
                <li><a href="{{ $lang_str }}/">{{ CustomSiteTranslator::get('Start', $lang) }}</a></li>
                <li><a href="{{ $lang_str }}/about_wms">{{ CustomSiteTranslator::get('About WMS', $lang) }}</a></li>
                <li><a href="{{ $lang_str }}/pricing">{{ CustomSiteTranslator::get('Pricing', $lang) }}</a></li>
                <li><a href="{{ $lang_str }}/support">{{ CustomSiteTranslator::get('Support', $lang) }}</a></li>
                <li><a href="{{ $lang_str }}/docs/theory">{{ CustomSiteTranslator::get('Docs', $lang) }}</a></li>
                <!--
                <li><a href="/">Pricing</a></li>
                <li><a href="/">Education</a></li>
                <li><a href="/">Support</a></li>
                <li><a href="/">Privacy Policy</a></li>
                <li><a href="/">Contact</a></li>
                -->
            </ul>
        </nav>
    </div>
</div>