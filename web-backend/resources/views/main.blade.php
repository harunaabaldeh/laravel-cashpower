<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{env('APP_NAME')}}</title>
    <!-- Favicon -->
    <link rel="icon" href="{{asset('assets/main/images/favicons/favicon-32x32.png')}}">
    <link rel="stylesheet"  type="text/css" href="{{asset('css/main.css')}}">
</head>
<body data-spy="scroll" data-target=".navbar" data-offset="90">

<!--start loader-->
<div class="loader">
    <div class="cssload-loader">
        <div class="cssload-inner cssload-one"></div>
        <div class="cssload-inner cssload-two"></div>
        <div class="cssload-inner cssload-three"></div>
    </div>
</div>
<!--loader end-->

<!--header start-->
<header>
    <!--header top-->
    <div class="top-header-area bg-transparent text-white text-uppercase">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="header-top-text text-center text-lg-left exo-font">24, Kairaba Avenue, Serrekunda
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="header-top-text text-center text-lg-right exo-font">
                        <a href="#." class="text-white mr-3"><i class="ti ti-mobile mr-2"></i>+220 222 96 83 | </a>
                        <a href="mailto:info@cartispay.com" class="text-white"><i class="ti ti-comment-alt mr-2"></i>info@cartispay.com  </a>
{{--                        <a href="{{route('login')}}" class="text-white"><i class="ti ti-lock-alt mr-2"></i>Login | </a>--}}
{{--                        <a href="{{route('register')}}" class="text-white"><i class="ti ti-lock-alt mr-2"></i>Register</a>--}}
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!--navigation-->
    <nav class="navbar navbar-top navbar-expand-lg top-center-logo nav-line">
        <div class="container">
            <a href="#." title="Logo" class="logo scroll"><img src="{{asset('assets/main/images/logo-main.png')}}" class="logo-dark" alt="logo">
                <img src="{{asset('assets/main/images/logo-transparent.png')}}" alt="logo" class="logo-light default">
            </a>
            <div class="collapse navbar-collapse" id="Heroxnav">
                <ul class="navbar-nav" id="container">
                    <li class="nav-item">
                        <a class="nav-link scroll active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link scroll" href="#feature">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link scroll" href="#team">Team</a>
                    </li>
{{--                    <li class="nav-item">--}}
{{--                        <a class="nav-link scroll" href="#work"></a>--}}
{{--                    </li>--}}
                </ul>
                <ul class="navbar-nav ml-auto">
{{--                    <li class="nav-item">--}}
{{--                        <a class="nav-link scroll" href="#price"></a>--}}
{{--                    </li>--}}
                    <li class="nav-item">
                        <a class="nav-link scroll" href="#contact">contact </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('register')}}">Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('login')}}">Login</a>
                    </li>

                </ul>
            </div>
            <!--side menu open button-->
            <a href="javascript:void(0)" class="d-inline-block sidemenu_btn d-block d-lg-none" id="sidemenu_toggle">
                <span></span> <span></span> <span></span>
            </a>
        </div>
    </nav>
    <!-- side menu -->
    <div class="side-menu">
        <div class="inner-wrapper text-center">
            <span class="btn-close" id="btn_sideNavClose"><i></i><i></i></span>
            <nav class="side-nav w-100">
                <ul class="navbar-nav">


                    <li class="nav-item">
                        <a class="nav-link scroll" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link scroll" href="#feature">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link scroll" href="#team">Team</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link scroll" href="#contact">Contact</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link scroll" href="{{route('login')}}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link scroll" href="{{route('register')}}">Register</a>
                    </li>


                </ul>
            </nav>

            <div class="side-footer text-white w-100 text-center">
                <ul class="social-icons-simple">
                    <li><a class="facebook-bg-hvr" href="javascript:void(0)"><i class="ti ti-facebook"></i> </a> </li>
                    <li><a class="instagram-bg-hvr" href="javascript:void(0)"><i class="ti ti-instagram"></i> </a> </li>
                    <li><a class="twitter-bg-hvr" href="javascript:void(0)"><i class="ti ti-twitter"></i> </a> </li>
                </ul>
                <p class="whitecolor">&copy; {{\Carbon\Carbon::now()->format('Y')}} Cartis Pay</p>
            </div>
        </div>
    </div>
    <a id="close_side_menu" href="javascript:void(0);"></a>
    <!-- End side menu -->
</header>
<!--header end-->

