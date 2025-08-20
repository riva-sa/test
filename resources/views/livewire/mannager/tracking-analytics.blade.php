<div class="p-6 bg-white min-h-screen" dir="rtl">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-2xl font-bold text-black">لوحة التحكم - الإحصائيات</h2>
            </div>
            
            <!-- Date Range Filter -->
            <div class="border border-gray-200 rounded-lg px-4 py-2 flex items-center justify-between">
                <label class="text-sm font-medium text-gray-700 me-4 block">الفترة الزمنية</label>
                <select wire:model.live="dateRange" class="border-0 bg-transparent focus:outline-none text-sm">
                    <option value="1">آخر يوم</option>
                    <option value="7">آخر 7 أيام</option>
                    <option value="30">آخر 30 يوم</option>
                    <option value="90">آخر 90 يوم</option>
                    <option value="365">آخر سنة</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="border border-gray-200 rounded-lg p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">إجمالي الأحداث</p>
                    <p class="text-2xl font-bold text-black">{{ number_format($this->analytics['overview']['total_events']) }}</p>
                </div>
                <div class="bg-gray-100 p-2 rounded-lg">
                    <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="border border-gray-200 rounded-lg p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">الزيارات</p>
                    <p class="text-3xl font-bold text-black">{{ number_format($this->analytics['overview']['total_visits']) }}</p>
                </div>
                <div class="bg-gray-100 p-2 rounded-lg">
                    <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="border border-gray-200 rounded-lg p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">(الوحدة) العروض المعروضة</p>
                    <p class="text-3xl font-bold text-black">{{ number_format($this->analytics['overview']['total_shows']) }}</p>
                </div>
                <div class="bg-gray-100 p-2 rounded-lg">
                    <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="border border-gray-200 rounded-lg p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">الطلبات</p>
                    <p class="text-3xl font-bold text-black">{{ number_format($this->analytics['overview']['total_orders']) }}</p>
                </div>
                <div class="bg-gray-100 p-2 rounded-lg">
                    <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 12H6L5 9z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Conversion Rates -->
    <div class="border border-gray-200 rounded-lg p-4 mb-8">
        <h3 class="text-xl font-bold text-black mb-6">معدلات التحويل</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="bg-black text-white rounded-lg p-3">
                    <p class="text-2xl font-bold">{{ $this->conversionRates['visit_to_view'] }} %</p>
                    <p class="text-sm opacity-80">من الزيارة للعرض</p>
                </div>
            </div>
            <div class="text-center">
                <div class="bg-gray-900 text-white rounded-lg p-3">
                    <p class="text-2xl font-bold">{{ $this->conversionRates['view_to_show'] }} %</p>
                    <p class="text-sm opacity-80">من العرض للتفصيل</p>
                </div>
            </div>
            <div class="text-center">
                <div class="bg-gray-800 text-white rounded-lg p-3">
                    <p class="text-2xl font-bold">{{ $this->conversionRates['show_to_order'] }} %</p>
                    <p class="text-sm opacity-80">من التفصيل للطلب</p>
                </div>
            </div>
            <div class="text-center">
                <div class="bg-gray-700 text-white rounded-lg p-3">
                    <p class="text-2xl font-bold">{{ $this->conversionRates['visit_to_order'] }} %</p>
                    <p class="text-sm opacity-80">من الزيارة للطلب</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top Projects -->
        <div class="border border-gray-200 rounded-lg p-6">
            <h3 class="text-xl font-bold text-black mb-6">أفضل المشاريع أداءً</h3>
            <div class="space-y-4">
                @forelse($this->topPerformingContent['projects'] as $project)
                <div class="flex items-center justify-between p-4 border border-gray-100 rounded-lg">
                    <div class="flex-1">
                        <h4 class="font-semibold text-black">{{ $project->name }}</h4>
                        <p class="text-sm text-gray-500">{{ $project->address ?? 'غير محدد' }}</p>
                    </div>
                    <div class="text-left">
                        <div class="flex space-x-4">
                            <div class="text-center me-2">
                                <p class="text-sm text-gray-500">زيارات</p>
                                <p class="font-bold text-black">{{ $project->visits_count }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-500">طلبات</p>
                                <p class="font-bold text-black">{{ $project->orders_count }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <p>لا توجد مشاريع في هذه الفترة</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Top Units -->
        <div class="border border-gray-200 rounded-lg p-6">
            <h3 class="text-xl font-bold text-black mb-6">أفضل الوحدات أداءً</h3>
            <div class="space-y-4">
                @forelse($this->popularUnits as $unit)
                <div class="flex items-center justify-between p-4 border border-gray-100 rounded-lg">
                    <div class="flex-1">
                        <h4 class="font-semibold text-black">وحدة {{ $unit->unit_number ?? $unit->id }}</h4>
                        <p class="text-sm text-gray-500">{{ $unit->project->name ?? 'مشروع غير محدد' }}</p>

                    </div>
                    <div class="text-left">
                        <div class="flex space-x-4">
                            <div class="text-center me-2">
                                <p class="text-sm text-gray-500">عروض</p>
                                <p class="font-bold text-black">{{ $unit->shows_count }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-500">طلبات</p>
                                <p class="font-bold text-black">{{ $unit->orders_count }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <p>لا توجد وحدات شائعة في هذه الفترة</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

        <!-- Traffic Sources & Popular Content -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Traffic Sources -->
        <div class="border border-gray-200 rounded-lg p-6">
            <h3 class="text-xl font-bold text-black mb-6">مصادر الزيارات</h3>
            <div class="space-y-4">
                @php $totalTraffic = $this->trafficSources->sum('count'); @endphp
                @forelse($this->trafficSources as $source)
                @php $percentage = $totalTraffic > 0 ? ($source->count / $totalTraffic) * 100 : 0; @endphp
                <div class="flex items-center justify-between">
                    <div class="flex items-center flex-1"><div class="w-4 h-4 bg-black rounded ml-3"></div><span class="font-medium text-black">{{ $source->source }}</span></div>
                    <div class="flex items-center space-x-3"><div class="text-left ml-2"><span class="text-sm text-gray-600">{{ number_format($source->count) }}</span><span class="text-xs text-gray-400">({{ number_format($percentage, 1) }}%)</span></div><div class="w-24 bg-gray-200 rounded-full h-2"><div class="bg-black h-2 rounded-full" style="width: {{ $percentage }}%"></div></div></div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500"><p>لا توجد بيانات زيارات</p></div>
                @endforelse
            </div>
        </div>

        <!-- Popular Content Summary -->
        <div class="border border-gray-200 rounded-lg p-6">
            <h3 class="text-xl font-bold text-black mb-6">المحتوى الشائع (حسب نقاط التفاعل)</h3>
            <div class="space-y-6">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">الوحدات الأكثر شعبية</h4>
                    <div class="space-y-2">
                        @forelse($this->popularUnits->take(3) as $unit)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"><span class="text-sm font-medium text-black">وحدة {{ $unit->unit_number ?? $unit->id }}</span><span class="text-sm text-gray-600 font-semibold">{{ $unit->popularity_score }} نقطة</span></div>
                        @empty
                        <p class="text-sm text-gray-500">لا توجد وحدات</p>
                        @endforelse
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">المشاريع الأكثر شعبية</h4>
                    <div class="space-y-2">
                        @forelse($this->popularProjects->take(3) as $project)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"><span class="text-sm font-medium text-black">{{ $project->name }}</span><span class="text-sm text-gray-600 font-semibold">{{ $project->popularity_score }} نقطة</span></div>
                        @empty
                        <p class="text-sm text-gray-500">لا توجد مشاريع</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- (NEW) Device & Browser Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="border border-gray-200 rounded-lg p-6">
            <h3 class="text-xl font-bold text-black mb-6">إحصائيات الأجهزة</h3>
            @php $totalDevices = $this->analytics['device_stats']->sum('count'); @endphp
            @if($totalDevices > 0)
                @foreach($this->analytics['device_stats'] as $device)
                    @php $percentage = ($device->count / $totalDevices) * 100; @endphp
                    <div class="mb-2"><div class="flex justify-between mb-1"><span class="text-base font-medium text-black">{{ $device->device_type ?: 'غير معروف' }}</span><span class="text-sm font-medium text-gray-600">{{ number_format($percentage, 1) }}%</span></div><div class="w-full bg-gray-200 rounded-full h-2.5"><div class="bg-gray-700 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div></div></div>
                @endforeach
            @else
                <div class="text-center py-8 text-gray-500"><p>لا توجد بيانات</p></div>
            @endif
        </div>
        <div class="border border-gray-200 rounded-lg p-6">
            <h3 class="text-xl font-bold text-black mb-6">إحصائيات المتصفحات</h3>
            @php $totalBrowsers = $this->analytics['browser_stats']->sum('count'); @endphp
            @if($totalBrowsers > 0)
                @foreach($this->analytics['browser_stats'] as $browser)
                    @php $percentage = ($browser->count / $totalBrowsers) * 100; @endphp
                    <div class="mb-2"><div class="flex justify-between mb-1"><span class="text-base font-medium text-black">{{ $browser->browser ?: 'غير معروف' }}</span><span class="text-sm font-medium text-gray-600">{{ number_format($percentage, 1) }}%</span></div><div class="w-full bg-gray-200 rounded-full h-2.5"><div class="bg-gray-700 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div></div></div>
                @endforeach
            @else
                <div class="text-center py-8 text-gray-500"><p>لا توجد بيانات</p></div>
            @endif
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 border border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-black"></div>
                <span class="text-black font-medium">جاري التحديث...</span>
            </div>
        </div>
    </div>
</div>