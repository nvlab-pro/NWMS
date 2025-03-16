<!DOCTYPE HTML>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>NWMS - free warehouse automation system</title>
    <meta name="description"
          content="Completely free, cloud-based warehouse management system (WMS). Receipt of goods, address storage of goods, work with data collection terminals, control of balances.">
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


</head>

<body>

<!--==================================================-->
<!-- Start techo header top area  -->
<!--==================================================-->
<div class="header-top-area">
    <div class="container">
        <div class="row align-items-center d-flex">
            <div class="col-lg-5">
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

            <div class="col-lg-4">
                <!--header top address-->
                <div class="header-top-right-social">
                    <ul>
                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                        <li><a href="https://www.youtube.com/@nwms_en" target="_blank"><i class="fab fa-youtube"></i></a></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-1">
                <!--header top address-->
                <div class="header-top-right-social">
                </div>
            </div>

            <div class="col-lg-1 header-top-right-language">
                <select id="language-selector" onchange="changeLanguage(this.value)">
                    <option value="en">🇺🇸 English</option>
                    <option value="bg">🇧🇬 Bulgarian</option>
                    <option value="de">🇩🇪 German</option>
                    <option value="fr">🇫🇷 French</option>
                    <option value="rus">🇷🇺 Russian</option>
                    <option value="ukr">🇺🇦 Ukrainian</option>
                    <option value="sp">🇪🇸 Spanish</option>
                    <option value="ch">🇨🇳 Chinese</option>
                    <option value="pr">🇵🇹 Portuguese</option>
                    <option value="kz">🇰🇿 Kazakh</option>
                    <option value="bel">🇧🇾 Belarusian</option>
                    <option value="gr">🇬🇪 Georgian</option>
                </select>
            </div>

        </div>
    </div>
</div>
<script>
    function changeLanguage(lang) {
        // Здесь можно реализовать логику смены языка, например, редирект
        window.location.href = '/' + lang + '/';
    }
</script>
<!--==================================================-->
<!-- End techo header top area  -->
<!--==================================================-->