<!--slider start-->
<section id="home" class="p-0">
    <h2 class="d-none" aria-hidden="true">slider</h2>
    <div id="rev_slider_12_1_wrapper" class="rev_slider_wrapper fullscreen-container" data-alias="Herox-one"
         data-source="gallery" style="background:transparent;padding:0px;">
        <!-- START REVOLUTION SLIDER 5.4.8.1 fullscreen mode -->
        <div id="rev_slider_12_1" class="rev_slider fullscreenbanner" style="display:none;" data-version="5.4.8.1">
            <ul>    <!-- SLIDE  -->
                <li data-index="rs-63" data-transition="crossfade" data-slotamount="default" data-hideafterloop="0"
                    data-hideslideonmobile="off" data-easein="default" data-easeout="default" data-masterspeed="500"
                    data-rotate="0"
                    data-saveperformance="off" data-title="Slide" data-param1="01" data-param2="" data-param3=""
                    data-param4="" data-param5="" data-param6="" data-param7="" data-param8="" data-param9=""
                    data-param10="" data-description="">
                    <!-- MAIN IMAGE -->
                    <img src="{{asset('assets/main/images/banner/banner-1.png')}}" alt="" data-bgposition="center center"
                         data-bgfit="cover" data-bgrepeat="no-repeat" class="rev-slidebg" data-no-retina>
                    <!-- LAYERS -->

                    <!-- LAYER NR. 1 -->
                    <div class="tp-caption   tp-resizeme"
                         data-x="['left','left','center','center']" data-hoffset="['35','16','0','0']"
                         data-y="['top','top','middle','middle']" data-voffset="['730','685','0','-70']"
                         data-fontsize="['60','40','40','40']"
                         data-lineheight="['60','50','50','40']"
                         data-width="none"
                         data-height="none"
                         data-whitespace="nowrap"

                         data-type="text"
                         data-responsive_offset="on"

                         data-frames='[{"delay":630,"speed":1500,"frame":"0","from":"z:0;rX:0;rY:0;rZ:0;sX:0.9;sY:0.9;skX:0;skY:0;opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":320,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                         data-textAlign="['inherit','inherit','center','center']"
                         data-paddingtop="[0,0,0,0]"
                         data-paddingright="[0,0,0,0]"
                         data-paddingbottom="[0,0,0,0]"
                         data-paddingleft="[0,0,0,0]"

                         style="z-index: 5; white-space: nowrap; font-size: 60px; line-height: 60px; font-weight: 500; color: #ffffff; letter-spacing: 8px;font-family:Exo;">
                        Instant Transfers
                    </div>

                    <!-- LAYER NR. 2 -->
                    <div class="tp-caption   tp-resizeme"
                         data-x="['center','center','center','center']" data-hoffset="['20' ,'-59','-69','811']"
                         data-y="['top','top','middle','middle']" data-voffset="['730','678','-71','-71']"
                         data-width="none"
                         data-height="none"
                         data-whitespace="nowrap"

                         data-type="text"
                         data-responsive_offset="on"

                         data-frames='[{"delay":1560,"speed":1230,"frame":"0","from":"opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                         data-textAlign="['inherit','inherit','inherit','inherit']"
                         data-paddingtop="[0,0,0,0]"
                         data-paddingright="[0,0,0,0]"
                         data-paddingbottom="[0,0,0,0]"
                         data-paddingleft="[0,0,0,0]"

                         style="z-index: 6; white-space: nowrap; font-size: 40px; line-height: 60px; font-weight: 300; color: rgba(255,255,255,0.49); letter-spacing: 0px;font-family: 'Exo', sans-serif;">
                        <div class="slider-line">|</div>
                    </div>

                    <!-- LAYER NR. 3 -->
                    <div class="tp-caption   tp-resizeme"
                         data-x="['center','center','center','center']" data-hoffset="['322','236','0','0']"
                         data-y="['bottom','bottom','middle','middle']" data-voffset="['90','40','78','0']"
                         data-fontsize="['15','14','14','14']"
                         data-width="['570','557','557','485']"
                         data-height="none"
                         data-whitespace="normal"

                         data-type="text"
                         data-responsive_offset="on"

                         data-frames='[{"delay":1230,"speed":1500,"frame":"0","from":"y:50px;opacity:0;","to":"o:1;","ease":"Power4.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                         data-textAlign="['left','left','center','center']"
                         data-paddingtop="[0,0,0,0]"
                         data-paddingright="[0,0,0,0]"
                         data-paddingbottom="[0,0,0,0]"
                         data-paddingleft="[0,0,0,0]"

                         style="z-index: 7; white-space: nowrap;">
                        <p class="slider-text" style=" font-size: 15px; line-height: 20px; font-weight: 300; color: #ffffff; letter-spacing: 2px;font-family: 'Exo', sans-serif;">
                            Let's Help you put a smile on your loved ones faces</p>
                    </div>
                </li>
                <!-- SLIDE  -->
                <li data-index="rs-69" data-transition="crossfade" data-slotamount="default" data-hideafterloop="0"
                    data-hideslideonmobile="off" data-easein="default" data-easeout="default" data-masterspeed="500"
                    data-rotate="0"
                    data-saveperformance="off" data-title="Slide" data-param1="02" data-param2="" data-param3=""
                    data-param4="" data-param5="" data-param6="" data-param7="" data-param8="" data-param9=""
                    data-param10="" data-description="">
                    <!-- MAIN IMAGE -->
                    <img src="{{asset('assets/main/images/banner/banner-2.png')}}" alt="" data-bgposition="center center"
                         data-bgfit="cover" data-bgrepeat="no-repeat" class="rev-slidebg" data-no-retina>
                    <!-- LAYERS -->

                    <!-- LAYER NR. 1 -->
                    <div class="tp-caption   tp-resizeme"
                         data-x="['left','left','center','center']" data-hoffset="['35','16','0','0']"
                         data-y="['top','top','middle','middle']" data-voffset="['730','685','0','-70']"
                         data-fontsize="['60','40','40','40']"
                         data-lineheight="['60','50','50','40']"
                         data-width="none"
                         data-height="none"
                         data-whitespace="nowrap"

                         data-type="text"
                         data-responsive_offset="on"

                         data-frames='[{"delay":630,"speed":1500,"frame":"0","from":"z:0;rX:0;rY:0;rZ:0;sX:0.9;sY:0.9;skX:0;skY:0;opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":320,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                         data-textAlign="['inherit','inherit','center','center']"
                         data-paddingtop="[0,0,0,0]"
                         data-paddingright="[0,0,0,0]"
                         data-paddingbottom="[0,0,0,0]"
                         data-paddingleft="[0,0,0,0]"

                         style="z-index: 5; white-space: nowrap; font-size: 60px; line-height: 60px; font-weight: 500; color: #ffffff; letter-spacing: 8px;font-family:Exo;">
                         A Soft Bank
                    </div>

                    <!-- LAYER NR. 2 -->
                    <div class="tp-caption   tp-resizeme"
                         data-x="['center','center','center','center']" data-hoffset="['20' ,'-59','-69','811']"
                         data-y="['top','top','middle','middle']" data-voffset="['730','678','-71','-71']"
                         data-width="none"
                         data-height="none"
                         data-whitespace="nowrap"

                         data-type="text"
                         data-responsive_offset="on"

                         data-frames='[{"delay":1560,"speed":1230,"frame":"0","from":"opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                         data-textAlign="['inherit','inherit','inherit','inherit']"
                         data-paddingtop="[0,0,0,0]"
                         data-paddingright="[0,0,0,0]"
                         data-paddingbottom="[0,0,0,0]"
                         data-paddingleft="[0,0,0,0]"

                         style="z-index: 6; white-space: nowrap; font-size: 40px; line-height: 60px; font-weight: 300; color: rgba(255,255,255,0.49); letter-spacing: 0px;font-family: 'Exo', sans-serif;">
                        <div class="slider-line">|</div>
                    </div>

                    <!-- LAYER NR. 3 -->
                    <div class="tp-caption   tp-resizeme"
                         data-x="['center','center','center','center']" data-hoffset="['322','236','0','0']"
                         data-y="['bottom','bottom','middle','middle']" data-voffset="['90','40','78','0']"
                         data-fontsize="['15','14','14','14']"
                         data-width="['570','557','557','500']"
                         data-height="none"
                         data-whitespace="normal"

                         data-type="text"
                         data-responsive_offset="on"

                         data-frames='[{"delay":1230,"speed":1500,"frame":"0","from":"y:50px;opacity:0;","to":"o:1;","ease":"Power4.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                         data-textAlign="['left','left','center','center']"
                         data-paddingtop="[0,0,0,0]"
                         data-paddingright="[0,0,0,0]"
                         data-paddingbottom="[0,0,0,0]"
                         data-paddingleft="[0,0,0,0]"

                         style="z-index: 7; white-space: nowrap;">
                        <p class="slider-text" style=" font-size: 15px; line-height: 20px; font-weight: 300; color: #ffffff; letter-spacing: 2px;font-family: 'Exo', sans-serif;">
                           User cartis pay as your electronic/soft bank - mostly an alternate banking solution</p>
                    </div>
                </li>
                <!-- SLIDE  -->
                <li data-index="rs-68" data-transition="crossfade" data-slotamount="default" data-hideafterloop="0"
                    data-hideslideonmobile="off" data-easein="default" data-easeout="default" data-masterspeed="500"
                    data-rotate="0"
                    data-saveperformance="off" data-title="Slide" data-param1="03" data-param2="" data-param3=""
                    data-param4="" data-param5="" data-param6="" data-param7="" data-param8="" data-param9=""
                    data-param10="" data-description="">
                    <!-- MAIN IMAGE -->
                    <img src="{{asset("assets/main/images/banner/banner-3.jpg")}}" alt="" data-bgposition="center center"
                         data-bgfit="cover" data-bgrepeat="no-repeat" class="rev-slidebg" data-no-retina>
                    <!-- LAYERS -->

                    <!-- LAYER NR. 1 -->
                    <div class="tp-caption   tp-resizeme"
                         data-x="['left','left','center','center']" data-hoffset="['35','16','0','0']"
                         data-y="['top','top','middle','middle']" data-voffset="['730','685','0','-70']"
                         data-fontsize="['60','40','40','40']"
                         data-lineheight="['60','50','50','40']"
                         data-width="none"
                         data-height="none"
                         data-whitespace="nowrap"

                         data-type="text"
                         data-responsive_offset="on"

                         data-frames='[{"delay":630,"speed":1500,"frame":"0","from":"z:0;rX:0;rY:0;rZ:0;sX:0.9;sY:0.9;skX:0;skY:0;opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":320,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                         data-textAlign="['inherit','inherit','center','center']"
                         data-paddingtop="[0,0,0,0]"
                         data-paddingright="[0,0,0,0]"
                         data-paddingbottom="[0,0,0,0]"
                         data-paddingleft="[0,0,0,0]"

                         style="z-index: 5; white-space: nowrap; font-size: 60px; line-height: 60px; font-weight: 500; color: #ffffff; letter-spacing: 8px;font-family:Exo;">
                        Money Transfer
                    </div>

                    <!-- LAYER NR. 2 -->
                    <div class="tp-caption   tp-resizeme"
                         data-x="['center','center','center','center']" data-hoffset="['20' ,'-59','-69','811']"
                         data-y="['top','top','middle','middle']" data-voffset="['730','678','-71','-71']"
                         data-width="none"
                         data-height="none"
                         data-whitespace="nowrap"

                         data-type="text"
                         data-responsive_offset="on"

                         data-frames='[{"delay":1560,"speed":1230,"frame":"0","from":"opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                         data-textAlign="['inherit','inherit','inherit','inherit']"
                         data-paddingtop="[0,0,0,0]"
                         data-paddingright="[0,0,0,0]"
                         data-paddingbottom="[0,0,0,0]"
                         data-paddingleft="[0,0,0,0]"

                         style="z-index: 6; white-space: nowrap; font-size: 40px; line-height: 60px; font-weight: 300; color: rgba(255,255,255,0.49); letter-spacing: 0px;font-family: 'Exo', sans-serif;">
                        <div class="slider-line">|</div>
                    </div>

                    <!-- LAYER NR. 3 -->
                    <div class="tp-caption   tp-resizeme"
                         data-x="['center','center','center','center']" data-hoffset="['322','236','0','0']"
                         data-y="['bottom','bottom','middle','middle']" data-voffset="['90','40','78','0']"
                         data-fontsize="['15','14','14','14']"
                         data-width="['570','557','557','500']"
                         data-height="none"
                         data-whitespace="normal"

                         data-type="text"
                         data-responsive_offset="on"

                         data-frames='[{"delay":1230,"speed":1500,"frame":"0","from":"y:50px;opacity:0;","to":"o:1;","ease":"Power4.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                         data-textAlign="['left','left','center','center']"
                         data-paddingtop="[0,0,0,0]"
                         data-paddingright="[0,0,0,0]"
                         data-paddingbottom="[0,0,0,0]"
                         data-paddingleft="[0,0,0,0]"

                         style="z-index: 7; white-space: nowrap;">
                        <p class="slider-text" style=" font-size: 15px; line-height: 20px; font-weight: 300; color: #ffffff; letter-spacing: 2px;font-family: 'Exo', sans-serif;">
                            Let's Help you send funds to anyone anywhere in the world</p>
                    </div>
                </li>

                <!-- SLIDE  -->
                <li data-index="rs-67" data-transition="crossfade" data-slotamount="default" data-hideafterloop="0"
                    data-hideslideonmobile="off" data-easein="default" data-easeout="default" data-masterspeed="500"
                    data-rotate="0"
                    data-saveperformance="off" data-title="Slide" data-param1="04" data-param2="" data-param3=""
                    data-param4="" data-param5="" data-param6="" data-param7="" data-param8="" data-param9=""
                    data-param10="" data-description="">
                    <!-- MAIN IMAGE -->
                    <img src="{{asset("assets/main/images/banner/banner-4.png")}}" alt="" data-bgposition="center center"
                         data-bgfit="cover" data-bgrepeat="no-repeat" class="rev-slidebg" data-no-retina>
                    <!-- LAYERS -->

                    <!-- LAYER NR. 1 -->
                    <div class="tp-caption   tp-resizeme"
                         data-x="['left','left','center','center']" data-hoffset="['35','16','0','0']"
                         data-y="['top','top','middle','middle']" data-voffset="['730','685','0','-70']"
                         data-fontsize="['60','40','40','40']"
                         data-lineheight="['60','50','50','40']"
                         data-width="none"
                         data-height="none"
                         data-whitespace="nowrap"

                         data-type="text"
                         data-responsive_offset="on"

                         data-frames='[{"delay":630,"speed":1500,"frame":"0","from":"z:0;rX:0;rY:0;rZ:0;sX:0.9;sY:0.9;skX:0;skY:0;opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":320,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                         data-textAlign="['inherit','inherit','center','center']"
                         data-paddingtop="[0,0,0,0]"
                         data-paddingright="[0,0,0,0]"
                         data-paddingbottom="[0,0,0,0]"
                         data-paddingleft="[0,0,0,0]"

                         style="z-index: 5; white-space: nowrap; font-size: 60px; line-height: 60px; font-weight: 500; color: #ffffff; letter-spacing: 8px;font-family:Exo;">
                        Utilities
                    </div>

                    <!-- LAYER NR. 2 -->
                    <div class="tp-caption   tp-resizeme"
                         data-x="['center','center','center','center']" data-hoffset="['20' ,'-59','-69','811']"
                         data-y="['top','top','middle','middle']" data-voffset="['730','678','-71','-71']"
                         data-width="none"
                         data-height="none"
                         data-whitespace="nowrap"

                         data-type="text"
                         data-responsive_offset="on"

                         data-frames='[{"delay":1560,"speed":1230,"frame":"0","from":"opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                         data-textAlign="['inherit','inherit','inherit','inherit']"
                         data-paddingtop="[0,0,0,0]"
                         data-paddingright="[0,0,0,0]"
                         data-paddingbottom="[0,0,0,0]"
                         data-paddingleft="[0,0,0,0]"

                         style="z-index: 6; white-space: nowrap; font-size: 40px; line-height: 60px; font-weight: 300; color: rgba(255,255,255,0.49); letter-spacing: 0px;font-family: 'Exo', sans-serif;">
                        <div class="slider-line">|</div>
                    </div>

                    <!-- LAYER NR. 3 -->
                    <div class="tp-caption   tp-resizeme"
                         data-x="['center','center','center','center']" data-hoffset="['322','236','0','0']"
                         data-y="['bottom','bottom','middle','middle']" data-voffset="['90','40','78','0']"
                         data-fontsize="['15','14','14','14']"
                         data-width="['570','557','557','500']"
                         data-height="none"
                         data-whitespace="normal"

                         data-type="text"
                         data-responsive_offset="on"

                         data-frames='[{"delay":1230,"speed":1500,"frame":"0","from":"y:50px;opacity:0;","to":"o:1;","ease":"Power4.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                         data-textAlign="['left','left','center','center']"
                         data-paddingtop="[0,0,0,0]"
                         data-paddingright="[0,0,0,0]"
                         data-paddingbottom="[0,0,0,0]"
                         data-paddingleft="[0,0,0,0]"

                         style="z-index: 7; white-space: nowrap;">
                        <p class="slider-text" style=" font-size: 15px; line-height: 20px; font-weight: 300; color: #ffffff; letter-spacing: 2px;font-family: 'Exo', sans-serif;">
                            Let's help you pay for Electricity and other Utilities</p>
                    </div>
                </li>
            </ul>
            <div class="tp-bannertimer tp-bottom" style="visibility: hidden !important;"></div>
        </div>
        <div class="slider-social">
            <a href="#." class="facebook-text-hvr"><i class="ti ti-facebook" aria-hidden="true"></i></a>
            <a href="#." class="twitter-text-hvr"><i class="ti ti-twitter-alt" aria-hidden="true"></i></a>
            <a href="#." class="instagram-text-hvr"><i class="ti ti-instagram" aria-hidden="true"></i></a>
        </div>

        <a href="#feature" class="scroll-down scroll d-none d-lg-block">
            <div class="triangle-down"></div>
        </a>

    </div>
