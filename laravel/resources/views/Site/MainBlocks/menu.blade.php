@php use App\Services\CustomSiteTranslator; @endphp
<style>
    .nav_scroll a.active {
        font-weight: bold;
        text-decoration: underline;
        color: #0D5ADB;
    }
</style>
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
                    @php
                        $currentPath = '/' . Request::path(); // например: 'en/pricing'
                    @endphp

                    <ul class="nav_scroll">
                        <li><a href="{{ $lang_str }}/" class="{{ $currentPath === $lang_str || $currentPath === $lang_str.'/' ? 'active' : '' }}">
                                {{ CustomSiteTranslator::get('Start', $lang) }}</a></li>

                        <li><a href="{{ $lang_str }}/about_wms" class="{{ str_starts_with($currentPath, $lang_str.'/about_wms') ? 'active' : '' }}">
                                {{ CustomSiteTranslator::get('About WMS', $lang) }}</a></li>

                        <li><a href="{{ $lang_str }}/pricing" class="{{ str_starts_with($currentPath, $lang_str.'/pricing') ? 'active' : '' }}">
                                {{ CustomSiteTranslator::get('Pricing', $lang) }}</a></li>

                        <li><a href="{{ $lang_str }}/support" class="{{ str_starts_with($currentPath, $lang_str.'/support') ? 'active' : '' }}">
                                {{ CustomSiteTranslator::get('Support', $lang) }}</a></li>

                        <li><a href="{{ $lang_str }}/docs/theory" class="{{ str_starts_with($currentPath, $lang_str.'/docs/theory') ? 'active' : '' }}">
                                {{ CustomSiteTranslator::get('Education', $lang) }}</a></li>
                    </ul>                 <!-- header button -->
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
<div class="mobile-menu-area sticky d-sm-block d-md-block d-lg-none">
    <div class="mobile-menu">
        <nav class="techo_menu">
            <ul class="nav_scroll">
                <li><a href="#">{{ CustomSiteTranslator::get('Change language', $lang) }}</a>
                    <ul class="sub-menu">
                        <li><a href="/bg">🇧🇬 Bulgarian</a></li>
                        <li><a href="/ch">🇨🇳 Chinese</a></li>
                        <li><a href="/en">🇺🇸 English</a></li>
                        <li><a href="/de">🇩🇪 German</a></li>
                        <li><a href="/gr">🇬🇪 Georgian</a></li>
                        <li><a href="/it">🇮🇹 Italian</a></li>
                        <li><a href="/jp">🇯🇵 Japanese</a></li>
                        <li><a href="/kz">🇰🇿 Kazakh</a></li>
                        <li><a href="/kl">🛸 Klingon</a></li>
                        <li><a href="/pr">🇵🇹 Portuguese</a></li>
                        <li><a href="/ro">🇷🇴 Romanian</a></li>
                        <li><a href="/rus">🇷🇺 Russian</a></li>
                        <li><a href="/sp">🇪🇸 Spanish</a></li>
                        <li><a href="/tr">🇹🇷 Turkish</a></li>
                        <li><a href="/ukr">🇺🇦 Ukrainian</a></li>
                        <li><a href="/bel">🇧🇾 Belarusian</a></li>
                    </ul>
                </li>
                <li><a href="{{ $lang_str }}/">{!! CustomSiteTranslator::get('Start', $lang) !!}</a></li>
                <li><a href="{{ $lang_str }}/about_wms">{!! CustomSiteTranslator::get('About WMS', $lang) !!}</a></li>
                <li><a href="{{ $lang_str }}/pricing">{!! CustomSiteTranslator::get('Pricing', $lang) !!}</a></li>
                <li><a href="{{ $lang_str }}/support">{!! CustomSiteTranslator::get('Support', $lang) !!}</a></li>
                <li><a href="{{ $lang_str }}/docs/theory">{!! CustomSiteTranslator::get('Education', $lang) !!}</a></li>
                <li><a href="/admin">Log in</a></li>
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