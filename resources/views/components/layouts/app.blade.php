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

        <link rel="shortcut icon" href="{{ asset('frontend/img/logoyy.png') }}">
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

            {{ $slot }}

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

        <x-livewire-alert::flash />
        <x-livewire-alert::scripts />
        {{-- <script>
            function toggleSidebar() {
                const sidebar = document.querySelector('.sidebar');
                sidebar.classList.toggle('show');

                if (sidebar.classList.contains('show')) {
                    sidebar.classList.remove('d-none');
                    document.body.style.overflow = 'hidden';
                } else {
                    setTimeout(() => {
                        sidebar.classList.add('d-none');
                        document.body.style.overflow = 'auto';
                    }, 300);
                }
            }
        </script> --}}
        <script>
            document.getElementById('fullscreen-btn').addEventListener('click', function () {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen();
                    localStorage.setItem('isFullScreen', 'true');
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                        localStorage.setItem('isFullScreen', 'false');
                    }
                }
            });

            // تحقق من حالة الشاشة الكاملة عند تحميل الصفحة
            window.addEventListener('load', function () {
                if (localStorage.getItem('isFullScreen') === 'true') {
                    document.documentElement.requestFullscreen();
                    document.getElementById('fullscreen-btn').innerText = "Exit Full Screen";
                }
            });

        </script>
        @stack('scripts')
    </body>
</html>