</section>
<!--slider end-->

<!--feature start-->
<section id="feature" class="feature circle-top pb-0">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-xs-5">
                <div class="feature-box text-center">
                    <i class="ti ti-timer feature-icon" aria-hidden="true"></i>
                    <p class="mt-3 mb-3">Instant Money Transfer</p>
                    <h4 class="text-capitalize">Reach Your Loved Ones Instantly Through an innovative Robust Platform</h4>
                    <span class="hr-line mt-4 mb-4"></span>
                    <a href="#." class="mb-3 mb-lg-0">Read More</a>
                </div>
            </div>
            <div class="col-md-4 mb-xs-5">
                <div class="feature-box text-center">
                    <i class="ti ti-target feature-icon" aria-hidden="true"></i>
                    <p class="mt-3 mb-3">Cross - Border</p>
                    <h4 class="text-capitalize">
                        cartis pay accounts can be used anytime, anywhere in the world
                    </h4>
                    <span class="hr-line mt-4 mb-4"></span>
                    <a href="#." class="mb-3 mb-lg-0">Read More</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <i class="ti ti-pulse feature-icon" aria-hidden="true"></i>
                    <p class="mt-3 mb-3">Utility Management</p>
                    <h4 class="text-capitalize">
                        cartis pay Integrates, Utility solutions such as Electricity and Airtime
                    </h4>
                    <span class="hr-line mt-4 mb-4"></span>
                    <a href="#." class="mb-3 mb-lg-0">Read More</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!--feature end-->

