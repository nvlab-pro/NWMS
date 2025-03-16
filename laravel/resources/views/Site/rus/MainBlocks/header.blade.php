<!DOCTYPE HTML>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>NWMS - бесплатная система автоматизации склада</title>
    <meta name="description"
          content="Полностью бесплатная, облачная система управления складом (WMS). Прием товара, адресное хранение товара, работа с терминалами сбора данных, контроль остатков..">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="56x56" href="assets/images/fav-icon/icon.png">
    <!-- bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css" media="all">
    <!-- carousel CSS -->
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css" type="text/css" media="all">
    <!-- animate CSS -->
    <link rel="stylesheet" href="assets/css/animate.css" type="text/css" media="all">
    <!-- animated-text CSS -->
    <link rel="stylesheet" href="assets/css/animated-text.css" type="text/css" media="all">
    <!-- font-awesome CSS -->
    <link rel="stylesheet" href="assets/css/all.min.css" type="text/css" media="all">
    <!-- font-flaticon CSS -->
    <link rel="stylesheet" href="assets/css/flaticon.css" type="text/css" media="all">
    <!-- theme-default CSS -->
    <link rel="stylesheet" href="assets/css/theme-default.css" type="text/css" media="all">
    <!-- meanmenu CSS -->
    <link rel="stylesheet" href="assets/css/meanmenu.min.css" type="text/css" media="all">
    <!-- transitions CSS -->
    <link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css" media="all">
    <!-- venobox CSS -->
    <link rel="stylesheet" href="venobox/venobox.css" type="text/css" media="all">
    <!-- bootstrap icons -->
    <link rel="stylesheet" href="assets/css/bootstrap-icons.css" type="text/css" media="all">
    <!-- Main Style CSS -->
    <link rel="stylesheet" href="assets/css/style.css" type="text/css" media="all">
    <!-- responsive CSS -->
    <link rel="stylesheet" href="assets/css/responsive.css" type="text/css" media="all">
    <!-- modernizr js -->
    <script src="assets/js/vendor/modernizr-3.5.0.min.js"></script>
    <link href="https://fonts.cdnfonts.com/css/clash-display" rel="stylesheet">

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-MLTHNSCT');</script>
    <!-- End Google Tag Manager -->

</head>

<body>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MLTHNSCT"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<!--==================================================-->
<!-- Start techo header top area  -->
<!--==================================================-->
<div class="header-top-area">
    <div class="container">
        <div class="row align-items-center d-flex">
            <div class="col-lg-9">
                <div class="header-address-info">
                    <ul>
                        <li><a href="#"><i class="far fa-envelope"></i><span>info@nwms.cloud</span></a></li>
                        <!--
                        <li><i class="fas fa-map"></i><span>1st Floor New World.</span></li>
                        <li><a href="#"><i class="fas fa-phone"></i><span>+880 320 432 242</span></a></li>
                        -->
                    </ul>
                </div>
            </div>

            <div class="col-lg-1">
                <!--header top address-->
                <div class="header-top-right-social">
                    <ul>
{{--                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>--}}
{{--                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>--}}
{{--                        <li><a href="#"><i class="fab fa-instagram"></i></a></li>--}}
                        <li><a href="https://t.me/+UJ5If6slQXZjYjMy"><i class="fab fa-telegram"></i></a></li>
                        <li><a href="https://www.youtube.com/@nwms_en" target="_blank"><i class="fab fa-youtube"></i></a></li>
                    </ul>
                </div>
            </div>


            <div class="col-lg-1 header-top-right-language">
                <select id="language-selector" onchange="changeLanguage(this.value)">
                    <option value="bel">🇧🇾 Belarusian</option>
                    <option value="bg">🇧🇬 Bulgarian</option>
                    <option value="ch">🇨🇳 Chinese</option>
                    <option value="de">🇩🇪 German</option>
                    <option value="en">🇺🇸 English</option>
                    <option value="fr">🇫🇷 French</option>
                    <option value="gr">🇬🇪 Georgian</option>
                    <option value="kz">🇰🇿 Kazakh</option>
                    <option value="pr">🇵🇹 Portuguese</option>
                    <option value="rus" SELECTED>🇷🇺 Russian</option>
                    <option value="sp">🇪🇸 Spanish</option>
                    <option value="ukr">🇺🇦 Ukrainian</option>
                </select>
            </div>

        </div>
    </div>
</div>
<script>
    function changeLanguage(lang) {
        if (lang == 'en') {
            window.location.href = '/';
        } else {
            window.location.href = '/' + lang + '/';
        }
    }
</script>
<!--==================================================-->
<!-- End techo header top area  -->
<!--==================================================-->