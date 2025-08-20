<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ريفا') }} - لوحة التحكم</title>

    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="shortcut icon" href="{{ asset('frontend/img/logoyy.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@100;200;300;400;500;600;700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

    <!-- Styles -->
    @livewireStyles

    <!-- Scripts -->

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#122818',
                            100: '#122818',
                            200: '#122818',
                            300: '#122818',
                            400: '#122818',
                            500: '#122818',
                            600: '#122818',
                            700: '#122818',
                            800: '#122818',
                            900: '#122818',
                        },
                    },
                    fontFamily: {
                        sans: ['IBM Plex Sans Arabic', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    <style>
        .dashboard-card {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        .sidebar-item:hover {
            background-color: rgba(18, 40, 24, 0.1);
        }
        .sidebar-item.active {
            background-color: rgb(249, 250, 251);
        }
        .form-input:focus {
            border-color: #122818;
            box-shadow: 0 0 0 3px rgba(18, 40, 24, 0.15);
        }
        .btn-primary {
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
        }
    </style>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <!-- Chart.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
        <!-- ApexCharts -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.5/apexcharts.min.js"></script>
        <!-- Custom styles -->
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap');

            body {
                font-family: 'Tajawal', sans-serif;
                background-color: #f3f4f6;
            }

            .dashboard-card {
                transition: all 0.3s ease;
            }

            .bg-primary-500 {
                background-color: #4f46e5;
            }

            .text-primary-500 {
                color: #4f46e5;
            }

            .text-primary-600 {
                color: #4338ca;
            }

            .hover\:text-primary-900:hover {
                color: #312e81;
            }

            .bg-blue-100 {
                background-color: #dbeafe;
            }

            .text-blue-800 {
                color: #1e40af;
            }

            .bg-yellow-100 {
                background-color: #fef3c7;
            }

            .text-yellow-800 {
                color: #92400e;
            }

            .bg-green-100 {
                background-color: #d1fae5;
            }

            .text-green-800 {
                color: #065f46;
            }

            .bg-gray-100 {
                background-color: #f3f4f6;
            }

            .text-gray-800 {
                color: #1f2937;
            }

            .bg-blue-500 {
                background-color: #3b82f6;
            }

            .bg-yellow-500 {
                background-color: #eab308;
            }

            .bg-green-500 {
                background-color: #22c55e;
            }

            .bg-gray-500 {
                background-color: #6b7280;
            }

            /* Custom styles for charts */
            .chart-container {
                position: relative;
                min-height: 300px;
            }
        </style>
        @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Component -->
        @livewire('Mannager.Partials.Sidebar')

        <!-- Main content -->
        <div class="flex flex-col flex-1 overflow-hidden">

            <!-- Main content area -->
            <main class="flex-1 overflow-y-auto bg-gray-50">

                {{ $slot }}

            </main>

        </div>
    </div>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/chart.js "></script>
    <script src="{{ asset('frontend/js/tracking.js') }}"></script>
    {{-- inter scripts here --}}
    @stack('scripts')

</body>
</html>