<!--team start-->
<section id="team" class="team-two pb-0">
    <div class="container sm-container-full">
        <div class="row">
            <div class="col-sm-12">
                <div class="title text-center pb-5">
                    <h2 class="font-weight-600 m-0">TEAM</h2>
                    <span class="hr-line mt-4 mb-4"></span>
                    <p class="mb-4">A Product is only as great as the it's team, that is why we've assembled a most brilliant team to deliver cartis pay to our cherished customers</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="tema-box-two-outer">
                    <!-- team carousel slider -->
                    <div class="owl-carousel owl-theme owl-team">
                        <!-- first slide -->
                        <div class="team-box-two-inner d-flex align-items-center item">
                            <div class="team-two-about">
                                <!--change also navPrev h4 text-->
                                <h4 class="font-weight-bold mb-3">Kojo Mills Robertson</h4>
                                <h6 class="font-weight-bold pb-3">CEO & Founder</h6>
                                <span class="hr-line ml-0 mt-4 mb-4"></span>
                                <p>
                                    Kojo Mills Robertson, a man who has vast knowledge in the area of financial services, is the Chief Executive Officer of the company. He is a team player and knows how to get his team to deliver top quality services to their valued customers.
                                </p>
                                <span class="hr-line ml-0 mt-4 mb-4"></span>
                                <!--team social-->
                                <div class="team-two-social">
                                    <a href="javascript:void(0)" class="facebook-text-hvr"><i class="ti ti-facebook" aria-hidden="true"></i></a>
                                    <a href="javascript:void(0)" class="twitter-text-hvr"><i class="ti ti-twitter-alt" aria-hidden="true"></i></a>
                                    <a href="javascript:void(0)" class="instagram-text-hvr"><i class="ti ti-instagram" aria-hidden="true"></i></a>
                                </div>
                            </div>
                            <!--team image-->
                            <div class="team-two-image">
                                <img src="{{asset("assets/main/images/team/team-img1.png")}}" alt="image">
                            </div>
                        </div>
                        <!-- second slide -->
                        <div class="team-box-two-inner d-flex align-items-center item">
                            <div class="team-two-about">
                                <h4 class="font-weight-bold mb-3">Modou lamin Jabang</h4>
                                <h6 class="font-weight-bold pb-3">Country Manager</h6>
                                <span class="hr-line ml-0 mt-4 mb-4"></span>
                                <p>
                                    Modou Lamin Jabang, is hardworking young man with the passion to achieve excellence in his duties and responsibilities. He is very smart in decision making and above all he is an asset to the company
                                </p>
                                <span class="hr-line ml-0 mt-4 mb-4"></span>
                                <!--team social-->
                                <div class="team-two-social">
                                    <a href="javascript:void(0)" class="facebook-text-hvr"><i class="ti ti-facebook" aria-hidden="true"></i></a>
                                    <a href="javascript:void(0)" class="twitter-text-hvr"><i class="ti ti-twitter-alt" aria-hidden="true"></i></a>
                                    <a href="javascript:void(0)" class="instagram-text-hvr"><i class="ti ti-instagram" aria-hidden="true"></i></a>
                                </div>
                            </div>
                            <!--team image-->
                            <div class="team-two-image">
                                <img src="{{asset('assets/main/images/team/team-img2.png')}}" alt="image">
                            </div>
                        </div>
                        <!-- third slide -->
                        <div class="team-box-two-inner d-flex align-items-center item">
                            <div class="team-two-about">
                                <!--change also navNext h4 text-->
                                <h4 class="font-weight-bold mb-3">Fatima Sillah</h4>
                                <h6 class="font-weight-bold pb-3">Chief Brand Officer</h6>
                                <span class="hr-line ml-0 mt-4 mb-4"></span>
                                <p>
                                    Fatima Sillah, a well known consultant with vast experience in PR and also well connected to so many high profile individuals and companies, she has what it takes to move the company to a higher level. With her level of education, dedication, innovative way of doing things and background, this company is on a path to achieving something really big.
                                </p>
                                <span class="hr-line ml-0 mt-4 mb-4"></span>
                                <!--team social-->
                                <div class="team-two-social">
                                    <a href="javascript:void(0)" class="facebook-text-hvr"><i class="ti ti-facebook" aria-hidden="true"></i></a>
                                    <a href="javascript:void(0)" class="twitter-text-hvr"><i class="ti ti-twitter-alt" aria-hidden="true"></i></a>
                                    <a href="javascript:void(0)" class="instagram-text-hvr"><i class="ti ti-instagram" aria-hidden="true"></i></a>
                                </div>
                            </div>
                            <!--team image-->
                            <div class="team-two-image">
                                <img src="{{asset('assets/main/images/team/team-img3.jpg')}}" alt="image">
                            </div>
                        </div>
                    </div>
                    <!-- previous slide thumbnail and name -->
                    <div class="navPrev">
                    <span>
                         <!-- same as first slider img src -->
                    <img src="{{asset('assets/main/images/team/team-img3.jpg')}}" alt="">
                    </span>
                        <a href="" class='team-two-left'>
                            <div class='team-two-left-nav'>
                                <i class='ti ti-angle-left'></i>
                                <span class='team-verticle-line'></span>
                                <!-- same as first slider h4 text -->
                                <h4 class="font-weight-600">Fatima</h4>
                            </div>
                        </a>
                    </div>
                    <!-- next slide thumbnail and name -->
                    <div class="navNext">
                    <span>
                        <!-- same as third slider img src -->
                    <img src="{{asset('assets/main/images/team/team-img2.png')}}" alt="">
                    </span>
                        <a href="" class='team-two-right'>
                            <div class='team-two-right-nav'>
                                <i class='ti ti-angle-right'></i>
                                <span class='team-verticle-line'></span>
                                <!-- same as third slider h4 text -->
                                <h4 class="font-weight-600">Modou lamin Jabang</h4>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--team end-->

