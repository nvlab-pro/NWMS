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
                        <h2>{{ CustomSiteTranslator::get('Create account', $lang) }}</h2>
                    </div>
                    <div class="breadcumb-content-menu wow fadeInRight animated">
                        <ul>
                            <li><a href="/">{{ CustomSiteTranslator::get('HOME', $lang) }}<i class="fas fa-angle-right"></i></a></li>
                            <li><span>{{ CustomSiteTranslator::get('CREATE ACCOUNT', $lang) }}</span></li>
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
                        @if($result == 'success')
                            <div class="alert alert-success">
                                {{ CustomSiteTranslator::get('Account created successfully!', $lang) }}
                            </div>
                            <br>
                            <H3>{{ CustomSiteTranslator::get('Please follow the link:', $lang) }}</H3>
                            <a href="https://nwms.cloud/admin" target="_blank">https://nwms.cloud/admin</a>
                            <br><br>
                            {{ CustomSiteTranslator::get('To log in, use the details you provided.', $lang) }}

                            <div class="bar-main">
                                <div class="bar bar-big"></div>
                            </div>

                            <H6>{{ CustomSiteTranslator::get('If you have any questions about launching or configuring', $lang) }} <b>NWMS</b>, you can ask them on our telegram channel:', $lang) }}</H6>
                            <a href="https://t.me/+UJ5If6slQXZjYjMy" target="_blank">https://t.me/+UJ5If6slQXZjYjMy</a>
                        @elseif($result == 'exists')
                            <div class="alert alert-warning">
                                {{ CustomSiteTranslator::get('An account with this email address already exists.', $lang) }}
                            </div>
                        @elseif($result == 'spam')
                            <div class="alert alert-danger">
                                {{ CustomSiteTranslator::get('Your request has been recognized as spam!', $lang) }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ CustomSiteTranslator::get($error, $lang) }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
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