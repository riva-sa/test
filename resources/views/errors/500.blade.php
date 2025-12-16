<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>@yield('title', 'حدث خطأ ما - ريفا العقارية')</title>

        <!-- SEO Meta Tags -->
        <meta name="description" content="@yield('description', 'ريفا العقارية')">
        <meta name="keywords" content="@yield('keywords', 'ريفا العقارية')">

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
        @livewireStyles

        @stack('styles')
    </head>
    <body>

        <div class="content-wrapper">

            @livewire('frontend.partials.nav-bar')
            
            <section class="wrapper bg-light" dir="rtl" style="background:url('{{ asset('frontend/img/error page.png') }}');background-repeat: no-repeat; ">
                <div class="container pt-15 pt-md-20" style="height:83vh">
                    <div class="row m-auto">
                        <!-- /column -->
                        <div class="col-lg-8 col-xl-7 col-xxl-6 mx-auto text-center">
                            <h1 class="mb-3">عفواً، حدث خطأ غير متوقع.</h1>
                            <p class="lead mb-7 px-md-12 px-lg-5 px-xl-7">نواجه حالياً مشكلة تقنية. يرجى المحاولة مرة أخرى لاحقاً أو التواصل معنا إذا استمرت المشكلة.</p>
                            <a href="{{ route('frontend.home') }}" class="btn btn-primary rounded-pill">انتقل إلى الصفحة الرئيسية</a>
                        </div>
                        <!-- /column -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container -->
            </section>
            
            @unless(request()->routeIs('frontend.projects.map') || request()->routeIs('frontend.projects'))
                @livewire('frontend.partials.footer')
            @endunless
        </div>

        @livewireScripts
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </body>
</html>
