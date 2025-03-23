<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        {{-- <title>{{ $title ?? 'ريفا العقارية' }}</title> --}}

        <title>@yield('title', $title ?? 'ريفا العقارية')</title>

        <!-- SEO Meta Tags -->
        <meta name="description" content="@yield('description', 'ريفا العقارية')">
        <meta name="keywords" content="@yield('keywords', 'ريفا العقارية')">

        <!-- Open Graph Tags -->
        <meta property="og:title" content="@yield('og:title', 'ريفا')" />
        <meta property="og:description" content="@yield('og:description', 'ريفا العقارية')" />
        <meta property="og:image" content="@yield('og:image', asset('frontend/img/riva.jpg'))" />

        <!-- Twitter Card Tags -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="@yield('twitter:title', 'ريفا')">
        <meta name="twitter:description" content="@yield('twitter:description', 'ريفا العقارية')">
        <meta name="twitter:image" content="@yield('twitter:image', asset('frontend/img/riva.jpg'))">

        @vite('resources/css/app.css')
        @vite(['resources/js/app.js'])

        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

        <link rel="shortcut icon" href="{{ asset('frontend/img/svg/Artboard 16.svg') }}">
        <link rel="stylesheet" href="{{ asset('frontend/css/plugins.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/css/colors/navy.css') }}">
        <link rel="preload" href="{{ asset('frontend/css/fonts/urbanist.css') }}" as="style" onload="this.rel='stylesheet'">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@100;200;300;400;500;600;700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
        {{-- <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css"> --}}
        @livewireStyles

        @stack('styles')

        <!-- Google Analytics and Ads -->
        @if(!empty($settings->google_analytic_code))
            {!! $settings->google_analytic_code !!}
        @endif

        <!-- google_console_code -->
        @if(!empty($settings->google_console_code))
            {!! $settings->google_console_code !!}
        @endif

        @if(!empty($settings->google_adsense_code))
            {!! $settings->google_adsense_code !!}
        @endif

        @if(!empty($settings->google_tag_code))
            {!! $settings->google_tag_code !!}
        @endif

    </head>
    <body>

        <div class="content-wrapper">

            @livewire('frontend.partials.nav-bar')
                <!-- /header -->
            <section class="wrapper bg-light" dir="rtl" style="background:url('{{ asset('frontend/img/error page.png') }}');background-repeat: no-repeat; ">
                <div class="container pt-15 pt-md-20" style="height:83vh">
                    <div class="row m-auto">
                        <!-- /column -->
                        <div class="col-lg-8 col-xl-7 col-xxl-6 mx-auto text-center">
                            <h1 class="mb-3">عفواً، لم يتم العثور على الصفحة.</h1>
                            <p class="lead mb-7 px-md-12 px-lg-5 px-xl-7">الصفحة التي تبحث عنها غير متوفرة أو تم نقلها. جرّب صفحة أخرى أو انتقل إلى الصفحة الرئيسية باستخدام الزر أدناه.</p>
                            <a href="{{ route('frontend.home') }}" class="btn btn-primary rounded-pill">انتقل إلى الصفحة الرئيسية</a>
                        </div>
                        <!-- /column -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container -->
            </section>
            <!-- /section -->
            @unless(request()->routeIs('frontend.projects.map') || request()->routeIs('frontend.projects'))
                @livewire('frontend.partials.footer')
            @endunless
        </div>

        @livewireScripts

        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Add Alpine.js (optional if you're using it) -->
        {{-- <script src="//unpkg.com/alpinejs" defer></script> --}}
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- /.page-frame -->
        <div class="progress-wrap">
            <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
                <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
            </svg>
        </div>
        {{-- <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script> --}}
        <script src="{{ asset('frontend/js/plugins.js') }}"></script>

        <script src="{{ asset('frontend/js/theme.js') }}"></script>

        {{-- <script src="{{ asset('vendor/livewire-alert/livewire-alert.js') }}"></script> --}}

    </body>
</html>
