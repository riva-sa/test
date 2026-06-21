<div class="hidden md:flex md:flex-shrink-0 h-full" wire:poll.30s="autoRefreshNotifications">
    @php
        $leadsActive = request()->routeIs(['manager.customerlist', 'manager.orders', 'manager.create-order', 'manager.order-details']);
        $managementActive = request()->routeIs(['manager.sales-managers', 'manager.reports.auto-assignment', 'manager.analytics', 'manager.analytics.campaigns', 'manager.bulk-lead-import', 'manager.blocked-numbers', 'manager.activities']);
        $brokersActive = request()->routeIs(['manager.broker-applications', 'manager.brokers', 'manager.broker-statement', 'manager.broker-commissions', 'manager.commission-payments', 'manager.broker-contract-template', 'manager.project-commissions']);
        $toolsActive = request()->routeIs(['manager.announcements', 'manager.notifications', 'manager.targets', 'manager.leaderboard']);
    @endphp
    <div class="flex flex-col w-64 h-full bg-white transition-all duration-300 relative z-20 ">
        
        <!-- Sidebar Header (Logo & Brand) -->
        <div class="p-2">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('frontend/img/logoyy.png') }}" class="h-10 w-auto" alt="Logo">
                    <div class="flex flex-col min-w-0">
                        <span class="text-[15px] font-black text-gray-900 tracking-tight leading-none">ريفا العقارية</span>
                        <span class="text-[10px] font-bold text-primary-600 mt-1 uppercase tracking-widest">لوحة التحكم</span>
                    </div>
                </div>

                <!-- Unified Notifications Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-900 hover:bg-gray-50 rounded-xl transition-all relative active:scale-95 group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:rotate-12 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @php $totalUnread = $unreadCount + $crmUnreadCount; @endphp
                        @if($totalUnread > 0)
                            <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[10px] font-bold rounded-full border-2 border-white flex items-center justify-center leading-none">
                                {{ $totalUnread > 99 ? '99+' : $totalUnread }}
                            </span>
                        @endif
                    </button>
                    
                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-gray-100 z-50 overflow-hidden" style="display: none;">
                        <div class="p-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                            <h3 class="text-[13px] font-bold text-gray-900">مركز التنبيهات</h3>
                            <button wire:click="markAllAsRead" class="text-[10px] text-primary-600 font-bold hover:underline">تحديد الكل كمقروء</button>
                        </div>
                        <div class="max-h-[450px] overflow-y-auto scrollbar-thin">
                            <!-- Section: Orders -->
                            @if($notifications->count() > 0)
                                <div class="px-4 py-2 bg-blue-50/50 text-[10px] font-bold text-blue-700 uppercase tracking-widest flex items-center">
                                    <span class="w-1 h-1 bg-blue-700 rounded-full ml-2"></span>
                                    تحديثات الطلبات
                                </div>
                                @foreach($notifications as $notification)
                                    <div class="p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-50 last:border-0 transition-colors {{ is_null($notification->read_at) ? 'bg-blue-50/20' : '' }}" 
                                         wire:click="handleNotificationClick('{{ $notification->id }}')">
                                        <p class="text-[12px] text-gray-700 leading-relaxed font-medium">{!! $notification->data['message'] !!}</p>
                                        <span class="text-[10px] text-gray-400 mt-2 block">{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                @endforeach
                            @endif

                            <!-- Section: Ads & Announcements -->
                            @php $announcements = $crmNotifications->filter(fn($n) => $n->notification->type === 'announcement'); @endphp
                            @if($announcements->count() > 0)
                                <div class="px-4 py-2 bg-purple-50/50 text-[10px] font-bold text-purple-700 uppercase tracking-widest flex items-center mt-1">
                                    <span class="w-1 h-1 bg-purple-700 rounded-full ml-2"></span>
                                    إعلانات ومنشورات
                                </div>
                                @foreach($announcements as $ann)
                                    <div class="p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-50 last:border-0 transition-colors {{ !$ann->read_at ? 'bg-purple-50/20' : '' }}" 
                                         wire:click="markCrmAsRead({{ $ann->id }})">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-[12px] font-bold text-gray-900">{{ $ann->notification->title }}</span>
                                            @if(!$ann->read_at) <span class="h-2 w-2 bg-purple-600 rounded-full"></span> @endif
                                        </div>
                                        <div class="text-[11px] text-gray-600 leading-normal line-clamp-2">{!! $ann->notification->content !!}</div>
                                        <span class="text-[10px] text-gray-400 mt-2 block">{{ $ann->created_at->diffForHumans() }}</span>
                                    </div>
                                @endforeach
                            @endif

                            <!-- Section: Admin Alerts -->
                            @php $alerts = $crmNotifications->filter(fn($n) => $n->notification->type !== 'announcement'); @endphp
                            @if($alerts->count() > 0)
                                <div class="px-4 py-2 bg-amber-50/50 text-[10px] font-bold text-amber-700 uppercase tracking-widest flex items-center mt-1">
                                    <span class="w-1 h-1 bg-amber-700 rounded-full ml-2"></span>
                                    تنبيهات النظام
                                </div>
                                @foreach($alerts as $alert)
                                    <div class="p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-50 last:border-0 transition-colors {{ !$alert->read_at ? 'bg-amber-50/20' : '' }}" 
                                         wire:click="markCrmAsRead({{ $alert->id }})">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-[12px] font-bold text-gray-900">{{ $alert->notification->title }}</span>
                                            @if(!$alert->read_at) <span class="h-2 w-2 bg-amber-600 rounded-full"></span> @endif
                                        </div>
                                        <p class="text-[11px] text-gray-600 leading-normal">{{ $alert->notification->message ?? strip_tags($alert->notification->content) }}</p>
                                        <span class="text-[10px] text-gray-400 mt-2 block">{{ $alert->created_at->diffForHumans() }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Navigation -->
        <div class="flex-1 overflow-y-auto scrollbar-thin px-2 space-y-1 pb-4">
            
            <!-- Group: Dashboard -->
            <a href="{{ route('manager.dashboard') }}" 
               class="flex items-center px-4 py-2.5 text-[14px] font-bold rounded-xl transition-all {{ request()->routeIs('manager.dashboard') ? 'bg-gray-900 text-white shadow-xl shadow-gray-200' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-3 {{ request()->routeIs('manager.dashboard') ? 'text-white' : 'opacity-70' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>الرئيسية</span>
            </a>

            <!-- Group: Operations -->
            <div class="space-y-1">
                <div class="w-full flex items-center justify-between px-4 py-2.5 text-[14px] font-bold rounded-xl transition-all text-gray-900 bg-gray-50/50">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-3 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>العمليات والطلبات</span>
                    </div>
                </div>
                <div class="mr-5 border-r border-gray-100 pr-4 space-y-1 py-1">
                    <a href="{{ route('manager.customerlist') }}" class="relative block px-3 py-2 text-[13px] font-medium rounded-lg transition-all {{ request()->routeIs('manager.customerlist') ? 'text-gray-900 bg-gray-50 font-bold' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50/50' }}">
                        @if(request()->routeIs('manager.customerlist')) <span class="absolute -right-[17px] top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-l-full"></span> @endif
                        العملاء
                    </a>
                    <a href="{{ route('manager.orders') }}" class="relative block px-3 py-2 text-[13px] font-medium rounded-lg transition-all {{ request()->routeIs(['manager.orders', 'manager.order-details']) ? 'text-gray-900 bg-gray-50 font-bold' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50/50' }}">
                        @if(request()->routeIs(['manager.orders', 'manager.order-details'])) <span class="absolute -right-[17px] top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-l-full"></span> @endif
                        <div class="flex items-center justify-between">
                            <span>الطلبات</span>
                            @if($newOrdersCount > 0)
                                <span class="flex items-center justify-center min-w-[18px] h-[18px] px-1.5 text-[10px] font-bold text-white bg-red-500 rounded-full">
                                    {{ $newOrdersCount }}
                                </span>
                            @endif
                        </div>
                    </a>
                    <a href="{{ route('manager.create-order') }}" class="relative block px-3 py-2 text-[13px] font-bold text-primary-800 rounded-lg transition-all">
                        @if(request()->routeIs('manager.create-order')) <span class="absolute -right-[17px] top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-l-full"></span> @endif
                        إضافة طلب جديد
                    </a>
                </div>
            </div>

            <!-- Group: Management -->
            @if (auth()->user()->hasRole(['sales_manager', 'follow_up', 'Admin']))
            <div x-data="{ open: @json($managementActive) }" class="space-y-1">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 text-[14px] font-bold rounded-xl transition-all {{ $managementActive ? 'text-gray-900 bg-gray-50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-3 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>الإدارة والتقارير</span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mr-5 border-r border-gray-100 pr-4 space-y-1 py-1">
                    <a href="{{ route('manager.sales-managers') }}" class="relative block px-3 py-2 text-[13px] font-medium rounded-lg transition-all {{ request()->routeIs('manager.sales-managers') ? 'text-gray-900 bg-gray-50 font-bold' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50/50' }}">
                        @if(request()->routeIs('manager.sales-managers')) <span class="absolute -right-[17px] top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-l-full"></span> @endif
                        فريق المبيعات
                    </a>
                    <a href="{{ route('manager.reports.auto-assignment') }}" class="relative block px-3 py-2 text-[13px] font-medium rounded-lg transition-all {{ request()->routeIs('manager.reports.auto-assignment') ? 'text-gray-900 bg-gray-50 font-bold' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50/50' }}">
                        @if(request()->routeIs('manager.reports.auto-assignment')) <span class="absolute -right-[17px] top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-l-full"></span> @endif
                        التوزيع التلقائي
                    </a>
                    <a href="{{ route('manager.analytics') }}" class="relative block px-3 py-2 text-[13px] font-medium rounded-lg transition-all {{ request()->routeIs('manager.analytics') ? 'text-gray-900 bg-gray-50 font-bold' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50/50' }}">
                        @if(request()->routeIs('manager.analytics')) <span class="absolute -right-[17px] top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-l-full"></span> @endif
                        الاحصائيات
                    </a>
                    <a href="{{ route('manager.analytics.campaigns') }}" class="relative block px-3 py-2 text-[13px] font-medium rounded-lg transition-all {{ request()->routeIs('manager.analytics.campaigns') ? 'text-gray-900 bg-gray-50 font-bold' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50/50' }}">
                        @if(request()->routeIs('manager.analytics.campaigns')) <span class="absolute -right-[17px] top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-l-full"></span> @endif
                        الحملات
                    </a>
                    <a href="{{ route('manager.bulk-lead-import') }}" class="relative block px-3 py-2 text-[13px] font-medium rounded-lg transition-all {{ request()->routeIs('manager.bulk-lead-import') ? 'text-gray-900 bg-gray-50 font-bold' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50/50' }}">
                        @if(request()->routeIs('manager.bulk-lead-import')) <span class="absolute -right-[17px] top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-l-full"></span> @endif
                        استيراد البيانات
                    </a>
                    @if(auth()->user()->hasRole('Admin'))
                        <a href="{{ route('manager.blocked-numbers') }}" class="relative block px-3 py-2 text-[13px] font-medium rounded-lg transition-all {{ request()->routeIs('manager.blocked-numbers') ? 'text-gray-900 bg-gray-50 font-bold' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50/50' }}">
                            @if(request()->routeIs('manager.blocked-numbers')) <span class="absolute -right-[17px] top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-l-full"></span> @endif
                            الأرقام المحظورة
                        </a>
                    @endif
                    @if(auth()->user()->hasRole(['Admin', 'sales_manager']))
                        <a href="{{ route('manager.activities') }}" class="relative block px-3 py-2 text-[13px] font-medium rounded-lg transition-all {{ request()->routeIs('manager.activities') ? 'text-gray-900 bg-gray-50 font-bold' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50/50' }}">
                            @if(request()->routeIs('manager.activities')) <span class="absolute -right-[17px] top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-l-full"></span> @endif
                            سجل العمليات
                        </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- Group: Brokers -->
            @if(auth()->user()->hasRole('Admin'))
            <div x-data="{ open: @json($brokersActive) }" class="space-y-1">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 text-[14px] font-bold rounded-xl transition-all {{ $brokersActive ? 'text-gray-900 bg-gray-50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-3 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>الوسطاء</span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mr-5 border-r border-gray-100 pr-4 space-y-1 py-1">
                    <a href="{{ route('manager.broker-applications') }}" class="relative block px-3 py-2 text-[13px] font-medium rounded-lg transition-all {{ request()->routeIs('manager.broker-applications') ? 'text-gray-900 bg-gray-50 font-bold' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50/50' }}">
                        @if(request()->routeIs('manager.broker-applications')) <span class="absolute -right-[17px] top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-l-full"></span> @endif
                        طلبات الوسطاء
                    </a>
                    <a href="{{ route('manager.brokers') }}" class="relative block px-3 py-2 text-[13px] font-medium rounded-lg transition-all {{ request()->routeIs('manager.brokers', 'manager.broker-statement') ? 'text-gray-900 bg-gray-50 font-bold' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50/50' }}">
                        @if(request()->routeIs('manager.brokers', 'manager.broker-statement')) <span class="absolute -right-[17px] top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-l-full"></span> @endif
                        الوسطاء
                    </a>
                    <a href="{{ route('manager.broker-commissions') }}" class="relative block px-3 py-2 text-[13px] font-medium rounded-lg transition-all {{ request()->routeIs('manager.broker-commissions') ? 'text-gray-900 bg-gray-50 font-bold' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50/50' }}">
                        @if(request()->routeIs('manager.broker-commissions')) <span class="absolute -right-[17px] top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-l-full"></span> @endif
                        مستحقات العمولات
                    </a>
                    <a href="{{ route('manager.commission-payments') }}" class="relative block px-3 py-2 text-[13px] font-medium rounded-lg transition-all {{ request()->routeIs('manager.commission-payments') ? 'text-gray-900 bg-gray-50 font-bold' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50/50' }}">
                        @if(request()->routeIs('manager.commission-payments')) <span class="absolute -right-[17px] top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-l-full"></span> @endif
                        سجلّ الحركات المالية
                    </a>
                    <a href="{{ route('manager.project-commissions') }}" class="relative block px-3 py-2 text-[13px] font-medium rounded-lg transition-all {{ request()->routeIs('manager.project-commissions') ? 'text-gray-900 bg-gray-50 font-bold' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50/50' }}">
                        @if(request()->routeIs('manager.project-commissions')) <span class="absolute -right-[17px] top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-l-full"></span> @endif
                        عمولات المشاريع
                    </a>
                    <a href="{{ route('manager.broker-contract-template') }}" class="relative block px-3 py-2 text-[13px] font-medium rounded-lg transition-all {{ request()->routeIs('manager.broker-contract-template') ? 'text-gray-900 bg-gray-50 font-bold' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50/50' }}">
                        @if(request()->routeIs('manager.broker-contract-template')) <span class="absolute -right-[17px] top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-l-full"></span> @endif
                        نسخة العقد
                    </a>
                </div>
            </div>
            @endif

            <!-- Group: Tools -->
            <div x-data="{ open: @json($toolsActive) }" class="space-y-1">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 text-[14px] font-bold rounded-xl transition-all {{ $toolsActive ? 'text-gray-900 bg-gray-50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-3 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        </svg>
                        <span>الأدوات والخدمات</span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-collapse class="mr-5 border-r border-gray-100 pr-4 space-y-1 py-1">
                    <a href="{{ route('manager.announcements') }}" class="relative flex items-center justify-between px-3 py-2 text-[13px] font-medium rounded-lg transition-all {{ request()->routeIs('manager.announcements') ? 'text-gray-900 bg-gray-50 font-bold' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50/50' }}">
                        @if(request()->routeIs('manager.announcements')) <span class="absolute -right-[17px] top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-l-full"></span> @endif
                        <span>الإعلانات العامة</span>
                        @if($crmUnreadCount > 0)
                            <span class="mr-auto mr-1 min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none">
                                {{ $crmUnreadCount > 99 ? '99+' : $crmUnreadCount }}
                            </span>
                        @endif
                    </a>
                    @if(auth()->user()->hasRole(['sales_manager', 'Admin']))
                        <a href="{{ route('manager.notifications') }}" class="relative block px-3 py-2 text-[13px] font-medium rounded-lg transition-all {{ request()->routeIs('manager.notifications') ? 'text-gray-900 bg-gray-50 font-bold' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50/50' }}">
                            @if(request()->routeIs('manager.notifications')) <span class="absolute -right-[17px] top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-l-full"></span> @endif
                            إرسال إشعار
                        </a>
                        <a href="{{ route('manager.targets') }}" class="relative block px-3 py-2 text-[13px] font-medium rounded-lg transition-all {{ request()->routeIs('manager.targets') ? 'text-gray-900 bg-gray-50 font-bold' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50/50' }}">
                            @if(request()->routeIs('manager.targets')) <span class="absolute -right-[17px] top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-l-full"></span> @endif
                            أهداف المبيعات
                        </a>
                        <a href="{{ route('manager.leaderboard') }}" class="relative block px-3 py-2 text-[13px] font-medium rounded-lg transition-all {{ request()->routeIs('manager.leaderboard') ? 'text-gray-900 bg-gray-50 font-bold' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50/50' }}">
                            @if(request()->routeIs('manager.leaderboard')) <span class="absolute -right-[17px] top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-600 rounded-l-full"></span> @endif
                            لوحة المتصدرين
                        </a>
                    @endif
                </div>
            </div>

        </div>

        <!-- Sidebar Footer (User Profile & Logout) -->
        <div class="mt-auto">
            <div class="flex items-center gap-3 mb-4 px-2">
                <div class="h-10 w-10 rounded-xl bg-gray-900 text-white flex items-center justify-center font-bold text-base shadow-lg shadow-gray-900/10">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-[13px] font-bold text-gray-900 truncate leading-tight">{{ auth()->user()->name }}</span>
                    <span class="text-[10px] font-medium text-gray-400 truncate mt-0.5">{{ auth()->user()->email }}</span>
                    <div class="flex flex-wrap gap-1 mt-1">
                        @foreach(auth()->user()->getRoleNames() as $role)
                            <span class="px-1.5 py-0.5 rounded-md bg-gray-50 text-[9px] font-bold text-gray-500 uppercase tracking-wider border border-gray-100">
                                {{ match($role) {
                                    'Admin' => 'مدير النظام',
                                    'sales_manager' => 'مدير مبيعات',
                                    'sales' => 'موظف مبيعات',
                                    'follow_up' => 'متابعة',
                                    'developer' => 'مطور',
                                    default => $role
                                } }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-between px-3 py-2.5 text-xs font-bold text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all group active:scale-95">
                    <span>تسجيل الخروج</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-30 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

</div>
