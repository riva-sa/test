<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ريفا') }} - بوابة الوسطاء</title>
    <link rel="shortcut icon" href="{{ asset('frontend/img/logoyy.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/broker.css'])
    @livewireStyles
</head>
<body class="bg-gray-50 min-h-screen">
    {{ $slot }}
    @livewireScripts
</body>
</html>
