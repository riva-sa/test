{{-- resources/views/livewire/mannager/partials/dashboard-overview.blade.php --}}

{{-- Overview Stats Cards --}}
@if(!empty($dashboardData))
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- Total Campaigns --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 hover:shadow-md transition-shadow">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                </div>
                <div class="mr-4 flex-1">
                    <p class="text-sm font-medium text-gray-600">إجمالي الحملات</p>
                    <div class="flex items-baseline">
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($dashboardData['campaigns']['total'] ?? 0) }}</p>
                        @if(isset($dashboardData['growth']['campaigns']))
                            <span class="mr-2 text-sm {{ $dashboardData['growth']['campaigns'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $dashboardData['growth']['campaigns'] >= 0 ? '+' : '' }}{{ $dashboardData['growth']['campaigns'] }}%
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Active Campaigns --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 hover:shadow-md transition-shadow">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mr-4 flex-1">
                    <p class="text-sm font-medium text-gray-600">الحملات النشطة</p>
                    <div class="flex items-baseline">
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($dashboardData['campaigns']['active'] ?? 0) }}</p>
                        <span class="mr-2 text-xs text-gray-500">
                            من {{ $dashboardData['campaigns']['total'] ?? 0 }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Events --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 hover:shadow-md transition-shadow">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mr-4 flex-1">
                    <p class="text-sm font-medium text-gray-600">إجمالي الأحداث</p>
                    <div class="flex items-baseline">
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($dashboardData['current']['total_events'] ?? 0) }}</p>
                        @if(isset($dashboardData['growth']['total_events']))
                            <span class="mr-2 text-sm {{ $dashboardData['growth']['total_events'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $dashboardData['growth']['total_events'] >= 0 ? '+' : '' }}{{ $dashboardData['growth']['total_events'] }}%
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Conversion Rate --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 hover:shadow-md transition-shadow">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <div class="mr-4 flex-1">
                    <p class="text-sm font-medium text-gray-600">معدل التحويل</p>
                    <div class="flex items-baseline">
                        @php
                            $visits = $dashboardData['current']['visits'] ?? 0;
                            $orders = $dashboardData['current']['orders'] ?? 0;
                            $conversionRate = $visits > 0 ? round(($orders / $visits) * 100, 2) : 0;
                        @endphp
                        <p class="text-2xl font-bold text-gray-900">{{ $conversionRate }}%</p>
                        @if(isset($dashboardData['growth']['orders']))
                            <span class="mr-2 text-sm {{ $dashboardData['growth']['orders'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $dashboardData['growth']['orders'] >= 0 ? '+' : '' }}{{ $dashboardData['growth']['orders'] }}%
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Performance Metrics Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    {{-- Event Types Breakdown --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">تفصيل الأحداث</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($dashboardData['current']['visits'] ?? 0) }}</div>
                    <div class="text-sm text-gray-600">زيارات</div>
                    @if(isset($dashboardData['growth']['visits']))
                        <div class="text-xs {{ $dashboardData['growth']['visits'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $dashboardData['growth']['visits'] >= 0 ? '+' : '' }}{{ $dashboardData['growth']['visits'] }}%
                        </div>
                    @endif
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ number_format($dashboardData['current']['views'] ?? 0) }}</div>
                    <div class="text-sm text-gray-600">مشاهدات</div>
                    @if(isset($dashboardData['growth']['views']))
                        <div class="text-xs {{ $dashboardData['growth']['views'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $dashboardData['growth']['views'] >= 0 ? '+' : '' }}{{ $dashboardData['growth']['views'] }}%
                        </div>
                    @endif
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">{{ number_format($dashboardData['current']['shows'] ?? 0) }}</div>
                    <div class="text-sm text-gray-600">عروض</div>
                    @if(isset($dashboardData['growth']['shows']))
                        <div class="text-xs {{ $dashboardData['growth']['shows'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $dashboardData['growth']['shows'] >= 0 ? '+' : '' }}{{ $dashboardData['growth']['shows'] }}%
                        </div>
                    @endif
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">{{ number_format($dashboardData['current']['orders'] ?? 0) }}</div>
                    <div class="text-sm text-gray-600">طلبات</div>
                    @if(isset($dashboardData['growth']['orders']))
                        <div class="text-xs {{ $dashboardData['growth']['orders'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $dashboardData['growth']['orders'] >= 0 ? '+' : '' }}{{ $dashboardData['growth']['orders'] }}%
                        </div>
                    @endif
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ number_format($dashboardData['current']['whatsapp'] ?? 0) }}</div>
                    <div class="text-sm text-gray-600">واتساب</div>
                    @if(isset($dashboardData['growth']['whatsapp']))
                        <div class="text-xs {{ $dashboardData['growth']['whatsapp'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $dashboardData['growth']['whatsapp'] >= 0 ? '+' : '' }}{{ $dashboardData['growth']['whatsapp'] }}%
                        </div>
                    @endif
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($dashboardData['current']['calls'] ?? 0) }}</div>
                    <div class="text-sm text-gray-600">مكالمات</div>
                    @if(isset($dashboardData['growth']['calls']))
                        <div class="text-xs {{ $dashboardData['growth']['calls'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $dashboardData['growth']['calls'] >= 0 ? '+' : '' }}{{ $dashboardData['growth']['calls'] }}%
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Top Performing Campaign --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">أفضل حملة أداءً</h3>
            @if(isset($dashboardData['top_performing_campaign']))
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">اسم الحملة</span>
                        <button wire:click="selectCampaign({{ $dashboardData['top_performing_campaign']['campaign']->id }})" 
                                class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                            {{ $dashboardData['top_performing_campaign']['campaign']->name }}
                        </button>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">المشروع</span>
                        <span class="text-sm font-medium">{{ $dashboardData['top_performing_campaign']['campaign']->project->name }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">نقاط الأداء</span>
                        <span class="text-sm font-bold text-green-600">{{ number_format($dashboardData['top_performing_campaign']['score']) }}</span>
                    </div>
                    <div class="pt-3 border-t border-gray-200">
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="text-center">
                                <div class="font-semibold">{{ number_format($dashboardData['top_performing_campaign']['metrics']['visits'] ?? 0) }}</div>
                                <div class="text-gray-500">زيارات</div>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold">{{ number_format($dashboardData['top_performing_campaign']['metrics']['orders'] ?? 0) }}</div>
                                <div class="text-gray-500">طلبات</div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">لا توجد بيانات كافية</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Recent Campaigns List --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">الحملات الحديثة</h3>
            <button wire:click="switchView('management')" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                عرض الكل
            </button>
        </div>
    </div>
    <div class="divide-y divide-gray-200">
        @forelse($this->campaigns->take(5) as $campaign)
            <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <button wire:click.live="selectCampaign({{ $campaign->id }})" 
                                    class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                {{ $campaign->name }}
                            </button>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $campaign->status === 'active' ? 'bg-green-100 text-green-800' : ($campaign->status === 'paused' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $campaignStatuses[$campaign->status] ?? $campaign->status }}
                            </span>
                        </div>
                        <div class="mt-1 flex items-center space-x-4 space-x-reverse text-sm text-gray-500">
                            <span>{{ $campaign->project->name ?? 'N/A' }}</span>
                            <span>•</span>
                            <span>{{ $availableSources[$campaign->source] ?? $campaign->source }}</span>
                            <span>•</span>
                            <span>{{ $campaign->start_date->format('Y/m/d') }}</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <button wire:click="selectCampaign({{ $campaign->id }})" 
                                class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            عرض التفاصيل
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد حملات</h3>
                <p class="mt-1 text-sm text-gray-500">ابدأ بإنشاء حملتك الأولى</p>
                <div class="mt-6">
                    <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        إنشاء حملة جديدة
                    </button>
                </div>
            </div>
        @endforelse
    </div>
</div>

{{-- Real-time Updates --}}
@if($enableRealTime && !empty($realTimeData))
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="w-3 h-3 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                التحديثات المباشرة
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-xl font-bold text-blue-600">{{ $realTimeData['live_stats']['active_visitors'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">زوار نشطون الآن</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-xl font-bold text-green-600">{{ $realTimeData['live_stats']['events_last_hour'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">أحداث آخر ساعة</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-xl font-bold text-purple-600">{{ $realTimeData['live_stats']['conversion_rate_today'] ?? 0 }}%</div>
                    <div class="text-sm text-gray-600">معدل التحويل اليوم</div>
                </div>
            </div>
        </div>
    </div>
@endif

@else
    {{-- No Data State --}}
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">جاري تحميل البيانات</h3>
        <p class="mt-1 text-sm text-gray-500">يرجى الانتظار...</p>
    </div>
@endif

