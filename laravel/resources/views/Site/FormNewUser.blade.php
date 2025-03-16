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
                        <h2>Create account</h2>
                    </div>
                    <div class="breadcumb-content-menu wow fadeInRight animated">
                        <ul>
                            <li><a href="/">HOME<i class="fas fa-angle-right"></i></a></li>
                            <li><span>CREATE ACCOUNT</span></li>
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
                                Account created successfully!
                            </div>
                            <br>
                            <H3>Please follow the link:</H3>
                            <a href="https://nwms.cloud/admin" target="_blank">https://nwms.cloud/admin</a>
                            <br><br>
                            To log in, use the details you provided.

                            <div class="bar-main">
                                <div class="bar bar-big"></div>
                            </div>

                            <H6>If you have any questions about launching or configuring <b>NWMS</b>, you can ask them on our telegram channel:</H6>
                            <a href="https://t.me/+UJ5If6slQXZjYjMy" target="_blank">https://t.me/+UJ5If6slQXZjYjMy</a>
                        @elseif($result == 'exists')
                            <div class="alert alert-warning">
                                An account with this email address already exists.
                            </div>
                        @elseif($result == 'spam')
                            <div class="alert alert-danger">
                                Your request has been recognized as spam!
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
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