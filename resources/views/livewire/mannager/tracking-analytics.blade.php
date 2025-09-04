<div class="p-6 bg-gray-50 min-h-screen" dir="rtl">
    <!-- رسالة النجاح -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <!-- 1. العنوان الرئيسي والفلاتر -->
    <div class="mb-6">
        <div class="flex flex-wrap items-center justify-between gap-y-4 mb-5">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">لوحة تحكم الأداء</h1>
            <a href="{{ route('manager.journeys') }}" wire:navigate 
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-sm transform hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                <span>تتبع رحلات العملاء</span>
            </a>
        </div>
        <!-- منطقة الفلاتر -->
        <div class="bg-white p-4 rounded-lg border border-gray-200 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center space-x-4 space-x-reverse">
                <!-- فلتر العرض -->
                <div class="flex items-center border border-gray-200 rounded-lg">
                    <div class="p-2">
                        <select wire:model.live="filterMode" class="border-0 bg-transparent focus:outline-none text-sm font-medium">
                            <option value="general">عرض عام</option>
                            <option value="project">حسب المشروع</option>
                            <option value="campaign">حسب الحملة</option>
                        </select>
                    </div>
                    <!-- فلاتر مشروطة -->
                    @if($filterMode === 'project')
                    <div class="p-2 border-s">
                        <select wire:model.live="selectedProject" class="border-0 bg-transparent focus:outline-none text-sm min-w-[150px]">
                            <option value="">-- اختر المشروع --</option>
                            @foreach($this->projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    @if($filterMode === 'campaign')
                    <div class="p-2 border-s">
                        <select wire:model.live="selectedCampaign" class="border-0 bg-transparent focus:outline-none text-sm min-w-[150px]">
                            <option value="">-- اختر الحملة --</option>
                            @foreach($this->campaigns as $campaign)
                            <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- فلتر التاريخ -->
            <div class="flex items-center border border-gray-200 rounded-lg p-2">
                <input type="checkbox" wire:model.live="useCustomDate" id="useCustomDate" class="me-2 h-4 w-4 rounded">
                <label for="useCustomDate" class="text-sm me-3 whitespace-nowrap">تاريخ مخصص</label>
                
                @if($useCustomDate)
                    <input type="date" wire:model.live="customStartDate" class="border-0 bg-transparent focus:outline-none text-sm me-2">
                    <span class="me-2 text-gray-400">إلى</span>
                    <input type="date" wire:model.live="customEndDate" class="border-0 bg-transparent focus:outline-none text-sm">
                @else
                    <select wire:model.live="dateRange" class="border-0 bg-transparent focus:outline-none text-sm">
                        <option value="1">آخر 24 ساعة</option>
                        <option value="7">آخر 7 أيام</option>
                        <option value="30">آخر 30 يوم</option>
                        <option value="90">آخر 90 يوم</option>
                        <option value="365">آخر سنة</option>
                    </select>
                @endif
            </div>
        </div>
    </div>

    <!-- 2. شريط الملخص التنفيذي (Executive Summary) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- إجمالي الزيارات -->
        <div class="bg-white border border-gray-200 rounded-lg p-4 flex items-center">
            <div class="bg-blue-100 p-3 rounded-lg me-4"><svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></div>
            <div>
                <p class="text-sm font-medium text-gray-500">الزيارات</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($this->analytics['overview']['total_visits']) }}</p>
            </div>
        </div>
        <!-- إجمالي التفاعلات -->
        <div class="bg-white border border-gray-200 rounded-lg p-4 flex items-center">
            <div class="bg-green-100 p-3 rounded-lg me-4"><svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>
            <div>
                <p class="text-sm font-medium text-gray-500">إجمالي التفاعلات (Leads)</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($this->analytics['overview']['total_whatsapp'] + $this->analytics['overview']['total_calls'] + $this->analytics['overview']['total_orders']) }}</p>
            </div>
        </div>
        <!-- معدل التحويل الإجمالي -->
        <div class="bg-white border border-gray-200 rounded-lg p-4 flex items-center">
            <div class="bg-purple-100 p-3 rounded-lg me-4"><svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg></div>
            <div>
                <p class="text-sm font-medium text-gray-500">معدل التحويل</p>
                <p class="text-2xl font-bold text-gray-800">{{ $this->conversionRates['engagement_rate'] }}<span class="text-lg">%</span></p>
            </div>
        </div>
        <!-- نقاط الأداء (ديناميكي) -->
        @if($filterMode === 'campaign' && $this->campaignAnalytics)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center">
            <div class="bg-blue-100 p-3 rounded-lg me-4"><svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg></div>
            <div>
                <p class="text-sm font-medium text-blue-600">أداء الحملة</p>
                <p class="text-2xl font-bold text-blue-800">{{ $this->campaignAnalytics['performance_score'] }}</p>
            </div>
        </div>
        @else
        <div class="bg-white border border-gray-200 rounded-lg p-4 flex items-center">
            <div class="bg-gray-100 p-3 rounded-lg me-4"><svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5h1.586a1 1 0 01.707.293l2.414 2.414a1 1 0 001.414 0l2.414-2.414a1 1 0 01.707-.293H17v5m-5 0h-2"></path></svg></div>
            <div>
                <p class="text-sm font-medium text-gray-500">أداء المشروع</p>
                <p class="text-2xl font-bold text-gray-800">{{ $this->projectAnalytics ? 'محدد' : 'عام' }}</p>
            </div>
        </div>
        @endif
    </div>

    <!-- 3. تفاصيل الحملة / المشروع (Banner) -->
    @if($filterMode === 'campaign' && $selectedCampaign && $this->campaignAnalytics)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
        <h3 class="text-lg font-bold text-blue-800">تفاصيل الحملة: {{ $this->campaigns->firstWhere('id', $selectedCampaign)->name ?? '' }}</h3>
        <p class="text-blue-600 text-sm">المصدر: {{ $this->campaigns->firstWhere('id', $selectedCampaign)->source_name ?? '' }} | المدة: {{ $this->campaignAnalytics['duration_days'] }} يوم | المشروع: {{ $this->campaignAnalytics['project']->name ?? '' }}</p>
    </div>
    @endif
    @if($filterMode === 'project' && $selectedProject && $this->projectAnalytics)
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-8">
        <h3 class="text-lg font-bold text-green-800">تفاصيل المشروع: {{ $this->projectAnalytics['project']->name }}</h3>
        <p class="text-green-600 text-sm">المدة: {{ $this->projectAnalytics['period_days'] }} يوم | عدد الوحدات: {{ $this->projectAnalytics['units_count'] }}</p>
    </div>
    @endif

    <!-- 4. مسار التحويل (Conversion Funnel) -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
        <h3 class="text-xl font-bold text-gray-800 mb-6">مسار التحويل الرئيسي</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <!-- الخطوات الأساسية -->
            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">الزيارات</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($this->analytics['overview']['total_visits']) }}</p>
            </div>
            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">عرض التفاصيل</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($this->analytics['overview']['total_shows']) }}</p>
                <p class="text-xs text-blue-600 font-semibold">{{ $this->conversionRates['visit_to_view'] }}% من الزيارات</p>
            </div>
            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">الطلبات</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($this->analytics['overview']['total_orders']) }}</p>
                <p class="text-xs text-blue-600 font-semibold">{{ $this->conversionRates['show_to_order'] }}% من العروض</p>
            </div>
            <!-- قنوات التفاعل -->
            <div class="text-center p-3 bg-green-50 rounded-lg border border-green-200">
                <p class="text-sm text-green-700">تواصل واتساب</p>
                <p class="text-2xl font-bold text-green-800">{{ number_format($this->analytics['overview']['total_whatsapp']) }}</p>
                <p class="text-xs text-green-600 font-semibold">{{ $this->conversionRates['visit_to_whatsapp'] }}% من الزيارات</p>
            </div>
            <div class="text-center p-3 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm text-blue-700">اتصال هاتفي</p>
                <p class="text-2xl font-bold text-blue-800">{{ number_format($this->analytics['overview']['total_calls']) }}</p>
                <p class="text-xs text-blue-600 font-semibold">{{ $this->conversionRates['visit_to_call'] }}% من الزيارات</p>
            </div>
            <div class="text-center p-3 bg-gray-800 text-white rounded-lg">
                <p class="text-sm opacity-80">طلب اهتمام</p>
                <p class="text-2xl font-bold">{{ $this->conversionRates['visit_to_order'] }}%</p>
                <p class="text-xs opacity-60">من زيارة إلى تفاعل</p>
            </div>
        </div>
    </div>

    <!-- 5. أداء المحتوى (المشاريع والوحدات) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- أفضل المشاريع أداءً -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">أفضل المشاريع أداءً</h3>
            <div class="space-y-4">
                @forelse($this->topPerformingContent['projects'] as $project)
                <div class="p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $project->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $project->address ?? 'غير محدد' }}</p>
                        </div>
                        <div class="text-left flex space-x-4 space-x-reverse">
                            <div class="text-center"><p class="font-bold text-gray-800">{{ $project->visits_count }}</p><p class="text-xs text-gray-500">زيارة</p></div>
                            <div class="text-center"><p class="font-bold text-green-600">{{ $project->whatsapp_count }}</p><p class="text-xs text-gray-500">واتساب</p></div>
                            <div class="text-center"><p class="font-bold text-blue-600">{{ $project->calls_count }}</p><p class="text-xs text-gray-500">اتصال</p></div>
                            <div class="text-center"><p class="font-bold text-gray-900">{{ $project->orders_count }}</p><p class="text-xs text-gray-500">طلب</p></div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500"><p>لا توجد مشاريع في هذه الفترة</p></div>
                @endforelse
            </div>
        </div>

        <!-- أفضل الوحدات أداءً -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">أفضل الوحدات أداءً</h3>
            <div class="space-y-4">
                @forelse($this->popularUnits as $unit)
                <div class="p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-gray-800">وحدة {{ $unit->unit_number ?? $unit->id }}</h4>
                            <p class="text-sm text-gray-500">{{ $unit->project->name ?? 'مشروع غير محدد' }}</p>
                        </div>
                        <div class="text-left flex space-x-4 space-x-reverse">
                            <div class="text-center"><p class="font-bold text-gray-800">{{ $unit->shows_count }}</p><p class="text-xs text-gray-500">عرض</p></div>
                            <div class="text-center"><p class="font-bold text-green-600">{{ $unit->whatsapp_count }}</p><p class="text-xs text-gray-500">واتساب</p></div>
                            <div class="text-center"><p class="font-bold text-blue-600">{{ $unit->calls_count }}</p><p class="text-xs text-gray-500">اتصال</p></div>
                            <div class="text-center"><p class="font-bold text-gray-900">{{ $unit->orders_count }}</p><p class="text-xs text-gray-500">طلب</p></div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500"><p>لا توجد وحدات شائعة في هذه الفترة</p></div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 6. مصادر الزيارات والإحصائيات التقنية -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- مصادر الزيارات -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">مصادر الزيارات</h3>
            <div class="space-y-4">
                @php $totalTraffic = $this->trafficSources->sum('count'); @endphp
                @forelse($this->trafficSources as $source)
                @php $percentage = $totalTraffic > 0 ? ($source->count / $totalTraffic) * 100 : 0; @endphp
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-base font-medium text-gray-700">{{ $source->source }}</span>
                        <span class="text-sm font-medium text-gray-500">{{ number_format($source->count) }} ({{ number_format($percentage, 1) }}%)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-gray-800 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500"><p>لا توجد بيانات زيارات</p></div>
                @endforelse
            </div>
        </div>

        <!-- إحصائيات الأجهزة والمتصفحات -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">إحصائيات تقنية</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">حسب الجهاز</h4>
                    @php $totalDevices = $this->analytics['device_stats']->sum('count'); @endphp
                    @if($totalDevices > 0)
                        @foreach($this->analytics['device_stats'] as $device)
                            @php $percentage = ($device->count / $totalDevices) * 100; @endphp
                            <div class="mb-2">
                                <div class="flex justify-between mb-1"><span class="text-sm font-medium text-gray-600">{{ $device->device_type ?: 'غير معروف' }}</span><span class="text-sm font-medium text-gray-500">{{ number_format($percentage, 1) }}%</span></div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5"><div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div></div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500">لا توجد بيانات</p>
                    @endif
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">حسب المتصفح</h4>
                    @php $totalBrowsers = $this->analytics['browser_stats']->sum('count'); @endphp
                    @if($totalBrowsers > 0)
                        @foreach($this->analytics['browser_stats'] as $browser)
                            @php $percentage = ($browser->count / $totalBrowsers) * 100; @endphp
                            <div class="mb-2">
                                <div class="flex justify-between mb-1"><span class="text-sm font-medium text-gray-600">{{ $browser->browser ?: 'غير معروف' }}</span><span class="text-sm font-medium text-gray-500">{{ number_format($percentage, 1) }}%</span></div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5"><div class="bg-purple-600 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div></div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500">لا توجد بيانات</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- مؤشر التحميل -->
    <div wire:loading class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-xl p-8 shadow-2xl max-w-sm mx-4" style="position: absolute;
            transform: translate(-50%,-50%);
            top: 50%;
            left: 50%;">
            <div class="flex flex-col items-center space-y-4">
                <svg class="loading-spinner w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900">جاري تحديث البيانات</h3>
                    <p class="text-sm text-gray-600 mt-1">يرجى الانتظار...</p>
                </div>
            </div>
        </div>
    </div>
</div>
