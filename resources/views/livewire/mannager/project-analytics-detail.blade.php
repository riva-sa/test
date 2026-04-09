<div class="p-6 bg-gray-50 min-h-screen" dir="rtl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">تحليل أداء المشروع: {{ $project->name }}</h1>
            <p class="text-gray-600 mt-1">تتبع أداء المشروع وتفاعل الزوار</p>
        </div>
        <div class="flex gap-4">
            <select wire:model.live="dateRange" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white cursor-pointer px-4">
                <option value="today">اليوم</option>
                <option value="yesterday">الأمس</option>
                <option value="7">آخر 7 أيام</option>
                <option value="30">آخر 30 يوم</option>
            </select>
            <a href="{{ route('manager.analytics') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700">العودة</a>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl border border-gray-200">
            <p class="text-sm font-medium text-gray-500">إجمالي الزيارات</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($analytics['overview']['total_visits'] ?? 0) }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200">
            <p class="text-sm font-medium text-gray-500">إجمالي الطلبات</p>
            <p class="text-3xl font-bold text-emerald-600 mt-1">{{ number_format($analytics['overview']['total_orders'] ?? 0) }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200">
            <p class="text-sm font-medium text-gray-500">مشاهدة الوحدات</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($analytics['overview']['total_shows'] ?? 0) }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200">
            <p class="text-sm font-medium text-gray-500">معدل التحويل الشامل</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $analytics['conversion_rates']['visit_to_order'] ?? 0 }}%</p>
        </div>
    </div>

    <!-- Detailed Stats Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Content interactions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">أكثر الوحدات تفاعلاً</h3>
            </div>
            <div class="p-6">
                <!-- Unit list goes here -->
                @if(isset($analytics['top_units']) && count($analytics['top_units']) > 0)
                    <ul class="space-y-4">
                        @foreach($analytics['top_units'] as $unit)
                            <li class="flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-100">
                                <div>
                                    <span class="font-medium text-gray-800">{{ $unit->title ?? ('وحدة ' . $unit->unit_number) }}</span>
                                    <div class="text-xs text-gray-500">طلبات: {{ $unit->orders_count ?? 0 }} | مشاهدات: {{ $unit->shows_count ?? 0 }}</div>
                                </div>
                                <a href="{{ route('manager.analytics.units.detail', $unit->id) }}" class="inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded text-sm font-medium transition-colors">
                                    تفاصيل الوحدة
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 text-center">لا يوجد بيانات لعرضها.</p>
                @endif
            </div>
        </div>

        <!-- Sources -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">المصادر الرئيسية</h3>
            </div>
            <div class="p-6">
                @if(isset($analytics['traffic_sources']) && count($analytics['traffic_sources']) > 0)
                    <ul class="space-y-4">
                        @foreach($analytics['traffic_sources'] as $source)
                            <li class="flex justify-between items-center p-2 border-b border-gray-50 last:border-b-0">
                                <span class="text-gray-700 font-medium">{{ $source->source ?? $source['source'] ?? 'غير محدد' }}</span>
                                <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2.5 py-1 rounded-full">{{ $source->count ?? $source['count'] ?? 0 }} زيارة</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 text-center">لا يوجد بيانات لعرضها.</p>
                @endif
            </div>
        </div>
    </div>
</div>
