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
    <script src="{{ mix('js/app.js') }}" defer></script>

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
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-72 bg-white border-l border-gray-200 shadow-lg">
            <!-- Sidebar header -->
            <div class="flex items-center  h-20 px-6 border-b border-gray-200 bg-gradient-to-l from-gray-50 to-white">
                <div class="flex items-center">
                <img src="{{ asset('frontend/img/logoyy.png') }}" width="40px" alt="ريفا" class="ml-3">
                <div class="flex flex-col">
                    <span class="text-xl font-bold text-gray-800">ريفا العقارية</span>
                    <span class="text-xs text-gray-500">لوحة التحكم</span>
                </div>
                </div>
            </div>

            <!-- Sidebar content -->
            <div class="flex flex-col flex-grow px-6 py-6 overflow-y-auto">
                <h3 class="text-xs font-semibold text-gray-500 mb-4 px-2">القائمة الرئيسية</h3>
                <nav class="flex-1 space-y-1">
                    <a href="{{ route('manager.dashboard') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-50 transition-all duration-200 text-gray-700 hover:text-primary-500 sidebar-item {{ request()->routeIs('manager.dashboard') ? 'bg-gray-50 text-primary-500 ' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-3 text-gray-500 group-hover:text-primary-500 {{ request()->routeIs('manager.dashboard') ? 'text-primary-500' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        الرئيسية
                    </a>

                    <a href="{{ route('manager.customerlist') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-50 transition-all duration-200 text-gray-700 hover:text-primary-500 sidebar-item {{ request()->routeIs('manager.customerlist') ? 'bg-gray-50 text-primary-500 ' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-3 text-gray-500 group-hover:text-primary-500 {{ request()->routeIs('manager.customerlist') ? 'text-primary-500' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        العملاء
                    </a>

                    <a href="{{ route('manager.orders') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-50 transition-all duration-200 text-gray-700 hover:text-primary-500 sidebar-item {{ request()->routeIs('manager.orders') ? 'bg-gray-50 text-primary-500 ' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-3 text-gray-500 group-hover:text-primary-500 {{ request()->routeIs('manager.orders') ? 'text-primary-500' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        الطلبات
                    </a>

                    @if (auth()->user()->hasRole('sales_manager') || auth()->user()->hasRole('follow_up'))
                    <a href="{{ route('manager.sales-managers') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-50 transition-all duration-200 text-gray-700 hover:text-primary-500 sidebar-item {{ request()->routeIs('manager.sales-managers') ? 'bg-gray-50 text-primary-500 ' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-3 text-gray-500 group-hover:text-primary-500 {{ request()->routeIs('manager.sales-managers') ? 'text-primary-500' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        مندوبي المبيعات
                    </a>
                    @endif


                    <!-- Quick Actions Section -->
                    <div class="px-4 py-2">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">إجراءات سريعة</h3>
                    </div>

                    <!-- New Order Link -->
                    <a href="{{ route('manager.create-order') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200
                        {{ request()->routeIs('manager.create-order') ? 'bg-primary-600 text-white' : 'bg-primary-500 bg-opacity-10 text-primary-600 hover:bg-opacity-20' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        إضافة طلب جديد
                    </a>
                </nav>
            </div>

            <!-- Sidebar footer - User info -->
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                @if(auth()->check())
                <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-primary-500 text-white flex items-center justify-center font-bold text-lg">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="mr-3 flex-1">
                    <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                    <div class="flex flex-wrap gap-1 mt-1">
                    @foreach(auth()->user()->roles as $role)
                        @if ($role->name == 'super_admin')
                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-medium rounded-full bg-blue-100 text-blue-800">مدير النظام</span>
                        @elseif ($role->name == 'sales_manager')
                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-medium rounded-full bg-green-100 text-green-800">مدير المبيعات</span>
                        @elseif ($role->name == 'follow_up')
                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-medium rounded-full bg-yellow-100 text-yellow-800">متابعة</span>
                        @elseif ($role->name == 'sales')
                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-medium rounded-full bg-purple-100 text-purple-800">مندوب مبيعات</span>
                        @endif
                    @endforeach
                    </div>
                </div>
                <div>
                    <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-red-500 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                    </form>
                </div>
                </div>
                @endif
            </div>
            </div>
        </div>

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

    {{-- inter scripts here --}}
    @stack('scripts')

</body>
</html>