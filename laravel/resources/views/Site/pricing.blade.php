@php use App\Services\CustomSiteTranslator; @endphp
@include('Site.MainBlocks.header')

<!--==================================================-->
<!-- Start techo Main Menu  -->
<!--==================================================-->
@include('Site.MainBlocks.menu')
<!--==================================================-->
<!-- End techo Main Menu  -->
<!--==================================================-->

@php
    $lang_str = '/' . $lang;
    if ($lang == 'en') $lang_str = '';
@endphp

<!--==================================================-->
<!-- Start techo Breadcumb Area -->
<!--==================================================-->
<div class="breadcumb-area align-items-center d-flex">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcumb-content">
                    <div class="breadcumb-title wow fadeInLeft animated">
                        <h2>{{ CustomSiteTranslator::get('PRICING', $lang) }}</h2>
                    </div>
                    <div class="breadcumb-content-menu wow fadeInRight animated">
                        <ul>
                            <li><a href="/">{{ CustomSiteTranslator::get('HOME', $lang) }}<i class="fas fa-angle-right"></i></a></li>
                            <li><span>{{ CustomSiteTranslator::get('PRICING', $lang) }}</span></li>
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
                        <H3>{{ CustomSiteTranslator::get('First and foremost, the program is provided', $lang) }} <span style="color: red;">{{ CustomSiteTranslator::get('free of charge', $lang) }}</span>!</H3>
                        <br>
                        {!! CustomSiteTranslator::get('You can use it for both personal and commercial purposes <b>completely free of charge</b>.', $lang) !!}<br>
                        <br>
                        {!! CustomSiteTranslator::get('This applies to both regular sellers managing their own stock in a warehouse, as well as service companies providing fulfillment and storage services.', $lang) !!}
                        <div style="border-top: 1px solid #AAAAAA; padding-top: 20px; margin-top: 20px;">
                            {!! CustomSiteTranslator::get('We also offer free assistance to help you get started and set up the software!
You’ll find <a href="'.$lang_str.'/support" style="text-decoration: underline;">training materials, video tutorials, and our Telegram channel</a> where you can get answers to your questions and connect with other WMS users.<br>
<br>
However, if you need more personalized support, tighter deadlines, or a higher level of service — you can always opt for our paid services:
', $lang) !!}
                            <br>
                            <H3>{{ CustomSiteTranslator::get('What costs money and how much?', $lang) }}</H3>
                            <br>
                            <h4>{{ CustomSiteTranslator::get('The following services are provided for a fee:', $lang) }}</h4>
                            <p>
                            <ul style="padding-left: 40px;">
                                <li style="padding-bottom: 20px;"><b>{{ CustomSiteTranslator::get('Demonstration of the system', $lang) }}</b><br>
                                    <br>
                                    {!! CustomSiteTranslator::get("You can explore the program at any time using our <a href='".$lang_str."/#demo' style='text-decoration: underline;'>demo version</a> or <a href='".$lang_str."/#form' style='text-decoration: underline;'>request a personal account for full access</a>.
However, sometimes that's not enough.<br>
<br>
In this case, we can offer you a personalized product presentation — typically a Google Meet session lasting about an hour.
We’ll walk you through the features, share our roadmap, and answer all your questions.<br>
<br>
This presentation is paid. Please check the current price in our Telegram group.", $lang) !!}</li>

                                <li style="padding-bottom: 20px;"><b>Onboarding</b> – {{ CustomSiteTranslator::get('that is, assistance with system setup.', $lang) }} <br>

                                    <br>
                                    {{ CustomSiteTranslator::get('During the launch period (usually from 1 week to 1 month), a dedicated manager will be assigned to you who will guide your warehouse step by step through all stages. They will consult and assist you throughout the process.', $lang) }}</li>

                                <li style="padding-bottom: 20px;"><b>{{ CustomSiteTranslator::get('Priority Support', $lang) }}</b><br>
                                    <br>
                                    {{ CustomSiteTranslator::get('WMS support is provided via our Telegram channel on a general basis. If you would like to have a dedicated manager who will assist you with any WMS-related questions on a priority basis, contact us and we will discuss the terms.', $lang) }}</li>

                                <li style="padding-bottom: 20px;"><b>{{ CustomSiteTranslator::get('Custom Functionality', $lang) }}</b> <br>
                                    <br>
                                    {{ CustomSiteTranslator::get('If the current WMS functionality does not meet your needs, we can develop the necessary modules for you, or prioritize their development if such functionality is already in our roadmap.', $lang) }}</li>

                                <li style="padding-bottom: 20px;"><b>{{ CustomSiteTranslator::get('Dedicated Version', $lang) }}</b> <br>
                                    <br>
                                    {{ CustomSiteTranslator::get('If you require significant customization of the project (interface or functionality), we can either deploy a separate instance in our cloud or sell you the source code for independent development and support.', $lang) }}</li>
                            </ul>

                            {{ CustomSiteTranslator::get('We are also open to discussing any other questions related to our project.', $lang) }} <br>
                            {{ CustomSiteTranslator::get('Just send your question in our Telegram channel and we’ll get back to you!', $lang) }}

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