<div class="min-h-full bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-2 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                    </svg>
                    لوحة التحكم
                </h1>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <!-- Customers Card -->
            <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100 hover:shadow-md transition-shadow duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-xl p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="mr-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">العملاء</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ number_format($customersCount ) }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- All Orders Card -->
            <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100 hover:shadow-md transition-shadow duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 rounded-xl p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <div class="mr-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">جميع الطلبات</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ number_format($allOrders ) }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Orders Card -->
            <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100 hover:shadow-md transition-shadow duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-100 rounded-xl p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div class="mr-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">طلبات جديدة</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ number_format($newOrders ) }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Open Orders Card -->
            <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100 hover:shadow-md transition-shadow duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-xl p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="mr-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">طلبات مفتوحة</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ number_format($openOrders ) }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">

            <!-- Delayed Orders Card -->
            <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100 hover:shadow-md transition-shadow duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-100 rounded-xl p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mr-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">طلبات متأخرة</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ number_format($delayedOrders ) }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Transactions Card -->
            <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100 hover:shadow-md transition-shadow duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-xl p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10l9-7 9 7v10a2 2 0 01-2 2h-4a2 2 0 01-2-2V13H9v7a2 2 0 01-2 2H3a2 2 0 01-2-2V10z" />
                            </svg>
                        </div>
                        <div class="mr-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">عمليات بيعية</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ number_format($SalesTransactions ) }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Closed Orders Card -->
            <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100 hover:shadow-md transition-shadow duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-gray-100 rounded-xl p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mr-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">طلبات مغلقة</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ number_format($closedOrders ) }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Orders Card -->
            <div class="bg-white overflow-hidden shadow rounded-xl border border-gray-100 hover:shadow-md transition-shadow duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-teal-100 rounded-xl p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="mr-4 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">طلبات مكتملة</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">
                                {{ number_format($completedOrders ) }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent activity and charts -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-6">
            <!-- Recent orders -->
            <div class="bg-white shadow rounded-xl border border-gray-100 lg:col-span-2">
                <div class="px-5 py-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            الطلبات الحديثة
                        </h3>
                        <a href="{{ route('manager.orders' ) }}" class="text-sm font-medium text-primary-600 hover:text-primary-800">
                            عرض الكل
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 responsive-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    العميل
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    المشروع
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    اخر تحديث
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الحالة
                                </th>
                                @if (auth()->user()->hasRole('sales_manager') || auth()->user()->hasRole('follow_up'))
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    مندوب المبيعات
                                </th>
                                @endif
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span class="">التاريخ</span>
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">تفاصيل</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($recentOrders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-medium">
                                            {{ substr($order->name, 0, 1) }}
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $order->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $order->phone }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->project->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{-- <div class="bg-white shadow rounded-2xl p-4 mt-6">
                                        <h3 class="text-lg font-bold mb-4">📜 سجل الأنشطة</h3>

                                        <div class="overflow-x-auto">
                                            <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-4 py-2 text-right text-sm font-semibold text-gray-600">النوع</th>
                                                        <th class="px-4 py-2 text-right text-sm font-semibold text-gray-600">التفاصيل</th>
                                                        <th class="px-4 py-2 text-right text-sm font-semibold text-gray-600">التاريخ</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100">
                                                    @forelse($order->activities() as $activity)
                                                        <tr class="hover:bg-gray-50">
                                                            <td class="px-4 py-2 whitespace-nowrap">
                                                                @if($activity['type'] === 'note')
                                                                    <span class="text-blue-600">📝 ملاحظة</span>
                                                                @elseif($activity['type'] === 'permission')
                                                                    <span class="text-green-600">🔑 صلاحية</span>
                                                                @elseif($activity['type'] === 'status')
                                                                    <span class="text-purple-600">📌 حالة</span>
                                                                @else
                                                                    <span class="text-gray-600">ℹ️ آخر</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-4 py-2 text-gray-800 text-sm">
                                                                {{ $activity['message'] }}
                                                            </td>
                                                            <td class="px-4 py-2 text-gray-500 text-sm whitespace-nowrap">
                                                                {{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="px-4 py-4 text-center text-gray-500">
                                                                لا توجد أنشطة مسجلة بعد.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div> --}}
                                    @if($last = $order->lastActivity())
                                    <div class="flex items-start gap-3 max-w-sm">
                                        {{-- أيقونة النشاط --}}
                                        <div class="flex-shrink-0 relative">
                                            <span class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium
                                                @if($last['type'] === 'note') bg-blue-50 text-blue-600 border border-blue-200
                                                @elseif($last['type'] === 'permission') bg-green-50 text-green-600 border border-green-200
                                                @elseif($last['type'] === 'status') bg-amber-50 text-amber-600 border border-amber-200
                                                @else bg-gray-50 text-gray-600 border border-gray-200 @endif
                                            ">
                                                @if($last['type'] === 'note') 
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                                    </svg>
                                                @elseif($last['type'] === 'permission')
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                @elseif($last['type'] === 'status')
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                    </svg>
                                                @endif
                                            </span>
                                            
                                            {{-- نقطة حالة صغيرة --}}
                                            <span class="absolute -top-1 -right-1 w-3 h-3 rounded-full
                                                @if($last['type'] === 'note') bg-blue-400
                                                @elseif($last['type'] === 'permission') bg-green-400
                                                @elseif($last['type'] === 'status') bg-amber-400
                                                @else bg-gray-400 @endif
                                            "></span>
                                        </div>
                                        
                                        {{-- محتوى النشاط --}}
                                        <div class="min-w-0 flex-1">
                                            {{-- نوع النشاط --}}
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                    @if($last['type'] === 'note') bg-blue-100 text-blue-700
                                                    @elseif($last['type'] === 'permission') bg-green-100 text-green-700
                                                    @elseif($last['type'] === 'status') bg-amber-100 text-amber-700
                                                    @else bg-gray-100 text-gray-700 @endif
                                                ">
                                                    @if($last['type'] === 'note') ملاحظة
                                                    @elseif($last['type'] === 'permission') صلاحية
                                                    @elseif($last['type'] === 'status') حالة
                                                    @else نشاط @endif
                                                </span>
                                            </div>
                                            
                                            {{-- رسالة النشاط --}}
                                            <p class="text-sm text-gray-800 leading-relaxed font-medium mb-2" 
                                            title="{{ $last['message'] }}"
                                            x-data="{ expanded: false }">
                                                <span x-show="!expanded">{{ Str::limit($last['message'], 50) }}</span>
                                                <span x-show="expanded" x-text="'{{ addslashes($last['message']) }}'"></span>
                                                {{-- @if(strlen($last['message']) > 50)
                                                    <button @click="expanded = !expanded" 
                                                            class="text-blue-600 hover:text-blue-800 text-xs mr-1 focus:outline-none">
                                                        <span x-show="!expanded">المزيد</span>
                                                        <span x-show="expanded">أقل</span>
                                                    </button>
                                                @endif --}}
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    {{-- في حالة عدم وجود نشاط --}}
                                    <div class="flex items-center justify-start h-16">
                                        <div class="text-center">
                                            <svg class="w-6 h-6 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-xs text-gray-400 font-medium">لا يوجد نشاط</span>
                                        </div>
                                    </div>
                                @endif
                                </td>
                         
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $statusConfig[$order->status]['color'] ?? 'gray' }}-100 text-{{ $statusConfig[$order->status]['color'] ?? 'gray' }}-800">
                                        {{ $statusConfig[$order->status]['label'] ?? 'غير معروف' }}
                                    </span>
                                </td>
                                @if (auth()->user()->hasRole('sales_manager') || auth()->user()->hasRole('follow_up'))
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->project->salesManager->name ?? '-' }}
                                </td>
                                @endif

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('manager.order-details', $order->id) }}" class="text-primary-600 hover:text-primary-900 flex items-center">
                                        التفاصيل
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    لا توجد طلبات حديثة
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Order Status Chart -->
            <div class="space-y-6">
                <!-- Pie Chart -->
                <div class="bg-white shadow rounded-xl border border-gray-100">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            حالة الطلبات
                        </h3>
                    </div>
                    <div class="px-5 py-5">
                        <canvas id="orderStatusChart" class="w-full h-64"></canvas>
                    </div>
                </div>

                <!-- Status Bars -->
                <div class="bg-white shadow rounded-xl border border-gray-100">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            توزيع الطلبات
                        </h3>
                    </div>
                    <div class="px-5 py-5 space-y-4">
                        @php $total = $allOrders > 0 ? $allOrders : 1; @endphp
                        @foreach($statusConfig as $key => $config )
                            @php
                                $count = match($key) {
                                    0 => $newOrders, 1 => $openOrders, 2 => $SalesTransactions,
                                    3 => $closedOrders, 4 => $completedOrders, default => 0
                                };
                                $percentage = round(($count / $total) * 100, 2);
                            @endphp
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-gray-700">{{ $config['label'] }}</span>
                                    <span class="text-xs font-medium text-gray-500">{{ $count }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-{{ $config['color'] }}-500 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function ( ) {
        // *** التعديل الرئيسي هنا: استخدام الإعدادات الجديدة ***

        // 1. قراءة إعدادات الحالة والألوان التي تم تمريرها من PHP
        const statusConfig = @json($statusConfig);
        
        // 2. تحويل الإعدادات إلى صيغة يفهمها Chart.js
        const chartLabels = Object.values(statusConfig).map(config => config.label);
        const chartColors = Object.values(statusConfig).map(config => config.hex);

        // 3. تجميع بيانات الرسم البياني
        const chartData = [
            {{ $newOrders }},
            {{ $openOrders }},
            {{ $SalesTransactions }},
            {{ $closedOrders }},
            {{ $completedOrders }}
        ];

        // 4. إنشاء الرسم البياني مع الإعدادات المحسّنة
        const ctx = document.getElementById('orderStatusChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'عدد الطلبات',
                    data: chartData,
                    backgroundColor: chartColors,
                    borderColor: '#ffffff', // لون أبيض للحدود بين الشرائح لمظهر أنظف
                    borderWidth: 2,
                    hoverOffset: 8 // تأثير بسيط عند مرور الماوس
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        rtl: true,
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                family: 'Tajawal, sans-serif', // تأكد من أن الخط مستخدم في الموقع
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        rtl: true,
                        bodyFont: { family: 'Tajawal, sans-serif' },
                        titleFont: { family: 'Tajawal, sans-serif' },
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                
                                // تجنب القسمة على صفر إذا لم تكن هناك بيانات
                                if (total === 0) {
                                    return ` ${label}: 0 (0%)`;
                                }
                                
                                let percentage = Math.round((value / total) * 100);
                                return ` ${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '70%' // يجعل الرسم البياني أنحف وأكثر حداثة
            }
        });
    });
</script>
@endpush
