<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <!--- basic page needs
    ================================================== -->
    <meta charset="utf-8">
    <title>Doortag</title>
    <meta name="description"
        content="Stop overpaying for postage. Doortag gives you free access to the cheapest FedEx, UPS, DHL, and USPS rates. Compare, print labels, and ship in minutes">
    <meta name="author" content="Zenith Tech">

    <meta property="og:title" content="Doortag">
    <meta property="og:description"
        content="Stop overpaying for postage. Doortag gives you free access to the cheapest FedEx, UPS, DHL, and UPS rates. Compare, print labels, and ship in minutes">
    <meta property="og:image" content="https://doortag.com/images/share.webp">
    <meta property="og:url" content="https://doortag.com/">

    <!-- mobile specific metas
    ================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS
    ================================================== -->
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/vendor.css">
    <link rel="stylesheet" href="css/main.css">

    <!-- script
    ================================================== -->
    <script src="js/modernizr.js"></script>
    <script defer src="js/fontawesome/all.min.js"></script>

    <!-- favicons
    ================================================== -->
    <link rel="apple-touch-icon" sizes="180x180" href="images/favicon.jpg">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.jpg">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.jpg">
</head>

<body id="top">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-529PNW3J" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <!-- preloader
    ================================================== -->
    <div id="preloader">
        <div id="loader" class="dots-fade">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>

    <!-- site header
    ================================================== -->
    <header class="s-header">

        <div class="header-logo">
            <img src="images/logo.png">
        </div>

        <div class="header-email">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path
                    d="M0 12l11 3.1 7-8.1-8.156 5.672-4.312-1.202 15.362-7.68-3.974 14.57-3.75-3.339-2.17 2.925v-.769l-2-.56v7.383l4.473-6.031 4.527 4.031 6-22z" />
            </svg>
            <a href="mailto:info@doortag.com">info@doortag.com</a>
            <br />
            <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24"
                viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2023 Fonticons, Inc.-->
                <path
                    d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z" />
            </svg> <a href="tel:(740) 715-1617">(740) 715-1617</a>
        </div>

    </header> <!-- end s-header -->


    <!-- intro
    ================================================== -->
    <section id="intro" class="s-intro s-intro--slides">

        <div class="intro-slider">
            <div class="intro-slider-img bg-opacity-40" style="background-image: url(images/slides/slide-01.webp);">
            </div>
            <div class="intro-slider-img" style="background-image: url(images/slides/slide-02.webp);"></div>
            <div class="intro-slider-img" style="background-image: url(images/slides/slide-03.webp);"></div>
        </div>

        <div class="grid-overlay">
            <div></div>
        </div>

        <div class="row intro-content">

            <div class="column">

                <div class="intro-content__text">

                    <h3>
                        Coming Soon
                    </h3>

                    <h1>
                        Get ready everyone. <br>
                        We are currently <br>
                        working on a super<br>
                        <span style="color:#c50105">awesome</span> website.
                    </h1>

                </div> <!-- end intro-content__text -->

                <div class="intro-content__bottom">

                    <div class="intro-content__counter-wrap">
                        <h4>Launching in</h4>

                        <div class="counter">
                            <div class="counter__time days">
                                365
                                <span>D</span>
                            </div>
                            <div class="counter__time hours">
                                09
                                <span>H</span>
                            </div>
                            <div class="counter__time minutes">
                                54
                                <span>M</span>
                            </div>
                            <div class="counter__time seconds">
                                57
                                <span>S</span>
                            </div>
                        </div> <!-- end counter -->
                    </div> <!-- end intro-content__counter-wrap -->

                    <div class="intro-content__notify">
                        <button type="button" class="btn--stroke btn--small"
                            onclick="window.location.href='{{ route('register') }}'">
                            Join Us
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path d="M24 12l-9-9v7h-15v4h15v7z" />
                            </svg>
                        </button>
                        <button type="button" class="btn--stroke btn--small"
                            onclick="window.location.href='{{ route('login') }}'">
                            Admin Login
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path d="M24 12l-9-9v7h-15v4h15v7z" />
                            </svg>
                        </button>
                    </div> <!-- end intro-content__notify -->

                    <div class="intro-content__notify" style="display:none">
                        <button type="button" class="btn--stroke btn--small modal-trigger">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path d="M24 12l-9-9v7h-15v4h15v7z" />
                            </svg>
                        </button>
                    </div> <!-- end intro-content__notify -->

                </div> <!-- end intro-content__bottom -->

            </div> <!-- end column -->

        </div> <!--  end intro-content -->

        <div class="modal">
            <div class="modal__inner">

                <span class="modal__close"></span>

            </div> <!-- end modal inner -->
        </div> <!-- end modal -->

        <ul class="intro-social">
            <li><a href="#"><i class="fab fa-facebook" aria-hidden="true"></i></a></li>
            <li><a href="#"><i class="fab fa-instagram" aria-hidden="true"></i></a></li>
        </ul> <!-- end intro social -->


    </section> <!-- end s-intro -->

    <!-- Java Script
    ================================================== -->
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/main.js?v=1"></script>

</body>
