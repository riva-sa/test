<div class="hidden md:flex md:flex-shrink-0">
    <div class="flex flex-col w-72 bg-white border-l border-gray-200 shadow-lg">
<!-- Sidebar header -->
        <div class="flex items-center justify-between h-20 px-6 border-b border-gray-200 bg-gradient-to-l from-gray-50 to-white">
            <div class="flex items-center">
                <img src="{{ asset('frontend/img/logoyy.png') }}" width="40px" alt="ريفا" class="ml-3">
                <div class="flex flex-col">
                    <span class="text-xl font-bold text-gray-800">ريفا العقارية</span>
                    <span class="text-xs text-gray-500">لوحة التحكم</span>
                </div>
            </div>
            
            <!-- Notifications Bell -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-primary-500 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @if($unreadCount > 0)
                        <span class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center animate-pulse">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    @endif
                </button>

                <!-- Notifications Dropdown -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                    
                    <div class="p-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                الإشعارات
                            </h3>
                            @if($unreadCount > 0)
                                <button wire:click="markAllAsRead" wire:loading.attr="disabled" class="text-sm text-primary-600 font-medium px-3 py-1 rounded-md transition-colors">
                                    تحديد الكل كمقروء
                                </button>
                            @endif
                        </div>
                        @if($unreadCount > 0)
                            <p class="text-xs text-gray-500 mt-1">{{ $unreadCount }} إشعار غير مقروء</p>
                        @endif
                    </div>

                    <div class="max-h-96 overflow-y-auto">
                        @forelse($notifications as $notification)
                            @php
                                // استخلاص البيانات لسهولة القراءة
                                $data = $notification->data;
                                $type = $data['type'] ?? 'default';
                                $isUnread = is_null($notification->read_at);
                            @endphp
                            
                            <div wire:click.prevent="handleNotificationClick('{{ $notification->id }}')"
                                class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors duration-150 {{ $isUnread ? 'bg-blue-50' : '' }}">
                                
                                <div class="flex items-start space-x-3 space-x-reverse">
                                    <!-- 1. أيقونة الإشعار (الجزء المطور) -->
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium {{ $this->getNotificationColor($type) }}">
                                            @switch($type)
                                                @case('new_order')
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                                    @break
                                                @case('new_note')
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" /></svg>
                                                    @break
                                                @case('status_update')
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                                    @break
                                                @case('client_update')
                                                @case('unit_info_update')
                                                @case('message_update')
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                    @break
                                                @case('permission_granted')
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                                    @break
                                                @case('permission_revoked')
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" /></svg>
                                                    @break
                                                @default
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            @endswitch
                                        </div>
                                    </div>
                                    
                                    <!-- 2. محتوى الإشعار (مدمج من التصميمين) -->
                                    <div class="flex-1 min-w-0">
                                        <!-- السطر الأول: عنوان الطلب وعلامة "جديد" -->
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-semibold text-gray-900">
                                                طلب #{{ $data['order_id'] }}
                                            </p>
                                            @if($isUnread)
                                                <span class="h-2 w-2 bg-primary-500 rounded-full"></span>
                                            @endif
                                        </div>
                                        
                                        <!-- الرسالة الرئيسية -->
                                        <p class="text-sm text-gray-700 mt-1 leading-relaxed">
                                            {!! $data['message'] !!}
                                        </p>
                                        
                                        <!-- [جديد] - عرض تفاصيل إضافية بناءً على نوع الإشعار -->
                                        @if($type === 'status_update' && isset($data['data']['project_name']))
                                            <div class="mt-2 p-2 bg-gray-100 rounded-md text-xs">
                                                <span class="font-semibold">المشروع:</span>
                                                <span class="text-gray-700">{{ $data['data']['project_name'] }}</span>
                                            </div>
                                        @endif
                                        
                                        <!-- [مُعاد] - معلومات "بواسطة" -->
                                        @if(isset($data['updated_by']))
                                            <p class="text-xs text-gray-500 mt-1">
                                                بواسطة: {{ $data['updated_by'] }}
                                            </p>
                                        @endif
                                        
                                        <!-- السطر الأخير: الوقت ونوع الإشعار -->
                                        <div class="flex items-center justify-between mt-2">
                                            <p class="text-xs text-gray-400">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                            
                                            <!-- [مُعاد] - نوع الإشعار -->
                                            @switch($type)
                                                @case('new_order')
                                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">طلب جديد</span>
                                                    @break
                                                @case('status_update')
                                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">تحديث حالة</span>
                                                    @break
                                                @case('new_note')
                                                @case('message_update')
                                                    <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">تحديث رسالة</span>
                                                    @break
                                                @case('client_update')
                                                @case('unit_info_update')
                                                    <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">تحديث بيانات</span>
                                                    @break
                                                @case('permission_granted')
                                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">صلاحية ممنوحة</span>
                                                    @break
                                                @case('permission_revoked')
                                                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">صلاحية ملغاة</span>
                                                    @break
                                            @endswitch
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <p class="text-gray-500 font-medium">لا توجد إشعارات</p>
                                <p class="text-gray-400 text-sm mt-1">ستظهر الإشعارات هنا عند حدوث تحديثات</p>
                            </div>
                        @endforelse
                    </div>


                    @if($notifications->count() >= 10)
                        <div class="p-4 border-t border-gray-200 text-center bg-gray-50 rounded-b-lg">
                            <a href="" class="text-sm text-primary-600 hover:text-primary-800 font-medium inline-flex items-center">
                                عرض جميع الإشعارات
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </a>
                        </div>
                    @endif
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

                <!-- Analytics link with custom SVG icon -->
                <a href="{{ route('manager.analytics') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-50 transition-all duration-200 text-gray-700 hover:text-primary-500 sidebar-item {{ request()->routeIs('manager.analytics') ? 'bg-gray-50 text-primary-500 ' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-3 text-gray-500 group-hover:text-primary-500 {{ request()->routeIs('manager.analytics') ? 'text-primary-500' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    الاحصائيات
                </a>

                <!-- Analytics link with custom SVG icon -->
                <a href="{{ route('manager.analytics.campaigns') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-gray-50 transition-all duration-200 text-gray-700 hover:text-primary-500 sidebar-item {{ request()->routeIs('manager.analytics') ? 'bg-gray-50 text-primary-500 ' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-3 text-gray-500 group-hover:text-primary-500 {{ request()->routeIs('manager.analytics') ? 'text-primary-500' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    الحملات
                </a>
                @endif

                <!-- Quick Actions Section -->
                <div class="px-4 py-2 mt-6">
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