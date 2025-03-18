@php use App\Services\CustomSiteTranslator; @endphp
@include('Site.MainBlocks.header', ['lang' => $lang])

<!--==================================================-->
<!-- Start techo Main Menu  -->
<!--==================================================-->
@include('Site.MainBlocks.menu', ['lang' => $lang])
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
                        <h2>{{ CustomSiteTranslator::get('Terms of Service', $lang) }}</h2>
                    </div>
                    <div class="breadcumb-content-menu wow fadeInRight animated">
                        <ul>
                            <li><a href="/">{{ CustomSiteTranslator::get('MAIN', $lang) }}<i
                                            class="fas fa-angle-right"></i></a></li>
                            <li><span>{{ CustomSiteTranslator::get('Terms of Service', $lang) }}</span></li>
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
<!-- start techo Privacy Policy Area -->
<!--==================================================-->
<div class="privacy-policy-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 wow fadeInLeft animated">
                <div class="privacy-policy-title">
                    <h2>{{ CustomSiteTranslator::get('1. Acceptance of Terms', $lang) }}</h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                        {{ CustomSiteTranslator::get('By using NWMS.CLOUD (hereinafter referred to as the "Service"), you confirm that you have read
                        and agree to these Terms of Service (hereinafter referred to as the "Terms").
                        If you do not agree to these Terms, you must immediately stop using the Service.', $lang) }}
                    </p>
                </div>

                <div class="privacy-policy-title">
                    <h2>{{ CustomSiteTranslator::get('2. Description of Service', $lang) }}</h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                        {{ CustomSiteTranslator::get('NWMS.CLOUD provides a free warehouse management system (WMS) as a software-as-a-service (SaaS).
                        The Service may include, but is not limited to, the following features: inventory management,
                        order processing, reporting, and analytics.', $lang) }}<br>
                        <br>
                        {{ CustomSiteTranslator::get('The functionality of the Service may be updated or changed without prior notice.', $lang) }}
                    </p>
                </div>

                <div class="privacy-policy-title">
                    <h2>{{ CustomSiteTranslator::get('3. Use of Service', $lang) }}</h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                    <ul style="padding-left: 40px;">
                        <li> {{ CustomSiteTranslator::get('You must use the Service in accordance with these Terms and applicable laws.', $lang) }}</li>
                        <li> {{ CustomSiteTranslator::get('You must not use the Service for any illegal or improper purposes.', $lang) }}</li>
                        <li> {{ CustomSiteTranslator::get('You must not attempt to gain unauthorized access to the Service or its systems.', $lang) }}</li>
                        <li> {{ CustomSiteTranslator::get('You must not interfere with the operation of the Service or attempt to circumvent its
                            security.', $lang) }}
                        </li>
                        <li> {{ CustomSiteTranslator::get('You must not abuse the system’s resources, including but not limited to, excessive use of
                            storage, processing power, or bandwidth. The Service is intended for reasonable and fair
                            use.', $lang) }}<br><br>
                            {{ CustomSiteTranslator::get('If your usage exceeds what is considered normal (e.g., uploading millions of items or
                            performing excessive automated requests), the Service administration reserves the right to
                            limit or suspend access.', $lang) }}
                        </li>
                        <li> {{ CustomSiteTranslator::get('Violation of these rules may result in temporary or permanent blocking of your account.', $lang) }}
                        </li>
                    </ul>
                    </p>
                </div>

                <div class="privacy-policy-title">
                    <h2>{{ CustomSiteTranslator::get('4. User Account', $lang) }}</h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                    <ul style="padding-left: 40px;">
                        <li> {{ CustomSiteTranslator::get('To use the Service, you may need to create a user account.', $lang) }}</li>
                        <li> {{ CustomSiteTranslator::get('You are responsible for maintaining the confidentiality of your account and password.', $lang) }}</li>
                        <li> {{ CustomSiteTranslator::get('You are responsible for all activities that occur under your account.', $lang) }}</li>
                        <li> {{ CustomSiteTranslator::get('The administration reserves the right to temporarily block or delete a user account in case of
                        violation of these Terms.', $lang) }}</li>
                    </ul>
                    </p>
                </div>

                <div class="privacy-policy-title">
                    <h2>{{ CustomSiteTranslator::get('5. Intellectual Property', $lang) }}</h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                    <ul style="padding-left: 40px;">
                        <li> {{ CustomSiteTranslator::get('The Service and its content are protected by copyright and other intellectual property laws.', $lang) }}</li>
                        <li> {{ CustomSiteTranslator::get('Users may use the content of the Service for personal and commercial purposes.', $lang) }}</li>
                        <li> {{ CustomSiteTranslator::get('You must not copy, distribute, modify, or create derivative works based on the Service or its
                        content without our express permission.', $lang) }}</li>
                    </ul>
                    </p>
                </div>

                <div class="privacy-policy-title">
                    <h2>{{ CustomSiteTranslator::get('6. Disclaimer of Warranty', $lang) }}</h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                    <ul style="padding-left: 40px;">
                        <li> {{ CustomSiteTranslator::get('The Service is provided "as is" and "as available."', $lang) }}</li>
                        <li> {{ CustomSiteTranslator::get('We make no warranties regarding the Service, including, but not limited to, warranties of
                        fitness for a particular purpose and non-infringement.', $lang) }}</li>
                        <li> {{ CustomSiteTranslator::get('We are not responsible for any losses or damages arising from your use of the Service.', $lang) }}</li>
                    </ul>
                    </p>
                </div>

                <div class="privacy-policy-title">
                    <h2>{{ CustomSiteTranslator::get('7. Changes to Terms', $lang) }}</li></h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                    <ul style="padding-left: 40px;">
                        <li> {{ CustomSiteTranslator::get('We may change these Terms from time to time.', $lang) }}</li>
                        <li> {{ CustomSiteTranslator::get('We will notify you of any changes by posting the new Terms on the Service.', $lang) }}</li>
                        <li> {{ CustomSiteTranslator::get('Your continued use of the Service after any changes constitutes your acceptance of the new
                        Terms.', $lang) }}</li>
                    </ul>
                    </p>
                </div>

                <div class="privacy-policy-title">
                    <h2>{{ CustomSiteTranslator::get('8. Termination', $lang) }}</li></h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                        {{ CustomSiteTranslator::get('We may terminate your access to the Service at any time and for any reason.', $lang) }}
                    </p>
                </div>

                <div class="privacy-policy-title">
                    <h2>{{ CustomSiteTranslator::get('9. Governing Law', $lang) }}</h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                    <ul style="padding-left: 40px;">
                        <li> {{ CustomSiteTranslator::get('These Terms are governed by and construed in accordance with the laws of Bulgaria.', $lang) }}</li>
                        <li> {{ CustomSiteTranslator::get('Any disputes arising from these Terms shall be resolved in the courts of Bulgaria.', $lang) }}</li>
                    </ul>
                    </p>
                </div>

                <div class="privacy-policy-title">
                    <h2>{{ CustomSiteTranslator::get('10. Contact Information', $lang) }}</h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                        {{ CustomSiteTranslator::get('If you have any questions about these Terms, please contact us at info@nwms.cloud.', $lang) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!--==================================================-->
<!-- End techo Privacy Policy Area -->
<!--==================================================-->


<!--==================================================-->
<!-- END techo Testimonial Area -->
<!--==================================================-->

@include('Site.MainBlocks.footer', ['lang' => $lang])