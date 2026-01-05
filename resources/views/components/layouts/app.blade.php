<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        {{-- <title>{{ $title ?? 'ريفا العقارية' }}</title> --}}

        <title>@yield('title', $title ?? 'ريفا العقارية')</title>
        <link rel="stylesheet" href="https://sets.hugeicons.com/YOUR-SET-ID.css" crossorigin="anonymous">
        <!-- SEO Meta Tags -->
        <meta name="description" content="@yield('description', 'رحلة سكن')">
        <meta name="keywords" content="@yield('keywords', 'ريفا العقارية')">
        <!-- Open Graph Tags -->
        <meta property="og:title" content="@yield('og:title', 'ريفا العقارية')" />
        <meta property="og:description" content="@yield('og:description', 'رحلة سكن')" />
        <meta property="og:image" content="@yield('og:image', asset('frontend/img/riva2.jpg'))" />

        <!-- Twitter Card Tags -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="@yield('twitter:title', 'ريفا العقارية')">
        <meta name="twitter:description" content="@yield('twitter:description', 'رحلة سكن')">
        <meta name="twitter:image" content="@yield('twitter:image', asset('frontend/img/riva2.jpg'))">

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
        <script src="https://cdn.jsdelivr.net/npm/progressbar.js"></script>

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

        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-THCCWX7G');</script>
        <!-- End Google Tag Manager -->

    </head>
    <body>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-THCCWX7G"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
            <!-- End Google Tag Manager (noscript) -->
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
        {{-- <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

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

        @stack('scripts')

        <!-- WhatsApp Fixed Icon -->
        {{-- <a href="https://wa.me/{{ setting('site_phone') }}" class="whatsapp-float glass-card" target="_blank">
            <i class="uil uil-whatsapp"></i>
        </a> --}}

        <!-- WhatsApp Fixed Icon -->
        @if(!Request::is('project/*'))
            <a href="https://wa.me/{{ setting('site_phone') }}" class="whatsapp-float glass-card" target="_blank">
                <i class="uil uil-whatsapp"></i>
            </a>
        @endif
        <script>
            (function(){
                function pushEvent(name, data){
                    if (window.ttq && typeof window.ttq.track === 'function') {
                        window.ttq.track(name);
                    }
                    if (window.dataLayer) {
                        window.dataLayer.push(Object.assign({ event: name }, data || {}));
                    }
                }
                document.addEventListener('click', function(e){
                    var el = e.target.closest('a.whatsapp-float');
                    if (el) {
                        pushEvent('WhatsAppClick', { context: 'floating_button' });
                    }
                });
                if (window.Livewire && typeof window.Livewire.on === 'function') {
                    window.Livewire.on('clientTrack', function(payload){
                        if (payload && payload.event) {
                            pushEvent(payload.event, payload);
                        }
                    });
                }
            })();
        </script>
    </body>
</html>
