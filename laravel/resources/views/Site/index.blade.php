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
<!-- Start techo Hero Section  -->
<!--==================================================-->
<div class="hero-section d-flex align-items-center">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="hero-content wow fadeInLeft animated">
                    <div class="hero-title">
                        <h5>{{ CustomSiteTranslator::get('Warehouse automation system', $lang) }}</h5>
                        <h1>{{ CustomSiteTranslator::get('Completely free WMS', $lang) }}</h1>
                    </div>
                    <div class="hero-text">
                        <p>{!! CustomSiteTranslator::get('The cloud <b>warehouse management system</b> NWMS offers a <b>completely free</b> solution to
                            automate and streamline all your warehouse operations. From inventory tracking to order fulfillment and
                            shipping, NWMS helps businesses of any size reduce errors, improve efficiency, and gain real-time
                            insights into stock movement—all without the cost of traditional WMS solutions.<br>
                            <br>
                            Start efficient warehouse automation with NWMS today!', $lang) !!}</p>
                    </div>
                    <div class="hero-button">
                        <div class="hero-main-button">
                            <a href="#form">Start A Project</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <img src="/img/index_main3.png" alt="hero-thumb">
            </div>
        </div>
    </div>
</div>
<!--==================================================-->
<!-- End techo Hero Section  -->
<!--==================================================-->


<!--==================================================-->
<!-- Start techo feature-area  -->
<!--==================================================-->
<div class="feature-area">
    <div class="container">
        <h2>{!! CustomSiteTranslator::get('Who needs the program:', $lang) !!}</h2>
        <br>
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="signal-feature-box wow fadeInLeft animated">
                    <div class="feature-thumb">
                        <img src="/img/iconFFWMS2.png" alt="thumb">
                    </div>
                    <div class="feature-box-title">
                        <h2>{!! CustomSiteTranslator::get('Fulfilment operators', $lang) !!}</h2>
                    </div>
                    <div class="feature-box-description">
                        <p>{!! CustomSiteTranslator::get('WMS is designed to provide a full range of fulfillment services as a supplier of warehouse
                            services.', $lang) !!}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="signal-feature-box wow fadeInUp animated">
                    <div class="feature-thumb">
                        <img src="/img/iconRetailersWMS.png" alt="thumb">
                    </div>
                    <div class="feature-box-title" style="padding-top: 10px;">
                        <h2>{!! CustomSiteTranslator::get('Retailers warehouses', $lang) !!}</h2>
                    </div>
                    <div class="feature-box-description">
                        <p>{!! CustomSiteTranslator::get('WMS can work as a classic software for automating the work of a regular retailer’s
                            warehouse.', $lang) !!}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="signal-feature-box wow fadeInDown animated">
                    <div class="feature-thumb">
                        <img src="/img/iconEShopWMS.png" alt="thumb">
                    </div>
                    <div class="feature-box-title" style="padding-top: 5px;">
                        <h2>{!! CustomSiteTranslator::get('Online store warehouses', $lang) !!}</h2>
                    </div>
                    <div class="feature-box-description">
                        <p>{!! CustomSiteTranslator::get('NWMS will completely cover all needs for warehouse processing of your goods in your online
                            store.', $lang) !!}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="signal-feature-box wow fadeInRight animated">
                    <div class="feature-thumb">
                        <img src="/img/iconWarehousesWMS.png" alt="thumb">
                    </div>
                    <div class="feature-box-title" style="padding-top: 20px;">
                        <h2>{!! CustomSiteTranslator::get('Any warehouses', $lang) !!}</h2>
                    </div>
                    <div class="feature-box-description">
                        <p>{!! CustomSiteTranslator::get('Actually, NWMS is suitable for automating the work of any warehouse, of almost any size.', $lang) !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--==================================================-->
<!-- End techo feature-area  -->
<!--==================================================-->


<!--==================================================-->
<!-- start techo WHY CHOOSE US area  -->
<!--==================================================-->
<div class="why-choose-us-area">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5">
                <div>
                    <img src="/img/iconProgrammingWMS.png" alt="Programming WMS" style="width:400px;">
                </div>
            </div>
            <div class="col-lg-7">
                <div class="techo-section-title text-left wow fadeInRight animated">
                    <h5>{!! CustomSiteTranslator::get('WHY FREE?', $lang) !!}</h5>
                    <h3>{!! CustomSiteTranslator::get('Why is the program', $lang) !!} </h3>
                    <h2>{!! CustomSiteTranslator::get('provided free of charge?', $lang) !!}</h2>
                    <div class="bar-main">
                    </div>
                    <p>
                        {!! CustomSiteTranslator::get('Our team has been involved in warehouse logistics for over 15 years and during this time we have
                        accumulated vast experience in the processes of developing warehouse automation systems and in managing these
                        warehouses.<br>
                        <br>
                        We want to offer our developments to as many users as possible simply because we can!<br>
                        <br>
                        We hope to create a community of warehouse logisticians who will help each other, as it happens
                        in other industries.', $lang) !!}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!--==================================================-->
<!-- End techo WHY CHOOSE US area  -->
<!--==================================================-->


<!--==================================================-->
<!-- Start techo feature-area  -->
<!--==================================================-->
<div class="feature-area">
    <div class="container">
        <h2>{!! CustomSiteTranslator::get('Main features:', $lang) !!}</h2>
        <br>
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="signal-feature-box wow fadeInLeft animated">
                    <div class="feature-thumb">
                        <img src="/img/iconFFWMS.png" alt="thumb">
                    </div>
                    <div class="feature-box-title">
                        <h2>{!! CustomSiteTranslator::get('Full functionality of classic WMS', $lang) !!}</h2>
                    </div>
                    <div class="feature-box-description">
                        <p>{!! CustomSiteTranslator::get('First of all, NWMS is a classic WMS with all the capabilities including address storage,
                            working with a terminal for data collection and control of balances, etc.', $lang) !!}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="signal-feature-box wow fadeInUp animated">
                    <div class="feature-thumb">
                        <img src="/img/iconAPIWMS.png" alt="thumb">
                    </div>
                    <div class="feature-box-title">
                        <h2>{!! CustomSiteTranslator::get('Powerful rest API', $lang) !!}</h2><br>
                    </div>
                    <div class="feature-box-description">
                        <p>{!! CustomSiteTranslator::get('Powerful rest API allowing you to do any integration of NWMS with your software or third
                            party services.', $lang) !!}
                            <br><br><br>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="signal-feature-box wow fadeInDown animated">
                    <div class="feature-thumb">
                        <img src="/img/iconStatsWMS.png" alt="thumb">
                    </div>
                    <div class="feature-box-title">
                        <h2>{!! CustomSiteTranslator::get('Statistics of all warehouse operations', $lang) !!}</h2>
                    </div>
                    <div class="feature-box-description">
                        <p>{!! CustomSiteTranslator::get('WMS includes a statistics module that displays data on employee actions in the warehouse,
                            processed orders, turnover and other key indicators.', $lang) !!}<br><br></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="signal-feature-box wow fadeInRight animated">
                    <div class="feature-thumb">
                        <img src="/img/iconBillingWMS.png" alt="thumb">
                    </div>
                    <div class="feature-box-title">
                        <h2>{!! CustomSiteTranslator::get('Billing of operations', $lang) !!}</h2>
                    </div>
                    <div class="feature-box-description">
                        <br>
                        <p>{!! CustomSiteTranslator::get('The billing module allows you to calculate the cost of each operation performed on the
                            company. This is especially relevant for service companies.', $lang) !!}</p>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--==================================================-->
<!-- End techo feature-area  -->
<!--==================================================-->


<!--==================================================-->
<!-- start techo counter area  -->
<!--==================================================-->
<div class="counter-area">
    <div class="container">
        <div class="row counter-bg text-center align-items-center d-flex">
            <div class="col-lg-3 col-md-6">
                <div class="counter-single-box wow fadeInLeft animated">
                    <div class="counter-icon">
                        <i class="fas fa-hands-wash"></i>
                    </div>
                    <div class="counter-content">
                        <div class="counter-number">
                            <h2 class="counter">12</h2>
                            <h2>+</h2>
                        </div>
                        <div class="counter-text">
                            <span>{!! CustomSiteTranslator::get('Happy warehouses', $lang) !!}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="counter-single-box wow fadeInLeft animated">
                    <div class="counter-icon">
                        <i class="fas fa-suitcase"></i>
                    </div>
                    <div class="counter-content">
                        <div class="counter-number">
                            <h2 class="counter">1240</h2>
                            <h2>+</h2>
                        </div>
                        <div class="counter-text">
                            <span>{!! CustomSiteTranslator::get('Orders Completed', $lang) !!}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="counter-single-box wow fadeInRight animated">
                    <div class="counter-icon">
                        <i class="far fa-star"></i>
                    </div>
                    <div class="counter-content">
                        <div class="counter-number">
                            <h2 class="counter">500</h2>
                        </div>
                        <div class="counter-text">
                            <span>{!! CustomSiteTranslator::get('Goods in system', $lang) !!}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="counter-single-box wow fadeInRight animated">
                    <div class="counter-icon">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div class="counter-content">
                        <div class="counter-number">
                            <h2 class="counter">3000</h2>
                            <h2>k+</h2>
                        </div>
                        <div class="counter-text">
                            <span>{!! CustomSiteTranslator::get('Total items in stock', $lang) !!}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--==================================================-->
<!-- start techo counter area  -->
<!--==================================================-->


<!--==================================================-->
<!-- start techo about us section -->
<!--==================================================-->
<div class="about-us-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5">
                <div>
                    <img src="/img/iconDemoWMS.png" alt="thumb">
                </div>
            </div>
            <div class="col-lg-7 wow fadeInRight animated">
                <div class="techo-section-title text-left">
                    <h5>{!! CustomSiteTranslator::get('DEMO VERSION', $lang) !!}</h5>
                    <h3>{!! CustomSiteTranslator::get('Check out the system right now', $lang) !!}</h3>
                    <h2>{!! CustomSiteTranslator::get('On our <span>demo version', $lang) !!}</span></h2>
                    <div class="bar-main">
                    </div>
                    <p>{!! CustomSiteTranslator::get('To try our WMS right now, use the following accesses:', $lang) !!}</p>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row about-box">
                            <div class="col-lg-12">
                                <div class="about-single-box">
                                    <div class="about-icon" style="width: 120px;">
                                        <i class="fas">URL: </i>
                                    </div>
                                    <div class="about-title">
                                        <a href="https://nwms.cloud/admin" style="font-size: 30px;"
                                           onmouseover="this.style.color='white'" onmouseout="this.style.color='black'"
                                           target="_blank">https://nwms.cloud/admin</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row about-box">
                            <div class="col-lg-6">
                                <div class="about-single-box">
                                    <div class="about-icon">
                                        <i class="fas">LOGIN: </i>
                                    </div>
                                    <div class="about-title" style="width: 100%">
                                        <h2 style="font-size: 20px;">admin@demo.com</h2>
                                    </div>
                                    <div>
                                        <i class="fas fa-copy copy-icon"
                                           onclick="copyToClipboard('admin@demo.com')"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="about-single-box">
                                    <div class="about-icon">
                                        <i class="fas">PASSWORD:</i>
                                    </div>
                                    <div class="about-title" style="width: 100%">
                                        <h2 style="font-size: 20px;">demo</h2>
                                    </div>
                                    <div>
                                        <i class="fas fa-copy copy-icon" onclick="copyToClipboard('demo')"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert("Copied: " + text);
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    }
</script>
<!--==================================================-->
<!-- End techo about us section -->
<!--==================================================-->


<!--==================================================-->
<!-- Start techo  Video Area -->
<!--==================================================-->
<div class="video-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="call-to-action style-one">
                    <div class="bd-video wow fadeInLeft animated">
                        <div class="video-icon">
                            <a class="video-vemo-icon venobox vbox-item" data-vbtype="youtube" data-autoplay="true"
                               href="https://youtu.be/BS4TUd7FJSg"><i class="fa fa-play"></i></a>
                        </div>
                    </div>
                    <div class="single_call-to-action_text">
                        <div class="call-to-action_top_text wow fadeInLeft animated">
                            <div class="call-to-action-title">
                                <span class="subtitlespan"></span>
                                <h2>{!! CustomSiteTranslator::get('A quick tour of our WMS', $lang) !!}</h2>
                            </div>
                        </div>
                        <div class="call-to-action-inner wow fadeInRight animated">
                            <div class="call-to-action-desc">
                                <p>{!! CustomSiteTranslator::get('To quickly familiarize yourself with the main features of NWMS, you can watch the
                                    introductory video on youtube.', $lang) !!}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--==================================================-->
<!-- End techo  Video Area -->
<!--==================================================-->


<!--==================================================-->
<!-- Start techo Testimonial Area -->
<!--==================================================-->
<a name="form"></a>
<div class="testimonial-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="techo-section-title text-center">
                    <h5>{!! CustomSiteTranslator::get('START NOW', $lang) !!}</h5>
                    <h3>{!! CustomSiteTranslator::get('Order form', $lang) !!}</h3>
                    <div class="bar-main">
                    </div>
                    <H4>{!! CustomSiteTranslator::get('You can start working immediately after filling out the form below.', $lang) !!}</H4>
                </div>
            </div>
            <div class="col-lg-12 wow fadeInRight animated">
                <form action="/new-user-form" method="POST" id="dreamit-form">
                    @csrf
                    <div class="row form">
                        <div class="col-lg-6">
                            <div class="form-box">
                                <input type="text" name="FName" placeholder="{!! CustomSiteTranslator::get('First Name', $lang) !!}" required="">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-box">
                                <input type="text" name="LName" placeholder="{!! CustomSiteTranslator::get('Last Name', $lang) !!}" required="">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-box">
                                <input type="email" name="email" placeholder="E-mail (login)" required="">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-box">
                                <input type="password" name="password" placeholder="Password" required minlength="6" pattern=".{6,}">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-box">
                                <input type="text" name="company" placeholder="{!! CustomSiteTranslator::get('Company name', $lang) !!}" required="">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-box">
                                <textarea name="massage" id="massage" cols="30" rows="10"
                                          placeholder="{!! CustomSiteTranslator::get('Comment', $lang) !!}"></textarea>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-box">
                                <div class="language-selector">
                                    <H5>{!! CustomSiteTranslator::get('Select interface language:', $lang) !!}</H5> &nbsp;
                                    <select id="language-select" name="language">
                                        <option value="en" data-flag="🇺🇸">🇺🇸 English</option>
                                        <option value="bg" data-flag="🇧🇬">🇧🇬 Bulgarian</option>
                                        <option value="rus" data-flag="🇷🇺">🇷🇺 Russian</option>
                                        <option value="ukr" data-flag="🇺🇦">🇺🇦 Ukrainian</option>
                                        <option value="fr" data-flag="🇫🇷">🇫🇷 French</option>
                                        <option value="de" data-flag="🇩🇪">🇩🇪 German</option>
                                        <option value="sp" data-flag="🇪🇸">🇪🇸 Spanish</option>
                                        <option value="ch" data-flag="🇨🇳">🇨🇳 Chinese</option>
                                        <option value="pr" data-flag="🇵🇹">🇵🇹 Portuguese</option>
                                        <option value="kz" data-flag="🇰🇿">🇰🇿 Kazakh</option>
                                        <option value="bel" data-flag="🇧🇾">🇧🇾 Belarusian</option>
                                        <option value="gr" data-flag="🇬🇪">🇬🇪 Georgian</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="submit-button">
                                <input type="text" name="mName" value="" style="display:none;">
                                <input type="hidden" name="fst" value="<?= time(); ?>">

                                <button type="submit">{!! CustomSiteTranslator::get('Create account', $lang) !!}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let form = document.getElementById("dreamit-form");
        let submitButton = form.querySelector("button[type='submit']");

        form.addEventListener("submit", function() {
            submitButton.disabled = true; // Блокируем кнопку
            submitButton.innerText = "Processing..."; // Меняем текст
        });
    });
</script>

<!--==================================================-->
<!-- END techo Testimonial Area -->
<!--==================================================-->

@include('Site.MainBlocks.footer')