<!--work start-->
{{--<section id="work" class="our-work pb-0">--}}
{{--    <div class="row">--}}
{{--        <div class="col-sm-12">--}}
{{--            <div class="title text-center pb-5">--}}
{{--                <h2 class="font-weight-600 m-0">PORTFOLIO</h2>--}}
{{--                <span class="hr-line mt-4 mb-4"></span>--}}
{{--                <p class="mb-4">Frameworks to provide a robust synopsis.</p>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <div class="row">--}}
{{--        <div class="col-sm-12">--}}
{{--            <div class="content-carousel">--}}
{{--                <div class="owl-carousel owl-work">--}}
{{--                    <div> <img src="images/work-img3.jpg" alt="image"> </div>--}}
{{--                    <div> <img src="images/work-img2.jpg" alt="image"> </div>--}}
{{--                    <div> <img src="images/work-img1.jpg" alt="image"> </div>--}}
{{--                    <div> <img src="images/work-img4.jpg" alt="image"> </div>--}}
{{--                    <div> <img src="images/work-img7.jpg" alt="image"> </div>--}}
{{--                    <div> <img src="images/work-img6.jpg" alt="image"> </div>--}}
{{--                    <div> <img src="images/work-img5.jpg" alt="image"> </div>--}}
{{--                    <div> <img src="images/work-img2.jpg" alt="image"> </div>--}}
{{--                    <div> <img src="images/work-img3.jpg" alt="image"> </div>--}}
{{--                    <div> <img src="images/work-img4.jpg" alt="image"> </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--        </div>--}}
{{--    </div>--}}
{{--</section>--}}
<!--work end-->

