<!DOCTYPE html>
<html lang="{{ \App\Helpers\LocalizationHelper::getHtmlLang() }}" dir="{{ \App\Helpers\LocalizationHelper::getDirection() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', $title ?? __('public.seo.default_title'))</title>

        <!-- Speed up handshakes to the third-party origins used below -->
        <link rel="preconnect" href="https://unpkg.com" crossorigin>
        <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
        <link rel="dns-prefetch" href="https://unpkg.com">
        <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

        <!-- SEO Meta Tags -->
        <meta name="description" content="@yield('description', __('public.seo.default_description'))">
        <meta name="keywords" content="@yield('keywords', __('public.seo.default_keywords'))">
        <!-- Open Graph Tags -->
        <meta property="og:title" content="@yield('og:title', __('public.seo.default_title'))" />
        <meta property="og:description" content="@yield('og:description', __('public.seo.default_description'))" />
        <meta property="og:image" content="@yield('og:image', asset('frontend/img/riva2.jpg'))" />

        <!-- Hreflang & Canonical -->
        {!! \App\Helpers\LocalizationHelper::getAlternateLinks() !!}
        <link rel="canonical" href="{{ \App\Helpers\LocalizationHelper::getCanonicalUrl() }}" />

        <!-- Twitter Card Tags -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="@yield('twitter:title', __('public.seo.default_title'))">
        <meta name="twitter:description" content="@yield('twitter:description', __('public.seo.default_description'))">
        <meta name="twitter:image" content="@yield('twitter:image', asset('frontend/img/riva2.jpg'))">

        <!-- @vite('resources/css/app.css')
        @vite(['resources/js/app.js']) -->

        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
        {{-- Leaflet CSS is render-blocking and only the map / project-single
             pages use it, so each of those pushes it via @stack('styles')
             instead of loading it on every page. --}}

        <link rel="shortcut icon" href="{{ asset('frontend/img/logoyy.png') }}">
        <link rel="stylesheet" href="{{ asset('frontend/css/plugins.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/css/colors/navy.css') }}">
        <link rel="preload" href="{{ asset('frontend/css/fonts/urbanist.css') }}" as="style" onload="this.rel='stylesheet'">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@100;200;300;400;500;600;700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css">

        
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

        <!-- /.page-frame -->
        <div class="progress-wrap">
            <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
                <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
            </svg>
        </div>
        <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
        {{-- Moved out of <head> so it no longer blocks rendering. Must stay
             before theme.js, which relies on the global ProgressBar. --}}
        <script src="https://cdn.jsdelivr.net/npm/progressbar.js"></script>
        <script src="{{ asset('frontend/js/plugins.js') }}"></script>

        <script src="{{ asset('frontend/js/theme.js') }}"></script>

        <script src="{{ asset('vendor/livewire-alert/livewire-alert.js') }}"></script>

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
                function trackToInternal(type, id, event) {
                    var token = document.querySelector('meta[name="csrf-token"]');
                    if (!token || !id) return;
                    fetch('/crm/tracking/track', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token.getAttribute('content')
                        },
                        body: JSON.stringify({ type: type, id: parseInt(id), event: event })
                    }).catch(function(){});
                }
                document.addEventListener('click', function(e){
                    var el = e.target.closest('a.whatsapp-float');
                    if (el) {
                        pushEvent('WhatsAppClick', { context: 'floating_button' });
                        var projectEl = document.querySelector('[data-project-id]');
                        if (projectEl) {
                            trackToInternal('project', projectEl.getAttribute('data-project-id'), 'whatsapp');
                        }
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


        <script>
            window.SudeemWidget = window.SudeemWidget || function() {
                (window.SudeemWidget.q = window.SudeemWidget.q || []).push(arguments);
            };
            window.SudeemWidget.config = {
                agentId: '7f8b132a-abe7-4381-968b-3cfec981cc9d'
            };
        </script>
        <script src="https://app.sudeem.ai/api/widget.js" async></script>
    </body>
</html>
