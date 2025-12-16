<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>@yield('title', 'تحت الصيانة - ريفا العقارية')</title>

        @vite('resources/css/app.css')
        @vite(['resources/js/app.js'])

        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
        <link rel="stylesheet" href="{{ asset('frontend/css/plugins.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/css/colors/navy.css') }}">
        <link rel="preload" href="{{ asset('frontend/css/fonts/urbanist.css') }}" as="style" onload="this.rel='stylesheet'">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@100;200;300;400;500;600;700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    </head>
    <body>

        <div class="content-wrapper">
            <section class="wrapper bg-light" dir="rtl">
                <div class="container pt-15 pt-md-20" style="height:100vh; display:flex; align-items:center; justify-content:center;">
                    <div class="row m-auto">
                        <div class="col-lg-8 col-xl-7 col-xxl-6 mx-auto text-center">
                            <h1 class="mb-3">الموقع تحت الصيانة</h1>
                            <p class="lead mb-7 px-md-12 px-lg-5 px-xl-7">نقوم حالياً ببعض التحديثات لتحسين تجربتكم. سنعود قريباً.</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </body>
</html>
