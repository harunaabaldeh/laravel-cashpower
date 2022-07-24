$ = jQuery.noConflict();

$(window).on("load", function () {

    /* ===================================
     Loading Timeout
     ====================================== */

    "use strict";
    setTimeout(function () {
        $(".loader").fadeOut("slow");
    }, 1000);

});

jQuery(function ($) {

    /* ===================================
            Scroll
    ====================================== */


    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 220)   { // Set position from top to add class
            $('header').addClass('header-appear');
        }
        else {
            $('header').removeClass('header-appear');
        }
    });

    //scroll to appear
    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 500)
            $('.scroll-top-arrow').fadeIn('slow');
        else
            $('.scroll-top-arrow').fadeOut('slow');
    });

    //Click event to scroll to top
    $(document).on('click', '.scroll-top-arrow', function () {
        $('html, body').animate({scrollTop: 0}, 800);
        return false;
    });

    //scroll sections
    $(".scroll").on("click", function (event) {
        event.preventDefault();
        $("html,body").animate({
            scrollTop: $(this.hash).offset().top
        }, 1200);
    });


    // fixing bottom nav to top on scrolliing

    var $fixednav = $(".navbar-bottom");
    $(window).on("scroll", function () {
        var $heightcalc = $(window).height() - $fixednav.height();
        if ($(this).scrollTop() > $heightcalc) {
            $fixednav.addClass("navbar-bottom-top");
        } else {
            $fixednav.removeClass("navbar-bottom-top");
        }
    });

    /* =====================================
          Portfolio Filter
      ====================================== */

    // isotope
    $('.gallery').isotope({
        // options
        itemSelector: '.items'
    });

    var $gallery = $('.gallery').isotope({
        // options
    });

    // filter items on button click
    $('.filtering').on('click', 'span', function () {

        var filterValue = $(this).attr('data-filter');

        $gallery.isotope({filter: filterValue});

    });

    $('.filtering').on('click', 'span', function () {

        $(this).addClass('active').siblings().removeClass('active');

    });

    setTimeout(function (){
        $('.filtering .active').click();
    }, 1500);

    /* =====================================
               Parallax
        ====================================== */

    if ($(window).width() > 992) {
        $(".parallax").parallaxie({
            speed: 0.55,
            offset:0,
        });
    }


    /* =====================================
              Search Bar
       ====================================== */

    $('#search-close').on('click', function () {
        $('#header-search').toggleClass("search-open");
    });


    /* ===================================
        Animated Progress Bar
    ====================================== */


    // progress bar
    $(window).on('scroll', function () {
        $(".skills-progress span").each(function () {
            var bottom_of_object =
                $(this).offset().top + $(this).outerHeight();
            var bottom_of_window =
                $(window).scrollTop() + $(window).height();
            var myVal = $(this).attr('data-value');
            if(bottom_of_window > bottom_of_object) {
                $(this).css({
                    width : myVal
                });
            }
        });
    });


    /* ===================================
         Side Menu
     ====================================== */
    if ($("#sidemenu_toggle").length) {
        $("body").addClass("pushwrap");
        $("#sidemenu_toggle").on("click", function () {
            $(".pushwrap").toggleClass("active");
            $(".side-menu").addClass("side-menu-active"), $("#close_side_menu").fadeIn(700)
        }), $("#close_side_menu").on("click", function () {
            $(".side-menu").removeClass("side-menu-active"), $(this).fadeOut(200), $(".pushwrap").removeClass("active")
        }), $(".side-nav .navbar-nav .nav-link").on("click", function () {
            $(".side-menu").removeClass("side-menu-active"), $("#close_side_menu").fadeOut(200), $(".pushwrap").removeClass("active")
        }), $("#btn_sideNavClose").on("click", function () {
            $(".side-menu").removeClass("side-menu-active"), $("#close_side_menu").fadeOut(200), $(".pushwrap").removeClass("active")
        });
    }


    /* ===================================
        Owl Carousel
    ====================================== */


    var mainSlider;

    $(document).ready(function () {

        mainSlider = $('.owl-team');

        mainSlider.owlCarousel({
            lazyLoad: true,
            dots: false,
            loop: true,
            nav: true,
            animateOut: 'fadeOut',
            animateIn: 'fadeIn',
            autoplay:true,
            autoplayTimeout:5000,
            autoplayHoverPause:true,
            items: 1,
            smartSpeed: 500,
            navText: ["", ""],
            responsive: {
                991: {
                    autoplay:false,
                    mouseDrag:false,
                }
            }
        });

        mainSlider.on('changed.owl.carousel', function (property) {
            var current = property.item.index;
            var prev = $(property.target).find(".owl-item").eq(current).prev().find("img").attr('src');
            var next = $(property.target).find(".owl-item").eq(current).next().find("img").attr('src');

            var prevText = $(property.target).find(".owl-item").eq(current).prev().find("h4").html();
            var nextText = $(property.target).find(".owl-item").eq(current).next().find("h4").html();

            $('.navPrev').find('img').attr('src', prev);
            $('.navNext').find('img').attr('src', next);

            $('.navPrev').find('h4').html(prevText);
            $('.navNext').find('h4').html(nextText);
        });

    });

    $('.navNext').on('click', function () {
        mainSlider.trigger('next.owl.carousel', [300]);
        return false;
        $(".navNext span").fadeIn("slow");
    });

    $('.navPrev').on('click', function () {
        mainSlider.trigger('prev.owl.carousel', [300]);
        return false;
    });


    $(document).ready(function(){

        $(".owl-work").owlCarousel({
            loop:true,
            items:1,
            margin:0,
            stagePadding: 0,
            animateOut: 'fadeOut',
            animateIn: 'fadeIn',
            smartSpeed: 500,
            mouseDrag:false,
        });

        dotcount = 1;

        $('.owl-work .owl-dot').each(function() {
            jQuery( this ).addClass( 'dotnumber' + dotcount);
            jQuery( this ).attr('data-info', dotcount);
            dotcount=dotcount+1;
        });

        slidecount = 1;

        $('.owl-work .owl-item').not('.cloned').each(function() {
            $( this ).addClass( 'slidenumber' + slidecount);
            slidecount=slidecount+1;
        });

        $('.owl-work .owl-dot').each(function() {
            grab = jQuery(this).data('info');
            slidegrab = jQuery('.slidenumber'+ grab +' img').attr('src');
            jQuery(this).css("background-image", "url("+slidegrab+")");
        });

        amount = $('.owl-work .owl-dot').length;
        gotowidth = 100/amount;
        $('.owl-work .owl-dot').css("height", gotowidth+"%");

    });


    var owl5 = $('.partners-slider');
    owl5.owlCarousel({
        items: 5,
        autoplay: 1500,
        smartSpeed: 1500,
        autoplayHoverPause: true,
        slideBy: 1,
        loop: true,
        margin: 30,
        dots: false,
        nav: false,
        responsive: {
            1200: {
                items: 5,
            },
            900: {
                items: 4,
            },
            768: {
                items: 3,
            },
            480: {
                items: 2,
            },
            320: {
                items: 1,
            },
        }
    });


    /*Testimonial one slide fade*/
    $("#testimonial-quote").owlCarousel({
        items: 1,
        autoplay: 2500,
        autoplayHoverPause: true,
        mouseDrag: false,
        loop: true,
        margin: 30,
        dots: true,
        dotsContainer: "#owl-thumbs",
        nav: false,
        animateIn: "fadeIn",
        animateOut: "fadeOut",
        responsive: {
            1280: {
                items: 1,
            },
            600: {
                items: 1,
            },
            320: {
                items: 1,
            },
        }
    });

    /*Testimonial one slide fade*/
    $(".owl-testimonial").owlCarousel({
        autoplay: 2500,
        autoplayHoverPause: true,
        loop: true,
        margin: 30,
        dots: true,
        nav: false,
        responsive: {
            1280: {
                center: true,
                items: 3,
            },
            800: {
                items: 2,
            },
            320: {
                items: 1,
            },
        }
    });

    /* owl team */
    $(".owl-team-three").owlCarousel({
        items: 3,
        autoplay: 2500,
        autoplayHoverPause: true,
        loop: true,
        margin: 30,
        dots: false,
        nav: false,
        responsive: {
            1280: {
                items: 3,
            },
            980: {
                items: 3,
            },
            600: {
                items: 2,
            },
            320: {
                items: 1,
            },
        }
    });


    /* ------ CubePortfolio ------ */

    $("#portfolio-measonry").cubeportfolio({
        filters: '#measonry-filters',
        loadMoreAction: 'click',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: "scaleSides",
        gapHorizontal: 30,
        gapVertical: 30,
        gridAdjustment: "responsive",
        mediaQueries: [{
            width: 1500,
            cols: 2
        }, {
            width: 1100,
            cols: 2
        }, {
            width: 768,
            cols: 2
        }, {
            width: 480,
            cols: 1
        }, {
            width: 320,
            cols: 1
        }],
    });

    
    /* ===================================
           Revolution slider
    ====================================== */


    $("#rev_slider_12_1").show().revolution({
        sliderType: "standard",
        jsFileLocation: "//localhost:82/revslider/revslider/public/assets/js/",
        sliderLayout: "fullscreen",
        dottedOverlay: "none",
        delay: 9000,
        navigation: {
            keyboardNavigation: "off",
            keyboard_direction: "horizontal",
            mouseScrollNavigation: "off",
            mouseScrollReverse: "default",
            onHoverStop: "off",
            bullets: {
                enable: true,
                hide_onmobile: false,
                style: "heroex",
                hide_onleave: false,
                direction: "vertical",
                h_align: "right",
                v_align: "center",
                h_offset: 50,
                v_offset: 0,
                space: 5,
                tmp: '<div class="tp-bullet-number"><span class="tp-count">{{param1}}</span><span class="tp-bullet-line"></span></div>'
            }
        },
        viewPort: {
        enable: true,
            outof: "pause",
            visible_area: "90%"
    },
        responsiveLevels: [1280, 1024, 768, 480],
        gridwidth: [1140, 1024, 768, 480],
        gridheight: [868, 768, 960, 720],
        shadow: 0,
        spinner: "off",
        stopLoop: "off",
        stopAfterLoops: -1,
        stopAtSlide: -1,
        shuffle: "off",
        autoHeight: "off",
        hideThumbsOnMobile: "off",
        hideSliderAtLimit: 0,
        hideCaptionAtLimit: 0,
        hideAllCaptionAtLilmit: 0,
        debugMode: false,
        fallbacks: {
            simplifyAll: "off",
            nextSlideOnWindowFocus: "off",
            disableFocusListener: false,
        }
    });

    $("#rev_slider_15_1").show().revolution({
        sliderType:"standard",
        jsFileLocation:"//localhost:82/revslider/revslider/public/assets/js/",
        sliderLayout:"fullscreen",
        dottedOverlay:"none",
        delay:9000,
        navigation: {
            keyboardNavigation:"off",
            keyboard_direction: "horizontal",
            mouseScrollNavigation:"off",
            mouseScrollReverse:"default",
            onHoverStop:"off",
            bullets: {
                enable:true,
                hide_onmobile:false,
                style:"heroex2",
                hide_onleave:false,
                direction:"horizontal",
                h_align:"center",
                v_align:"bottom",
                h_offset:-270,
                v_offset:50,
                space:5,
                tmp:'<div class="tp-bullet-number"><span class="tp-count">{{param1}}</span><span class="tp-bullet-line"></span></div>'
            }
        },  viewPort: {
            enable: true,
            outof: "pause",
            visible_area: "90%"
        },
        responsiveLevels:[1240,1024,768,480],
        visibilityLevels:[1240,1024,768,480],
        gridwidth:[1240,1024,768,480],
        gridheight:[868,600,500,400],
        shadow: 0,
        spinner: "off",
        stopLoop: "off",
        stopAfterLoops: -1,
        stopAtSlide: -1,
        shuffle: "off",
        autoHeight: "off",
        hideThumbsOnMobile: "off",
        hideSliderAtLimit: 0,
        hideCaptionAtLimit: 0,
        hideAllCaptionAtLilmit: 0,
        debugMode: false,
        fallbacks: {
            simplifyAll: "off",
            nextSlideOnWindowFocus: "off",
            disableFocusListener: false,
        }
    });


    $("#rev_slider_11_1").show().revolution({
        sliderType:"standard",
        jsFileLocation:"//localhost:82/revslider/revslider/public/assets/js/",
        sliderLayout:"fullscreen",
        dottedOverlay:"none",
        delay:20000,
        navigation: {
            onHoverStop:"off",
        },
        responsiveLevels:[1240,1024,768,480],
        visibilityLevels:[1240,1024,768,480],
        gridwidth:[1240,1024,768,480],
        gridheight:[868,600,500,400],
        lazyType:"none",
        parallax: {
            type:"mouse",
            origo:"slidercenter",
            speed:2000,
            speedbg:0,
            speedls:0,
            levels:[2,3,4,5,6,7,12,16,10,50,47,48,49,50,51,55],
        },
        shadow:0,
        spinner:"off",
        stopLoop:"on",
        stopAfterLoops:0,
        stopAtSlide:1,
        shuffle:"off",
        autoHeight:"off",
        fullScreenAutoWidth:"off",
        fullScreenAlignForce:"off",
        fullScreenOffsetContainer: "",
        disableProgressBar:"on",
        hideThumbsOnMobile:"off",
        hideSliderAtLimit:0,
        hideCaptionAtLimit:0,
        hideAllCaptionAtLilmit:0,
        debugMode:false,
        fallbacks: {
            simplifyAll:"off",
            nextSlideOnWindowFocus:"off",
            disableFocusListener:false,
        }
    });


});