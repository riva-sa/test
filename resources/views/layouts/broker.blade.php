<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ريفا') }} - بوابة الوسطاء</title>

    <link rel="shortcut icon" href="{{ asset('frontend/img/logoyy.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/broker.css'])

    @livewireStyles
</head>
<body class="antialiased text-gray-900">
    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 h-full bg-white border-l border-gray-100">
                <div class="p-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('frontend/img/logoyy.png') }}" class="h-10 w-auto" alt="Logo">
                        <div class="flex flex-col min-w-0">
                            <span class="text-[15px] font-black text-gray-900 tracking-tight leading-none">ريفا العقارية</span>
                            <span class="text-[10px] font-bold text-primary-600 mt-1 uppercase tracking-widest">بوابة الوسطاء</span>
                        </div>
                    </div>
                </div>

                <nav class="flex-1 overflow-y-auto scrollbar-thin px-2 space-y-1 pb-4">
                    <a href="{{ route('broker.dashboard') }}"
                       class="flex items-center px-4 py-2.5 text-[14px] font-bold rounded-xl transition-all {{ request()->routeIs('broker.dashboard') ? 'bg-gray-900 text-white shadow-xl shadow-gray-200' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                        <i class="fas fa-home ml-3 text-[13px] opacity-70"></i>
                        <span>الرئيسية</span>
                    </a>
                    <a href="{{ route('broker.projects') }}"
                       class="flex items-center px-4 py-2.5 text-[14px] font-bold rounded-xl transition-all {{ request()->routeIs(['broker.projects', 'broker.projects.show']) ? 'bg-gray-900 text-white shadow-xl shadow-gray-200' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                        <i class="fas fa-building ml-3 text-[13px] opacity-70"></i>
                        <span>المشاريع والوحدات</span>
                    </a>
                    <a href="{{ route('broker.leads.create') }}"
                       class="flex items-center px-4 py-2.5 text-[14px] font-bold rounded-xl transition-all {{ request()->routeIs('broker.leads.create') ? 'bg-gray-900 text-white shadow-xl shadow-gray-200' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                        <i class="fas fa-user-plus ml-3 text-[13px] opacity-70"></i>
                        <span>إرسال عميل جديد</span>
                    </a>
                    <a href="{{ route('broker.leads') }}"
                       class="flex items-center px-4 py-2.5 text-[14px] font-bold rounded-xl transition-all {{ request()->routeIs(['broker.leads', 'broker.leads.show']) ? 'bg-gray-900 text-white shadow-xl shadow-gray-200' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                        <i class="fas fa-list-check ml-3 text-[13px] opacity-70"></i>
                        <span>طلباتي</span>
                    </a>
                    <a href="{{ route('broker.profile') }}"
                       class="flex items-center px-4 py-2.5 text-[14px] font-bold rounded-xl transition-all {{ request()->routeIs('broker.profile') ? 'bg-gray-900 text-white shadow-xl shadow-gray-200' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                        <i class="fas fa-id-card ml-3 text-[13px] opacity-70"></i>
                        <span>الملف الشخصي</span>
                    </a>
                </nav>

                <div class="mt-auto p-3 border-t border-gray-50">
                    <div class="flex items-center gap-3 mb-3 px-1">
                        <div class="h-10 w-10 rounded-xl bg-gray-900 text-white flex items-center justify-center font-bold text-base">
                            {{ mb_substr(auth('broker')->user()->name ?? 'و', 0, 1) }}
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="text-[13px] font-bold text-gray-900 truncate leading-tight">{{ auth('broker')->user()->name }}</span>
                            <span class="text-[10px] font-medium text-gray-400 truncate mt-0.5">{{ auth('broker')->user()->reference_number }}</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('broker.logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-between px-3 py-2.5 text-xs font-bold text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                            <span>تسجيل الخروج</span>
                            <i class="fas fa-arrow-left-long opacity-50"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Mobile topbar -->
            <div class="md:hidden bg-white border-b border-gray-100 p-3" x-data="{ open: false }">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('frontend/img/logoyy.png') }}" class="h-8 w-auto" alt="Logo">
                        <span class="text-sm font-black">بوابة الوسطاء</span>
                    </div>
                    <button @click="open = !open" class="p-2 text-gray-500"><i class="fas fa-bars"></i></button>
                </div>
                <div x-show="open" x-cloak class="mt-3 space-y-1">
                    <a href="{{ route('broker.dashboard') }}" class="block px-3 py-2 text-sm font-bold rounded-lg {{ request()->routeIs('broker.dashboard') ? 'bg-gray-900 text-white' : 'text-gray-600' }}">الرئيسية</a>
                    <a href="{{ route('broker.projects') }}" class="block px-3 py-2 text-sm font-bold rounded-lg {{ request()->routeIs(['broker.projects', 'broker.projects.show']) ? 'bg-gray-900 text-white' : 'text-gray-600' }}">المشاريع والوحدات</a>
                    <a href="{{ route('broker.leads.create') }}" class="block px-3 py-2 text-sm font-bold rounded-lg {{ request()->routeIs('broker.leads.create') ? 'bg-gray-900 text-white' : 'text-gray-600' }}">إرسال عميل جديد</a>
                    <a href="{{ route('broker.leads') }}" class="block px-3 py-2 text-sm font-bold rounded-lg {{ request()->routeIs(['broker.leads', 'broker.leads.show']) ? 'bg-gray-900 text-white' : 'text-gray-600' }}">طلباتي</a>
                    <a href="{{ route('broker.profile') }}" class="block px-3 py-2 text-sm font-bold rounded-lg {{ request()->routeIs('broker.profile') ? 'bg-gray-900 text-white' : 'text-gray-600' }}">الملف الشخصي</a>
                    <form method="POST" action="{{ route('broker.logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-right px-3 py-2 text-sm font-bold text-red-600">تسجيل الخروج</button>
                    </form>
                </div>
            </div>

            <main class="flex-1 overflow-y-auto p-4 md:p-8">
                @if (session('message'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 text-sm font-bold rounded-xl">
                        {{ session('message') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 text-sm font-bold rounded-xl">
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
