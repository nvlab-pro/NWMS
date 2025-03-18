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
                        <h2>{{ CustomSiteTranslator::get('Privacy Policy', $lang) }}</h2>
                    </div>
                    <div class="breadcumb-content-menu wow fadeInRight animated">
                        <ul>
                            <li><a href="/">{{ CustomSiteTranslator::get('MAIN', $lang) }}<i
                                            class="fas fa-angle-right"></i></a></li>
                            <li><span>{{ CustomSiteTranslator::get('Privacy Policy', $lang) }}</span></li>
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
                    <h2>{{ CustomSiteTranslator::get('Introduction', $lang) }}</h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                        {{ CustomSiteTranslator::get('We, the NWMS.CLOUD team, value your privacy and are committed to protecting your personal data. This Privacy Policy explains how we collect, use, and protect your information when you use our service. This policy complies with the General Data Protection Regulation (GDPR) and other applicable privacy laws.', $lang) }}
                    </p>
                </div>

                <div class="privacy-policy-title">
                    <h2>{{ CustomSiteTranslator::get('Information Collection', $lang) }}</h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                        {{ CustomSiteTranslator::get('We collect the following information:', $lang) }}
                    </p>
                    <ul style="padding-left: 40px;">
                        <li>{{ CustomSiteTranslator::get('Account Information: Name, email address, password, and other data necessary to create and manage your account.', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('Usage Information: Data about how you use our service, including information about your actions and interactions with the service.', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('Technical Information: IP address, browser type, operating system, and other technical data collected automatically.', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('Cookies and Tracking Technologies: We use cookies and similar technologies to enhance user experience, analyze trends, and administer the service.', $lang) }}</li>
                    </ul>
                </div>

                <div class="privacy-policy-title">
                    <h2>{{ CustomSiteTranslator::get('Use of Information', $lang) }}</h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                        {{ CustomSiteTranslator::get('We use the collected information for the following purposes:', $lang) }}
                    </p>
                    <ul style="padding-left: 40px;">
                        <li>{{ CustomSiteTranslator::get('Providing and supporting our service.', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('Improving and developing our service.', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('Responding to your requests and providing support.', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('Analyzing the use of our service to improve its functionality.', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('Ensuring the security of our service.', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('Complying with legal obligations and enforcing our policies.', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('Personalizing user experience and offering relevant content.', $lang) }}</li>
                    </ul>
                    <p>
                        {{ CustomSiteTranslator::get('Legal Basis for Processing:', $lang) }}
                    </p>
                    <ul style="padding-left: 40px;">
                        <li>{{ CustomSiteTranslator::get('Performance of a contract (providing the service).', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('Compliance with legal obligations.', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('Legitimate interests (service improvement, security enforcement).', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('User consent (where applicable, such as for marketing communications).', $lang) }}</li>
                    </ul>
                </div>

                <div class="privacy-policy-title">
                    <h2>{{ CustomSiteTranslator::get('Sharing Information with Third Parties', $lang) }}</h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                        {{ CustomSiteTranslator::get('We do not share your personal information with third parties, except in the following cases:', $lang) }}
                    </p>
                    <ul style="padding-left: 40px;">
                        <li>{{ CustomSiteTranslator::get('When required by law.', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('When necessary to provide our service (e.g., using third-party services for hosting, analytics, or customer support).', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('If we transfer data to service providers outside the EU, we ensure appropriate safeguards such as Standard Contractual Clauses (SCCs).', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('With your consent.', $lang) }}</li>
                    </ul>
                </div>

                <div class="privacy-policy-title">
                    <h2>{{ CustomSiteTranslator::get('Data Security', $lang) }}</h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                        {{ CustomSiteTranslator::get('We take measures to protect your personal information from unauthorized access, use, or disclosure. These include:', $lang) }}
                    </p>
                    <ul style="padding-left: 40px;">
                        <li>{{ CustomSiteTranslator::get('Encryption of sensitive data (e.g., passwords are hashed and stored securely).', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('Access control and authentication mechanisms.', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('Regular security audits and monitoring.', $lang) }}</li>
                    </ul>
                    <p>
                        {{ CustomSiteTranslator::get('However, please be aware that no method of data transmission over the Internet or method of data storage is completely secure.', $lang) }}
                    </p>
                </div>

                <div class="privacy-policy-title">
                    <h2>{{ CustomSiteTranslator::get('Your Rights', $lang) }}</h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                        {{ CustomSiteTranslator::get('Under GDPR and other applicable laws, you have the right to:', $lang) }}
                    </p>
                    <ul style="padding-left: 40px;">
                        <li>{{ CustomSiteTranslator::get('Access your personal information.', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('Correct inaccurate personal information.', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('Delete your personal information (right to be forgotten).', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('Restrict processing of your personal information.', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('Object to processing based on legitimate interests.', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('Data portability: Request a copy of your data in a structured, machine-readable format.', $lang) }}</li>
                        <li>{{ CustomSiteTranslator::get('Lodge a complaint with a data protection authority.', $lang) }}</li>
                    </ul>
                    <p>
                        {{ CustomSiteTranslator::get('To exercise these rights, please contact us at [specify email address].', $lang) }}
                    </p>
                </div>

                <div class="privacy-policy-title">
                    <h2>{{ CustomSiteTranslator::get('Data Retention', $lang) }}</h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                        {{ CustomSiteTranslator::get('We retain your personal data as long as necessary for the purposes outlined in this policy. If your account is inactive for more than [X months], we may delete your data unless legally required to retain it longer.', $lang) }}
                    </p>
                </div>

                <div class="privacy-policy-title">
                    <h2>{{ CustomSiteTranslator::get('Changes to the Privacy Policy', $lang) }}</h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                        {{ CustomSiteTranslator::get('We may change this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on our service. Your continued use of the service after any changes constitutes your acceptance of the new Privacy Policy.', $lang) }}
                    </p>
                </div>

                <div class="privacy-policy-title">
                    <h2>{{ CustomSiteTranslator::get('Prohibited Use of System Resources', $lang) }}</h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                        {{ CustomSiteTranslator::get('Users must not abuse system resources. NWMS.CLOUD is a free service, but it is not intended for excessively resource-intensive projects. Users must not upload extremely large datasets (e.g., millions of product records) or perform activities that place excessive load on our infrastructure.', $lang) }}
                    </p>
                    <p>
                        {{ CustomSiteTranslator::get('If we determine that a user\'s activities negatively impact the performance of the service, we reserve the right to restrict or terminate access.', $lang) }}
                    </p>
                </div>

                <div class="privacy-policy-title">
                    <h2>{{ CustomSiteTranslator::get('Governing Law', $lang) }}</h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                        {{ CustomSiteTranslator::get('This Privacy Policy is governed by and construed in accordance with the laws of Bulgaria.', $lang) }}
                    </p>
                </div>

                <div class="privacy-policy-title">
                    <h2>{{ CustomSiteTranslator::get('Contact Information', $lang) }}</h2>
                </div>
                <div class="privacy-policy-text">
                    <p>
                        {{ CustomSiteTranslator::get('If you have any questions about this Privacy Policy, please contact us at [specify email address].', $lang) }}
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