<!--price-->
{{--<section id="price" class="price text-center">--}}
{{--    <div class="container">--}}
{{--        <div class="row mb-3">--}}
{{--            <div class="col-sm-12">--}}
{{--                <div class="title text-center pb-5">--}}
{{--                    <h2 class="font-weight-600 m-0">PRICE</h2>--}}
{{--                    <span class="hr-line mt-4 mb-4"></span>--}}
{{--                    <p class="mb-4">Frameworks to provide a robust synopsis.</p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="row">--}}
{{--            <div class="col-lg-4 mb-4">--}}
{{--                <div class="price-item">--}}
{{--                    <h2 class="font-weight-bold poppins-font mb-2">$19</h2>--}}
{{--                    <h5 class="mb-3 poppins-font font-weight-bold">Standard</h5>--}}
{{--                    <ul class="p-0 price-list list-unstyled">--}}
{{--                        <li>4 Template Design</li>--}}
{{--                        <li>5 Hosting</li>--}}
{{--                        <li>10 Email Address</li>--}}
{{--                        <li>15 Free Images</li>--}}
{{--                        <li>2 Print Template</li>--}}
{{--                    </ul>--}}
{{--                    <a href="javascript:void(0)" class="btn btn-large btn-transparent-gray mt-4">Getting Now</a>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-lg-4 mb-4">--}}
{{--                <div class="price-item center">--}}
{{--                    <div class="price-offer"><h6 class="">Best Choice</h6></div>--}}
{{--                    <h2 class="font-weight-bold poppins-font mb-3">$19</h2>--}}
{{--                    <h5 class="mb-4 poppins-font font-weight-bold">Standard</h5>--}}
{{--                    <ul class="p-0 price-list list-unstyled">--}}
{{--                        <li>4 Template Design</li>--}}
{{--                        <li>5 Hosting</li>--}}
{{--                        <li>10 Email Address</li>--}}
{{--                        <li>15 Free Images</li>--}}
{{--                        <li>2 Print Template</li>--}}
{{--                    </ul>--}}
{{--                    <a href="javascript:void(0)" class="btn btn-large btn-transparent-white mt-5">Getting Now</a>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-lg-4">--}}
{{--                <div class="price-item">--}}
{{--                    <h2 class="font-weight-bold poppins-font mb-2">$19</h2>--}}
{{--                    <h5 class="mb-3 poppins-font font-weight-bold">Standard</h5>--}}
{{--                    <ul class="p-0 price-list list-unstyled">--}}
{{--                        <li>4 Template Design</li>--}}
{{--                        <li>5 Hosting</li>--}}
{{--                        <li>10 Email Address</li>--}}
{{--                        <li>15 Free Images</li>--}}
{{--                        <li>2 Print Template</li>--}}
{{--                    </ul>--}}
{{--                    <a href="javascript:void(0)" class="btn btn-large btn-transparent-gray mt-4">Getting Now</a>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</section>--}}
<!--price end-->

<!--parallax start-->
<section class="parallax video-parallax text-center text-md-left bg-1">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 pt-lg-5 pb-lg-5">
                <h3 class="text-white text-capitalize">A Solution Bread From the heart Of Service</h3>
                <span class="hr-line mt-4 mb-4 ml-md-0"></span>
                <h4 class="text-white font-weight-500 mb-4">Our Solution is about driving financial inclusion, see why we care that much</h4>
                <a data-fancybox href="https://www.youtube.com/watch?v=BGNDQtHyasw"
                   class="text-white">click to <span class="button-play-two"><i class="ti ti-control-play"></i></span> watch</a>
            </div>
        </div>
    </div>
</section>
<!--parallax end-->

<!--testimonial start-->
{{--<section id="testimonial" class="pb-0">--}}
{{--    <div class="container">--}}
{{--        <div class="row">--}}
{{--            <div class="col-sm-12">--}}
{{--                <div class="title text-center">--}}
{{--                    <h2 class="font-weight-600 m-0">Testimonials</h2>--}}
{{--                    <span class="hr-line mt-4 mb-4"></span>--}}
{{--                    <p>Frameworks to provide a robust synopsis.</p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

{{--        <div class="row">--}}
{{--            <div class="col-sm-12">--}}
{{--                <div class="owl-testimonial owl-carousel owl-theme">--}}
{{--                    <div class="testimonial-item item text-center">--}}
{{--                        <span class="testimonial-quote"><i class="ti ti-quote-right" aria-hidden="true"></i></span>--}}
{{--                        <div class="testimonial-text mb-3"><p>Donec semper euismod nisi quis feugiat. Nullam finibus metus eget orci volutpat porta. Morbi quis arcu vulputate, dignissim mi ac, varius magna.</p></div>--}}
{{--                        <div class="testimonial-photo"><img src="images/testimonial-1.jpg" alt="image"></div>--}}
{{--                        <h5 class="text-capitalize mt-3 mb-0">Jhony Deev</h5>--}}
{{--                        <p class="text-small mb-0">Executive Manager</p>--}}
{{--                    </div>--}}
{{--                    <div class="testimonial-item item text-center">--}}
{{--                        <span class="testimonial-quote"><i class="ti ti-quote-right" aria-hidden="true"></i></span>--}}
{{--                        <div class="testimonial-text mb-3"><p>Donec semper euismod nisi quis feugiat. Nullam finibus metus eget orci volutpat porta. Morbi quis arcu vulputate, dignissim mi ac, varius magna.</p></div>--}}
{{--                        <div class="testimonial-photo"><img src="images/testimonial-2.jpg" alt="image"></div>--}}
{{--                        <h5 class="text-capitalize mt-3 mb-0">Teena Walkin</h5>--}}
{{--                        <p class="text-small mb-0">Executive Manager</p>--}}
{{--                    </div>--}}
{{--                    <div class="testimonial-item item text-center">--}}
{{--                        <span class="testimonial-quote"><i class="ti ti-quote-right" aria-hidden="true"></i></span>--}}
{{--                        <div class="testimonial-text mb-3"><p>Donec semper euismod nisi quis feugiat. Nullam finibus metus eget orci volutpat porta. Morbi quis arcu vulputate, dignissim mi ac, varius magna.</p></div>--}}
{{--                        <div class="testimonial-photo"><img src="images/testimonial-3.jpg" alt="image"></div>--}}
{{--                        <h5 class="text-capitalize mt-3 mb-0">Teena Walkin</h5>--}}
{{--                        <p class="text-small mb-0">Executive Manager</p>--}}
{{--                    </div>--}}
{{--                    <div class="testimonial-item item text-center">--}}
{{--                        <span class="testimonial-quote"><i class="ti ti-quote-right" aria-hidden="true"></i></span>--}}
{{--                        <div class="testimonial-text mb-3"><p>Donec semper euismod nisi quis feugiat. Nullam finibus metus eget orci volutpat porta. Morbi quis arcu vulputate, dignissim mi ac, varius magna.</p></div>--}}
{{--                        <div class="testimonial-photo"><img src="images/testimonial-3.jpg" alt="image"></div>--}}
{{--                        <h5 class="text-capitalize mt-3 mb-0">Teena Walkin</h5>--}}
{{--                        <p class="text-small mb-0">Executive Manager</p>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</section>--}}
<!--testimonial end-->

<!-- our client -->
{{--<section id="client" class="our-client">--}}
{{--    <h2 class="d-none" aria-hidden="true">slider</h2>--}}
{{--    <div class="container">--}}
{{--        <div class="row">--}}
{{--            <div class="col-md-12 col-sm-12 p-0">--}}
{{--                <div class="owl-carousel partners-slider">--}}
{{--                    <div class="item">--}}
{{--                        <div class="logo-item"> <img alt="image" src="images/client-one.png"></div>--}}
{{--                    </div>--}}
{{--                    <div class="item">--}}
{{--                        <div class="logo-item"><img alt="image" src="images/client-two.png"></div>--}}
{{--                    </div>--}}
{{--                    <div class="item">--}}
{{--                        <div class="logo-item"> <img alt="image" src="images/client-one.png"></div>--}}
{{--                    </div>--}}
{{--                    <div class="item">--}}
{{--                        <div class="logo-item"><img alt="image" src="images/client-two.png"></div>--}}
{{--                    </div>--}}
{{--                    <div class="item">--}}
{{--                        <div class="logo-item"> <img alt="image" src="images/client-one.png"></div>--}}
{{--                    </div>--}}
{{--                    <div class="item">--}}
{{--                        <div class="logo-item"><img alt="image" src="images/client-two.png"></div>--}}
{{--                    </div>--}}
{{--                    <div class="item">--}}
{{--                        <div class="logo-item"> <img alt="image" src="images/client-one.png"></div>--}}
{{--                    </div>--}}
{{--                    <div class="item">--}}
{{--                        <div class="logo-item"><img alt="image" src="images/client-two.png"></div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</section>--}}
<!-- our client end -->

<!--blog-->
{{--<section id="blog" class="blog-list bg-light text-center text-md-left">--}}
{{--    <h2 class="d-none" aria-hidden="true">slider</h2>--}}
{{--    <div class="container">--}}
{{--        <div class="row">--}}
{{--            <!-- blog-item one -->--}}
{{--            <div class="col-lg-3 col-md-6 mb-3 mb-xs-5">--}}
{{--                <div class="image">--}}
{{--                    <img alt="image" src="images/blog-img-1.jpg">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-lg-3 col-md-6 mb-xs-5">--}}
{{--                <div class="blog-box">--}}
{{--                    <h4 class="text-capitalize mb-4">360° arial view</h4>--}}
{{--                    <p class="mb-3 mb-xs-4">Leverage agile frameworks to provide a robust synopsis for high level overviews.</p>--}}
{{--                    <a class="btn btn-large btn-pink mb-xs-4" href="blog-left.html"> Read More</a>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <!-- blog-item two -->--}}
{{--            <div class="col-lg-3 col-md-6 mb-xs-5">--}}
{{--                <div class="image">--}}
{{--                    <img alt="image" src="images/blog-img-2.jpg">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-lg-3 col-md-6">--}}
{{--                <div class="blog-box">--}}
{{--                    <h4 class="text-capitalize mb-4">360° arial view</h4>--}}
{{--                    <p class="mb-3 mb-xs-4">Leverage agile frameworks to provide a robust synopsis for high level overviews.</p>--}}
{{--                    <a class="btn btn-large btn-pink" href="blog-left.html"> Read More</a>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</section>--}}
<!--blog end-->

<!-- Contact US -->
<section id="contact" class="contact">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="title text-center pb-5">
                    <h2 class="font-weight-600 m-0">CONTACT</h2>
                    <span class="hr-line mt-4 mb-4"></span>
                    <p class="mb-4">Get In Touch, Any day, anytime; We'll be glad to assist you.</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-12 mb-xs-5">
                <form class="getin_form" onsubmit="return false;">
                    <div class="row">

                        <div class="col-sm-12" id="result"></div>

                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <input class="form-control" type="text" placeholder="First Name : " required id="first_name" name="first_name">
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <input class="form-control" type="text" placeholder="Last Name : " required id="last_name" name="last_name">
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <input class="form-control" type="email" placeholder="Email : " required id="email" name="email">
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="form-group">
                                <input class="form-control" type="tel" placeholder="Phone : " id="phone" name="phone">
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12">
                            <div class="form-group mb-4">
                                <textarea class="form-control" placeholder="Message" id="message" name="message"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-large btn-pink w-100" id="submit_btn">submit request</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6 col-sm-12 pl-4">
                <p class="mb-lg-5 mb-4 mt-2">As a membership site we're always focused on reducing churn and increasing satisfaction. We know that collecting feedback from customers throughout the customer's lifecycle has allowed us to achieve both. ~ James Bake</p>
                <div class="row">
                    <div class="col-md-6 col-sm-6 our-address mb-4">
                        <h6 class="mb-3 font-weight-600">Our Address</h6>
                        <p class="mb-2">24, KAIRABA AVENUE, SERREKUNDA, The Gambia </p>
                        <a class="pickus" href="#." data-text="Get Directions">Get Directions</a>
                    </div>
                    <div class="col-md-6 col-sm-6 our-address mb-4">
                        <h6 class="mb-3 font-weight-600">Our Phone</h6>
                        <p class="mb-2">Phone No. +220 222 96 83 <br>Mobile No. +220 222 96 83</p>
                        <a class="pickus" href="#." data-text="Call Us">Call Us</a>
                    </div>
                    <div class="col-md-6 col-sm-6 our-address mb-4">
                        <h6 class="mb-3 font-weight-600">Our Email</h6>
                        <p class="mb-2">Main Email : sales@cartispay.com <span class="block">Inquiries : info@cartispay.com</span> </p>
                        <a class="pickus" href="#." data-text="Send a Message">Send a Message</a>
                    </div>
                    <div class="col-md-6 col-sm-6 our-address">
                        <h6 class="mb-3 font-weight-600">Our Support</h6>
                        <p class="mb-2">Main Support : inquiries@cartispay.com <span>Sales : support@cartispay.com</span> </p>
                        <a class="pickus" href="#." data-text="Open a Ticket">Open a Ticket</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Contact US Ends-->

<!-- Map -->
<section class="p-0 bg-light text-center">
    <h2 class="d-none" aria-hidden="true">slider</h2>
    <!--Location Map here-->
    <div id="map" class="map-container"></div>

    <!--Bottom Logo-->
    <div class="logo-icon">
        <img src="{{asset('assets/main/images/logo-main.png')}}" alt="image">
    </div>
</section>
<!-- Map End -->

<!-- Footer -->
<footer class="footer-padding bg-dark font-weight-600">
    <div class="container">
        <div class="row text-center text-md-left">
            <div class="col-md-4 mb-xs-4">
                <h4 class="text-white font-weight-500">ADDRESS</h4>
                <span class="hr-line mt-4 mb-4 ml-md-0"></span>
                <p class="mb-4">24, KAIRABA AVENUE, SERREKUNDA<br>
                    The Gambia</p>
                <p class="mb-4">TEL: <a href="javascript:void(0)"> +220 222 96 83</a><br>
                    FAX: <a href="javascript:void(0)">+220 222 96 83</a></p>
                <p>EMAIL:<a href="javascript:void(0)"> INFO@cartispay.COM</a></p>
            </div>

            <div class="col-md-4 mb-xs-4">
                <h4 class="text-white font-weight-500">COMPANY</h4>
                <span class="hr-line mt-4 mb-4 ml-md-0"></span>
                <ul class="list-unstyled">
                    <li><a href="javascript:void(0)">About</a></li>
                    <li><a href="javascript:void(0)">Leadership</a></li>
                    <li><a href="javascript:void(0)">Blog</a></li>
                    <li><a href="javascript:void(0)">Careers</a></li>
                    <li><a href="javascript:void(0)">Partner Network</a></li>
                    <li><a href="javascript:void(0)">Referral Program</a></li>
                    <li><a href="javascript:void(0)">Events</a></li>
                    <li><a href="javascript:void(0)">Legal Security</a></li>
                </ul>
            </div>

            <div class="col-md-4">
                <h4 class="text-white font-weight-500">SUPPORT</h4>
                <span class="hr-line mt-4 mb-4 ml-md-0"></span>
                <ul class="list-unstyled">
                    <li><a href="javascript:void(0)">Affiliates</a></li>
                    <li><a href="javascript:void(0)">Demo</a></li>
                    <li><a href="javascript:void(0)">Help Center</a></li>
                    <li><a href="javascript:void(0)">FAQ</a></li>
                    <li><a href="javascript:void(0)">Testimonials</a></li>
                    <li><a href="javascript:void(0)">Blog</a></li>
                    <li><a href="javascript:void(0)">Press</a></li>
                    <li><a href="javascript:void(0)">Email Us</a></li>
                </ul>
            </div>

            <div class="col-sm-12 mt-lg-5 mt-2 text-center">
                <p class="mb-4 mt-3">© {{\Carbon\Carbon::now()->format('Y')}} cartis pay LLC. All rights reserved.</p>
                <div class="footer-social">
                    <a href="javascript:void(0)"><i class="ti ti-twitter-alt" aria-hidden="true"></i></a>
                    <a href="javascript:void(0)"><i class="ti ti-facebook" aria-hidden="true"></i></a>
{{--                    <a href="javascript:void(0)"><i class="ti ti-pinterest-alt" aria-hidden="true"></i></a>--}}
                    <a href="javascript:void(0)"><i class="ti ti-instagram" aria-hidden="true"></i></a>
                </div>
            </div>

        </div>
    </div>
</footer>
<!-- Footer End -->

<!-- start scroll to top -->
<a class="scroll-top-arrow" href="javascript:void(0);"><i class="ti ti-angle-up"></i></a>
<!-- end scroll to top  -->


<!-- map -->
{{--<script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_API_KEY')}}"></script>--}}
<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
<!-- custom script -->
<script src="{{asset('js/main.js')}}"></script>

</body>
</html>
