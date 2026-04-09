<div class="p-6 bg-gray-50 min-h-screen" dir="rtl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">تحليل أداء الوحدة: {{ $unit->title ?? $unit->unit_number }}</h1>
            <p class="text-gray-600 mt-1">مشروع: <a href="{{ route('manager.analytics.projects.detail', $unit->project_id) }}" class="text-indigo-600 underline font-medium hover:text-indigo-800">{{ $unit->project->name }}</a></p>
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-center items-center text-center hover:-translate-y-1 transition duration-300">
            <div class="bg-sky-100 text-sky-600 p-3 rounded-full mb-3">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-500">مشاهدة التفاصيل (النافذة)</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($analytics['overview']['total_views'] ?? 0) }}</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-center items-center text-center hover:-translate-y-1 transition duration-300">
            <div class="bg-indigo-100 text-indigo-600 p-3 rounded-full mb-3">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-500">عروض عامة</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($analytics['overview']['total_shows'] ?? 0) }}</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-center items-center text-center hover:-translate-y-1 transition duration-300">
            <div class="bg-emerald-100 text-emerald-600 p-3 rounded-full mb-3">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-500">الطلبات المؤكدة</p>
            <p class="text-3xl font-bold text-emerald-600 mt-1">{{ number_format($analytics['overview']['total_orders'] ?? 0) }}</p>
        </div>
    </div>

    <!-- Conversion & Engagement -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">معدلات التحويل للوحدة</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">معدل التحويل (مشاهدة إلى طلب):</span>
                    <span class="font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md">{{ $analytics['conversion_rates']['view_to_order'] ?? 0 }}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">معدل التفاعل العام:</span>
                    <span class="font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md">{{ $analytics['conversion_rates']['engagement_rate'] ?? 0 }}%</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">تواصل مباشر</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fab fa-whatsapp text-green-500 text-xl"></i>
                        <span class="text-gray-600">نقرات الواتساب:</span>
                    </div>
                    <span class="font-bold text-gray-900 bg-gray-100 px-3 py-1 rounded-full">{{ number_format($analytics['overview']['total_whatsapp'] ?? 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-phone-alt text-blue-500 text-lg"></i>
                        <span class="text-gray-600">اتصالات هاتفية:</span>
                    </div>
                    <span class="font-bold text-gray-900 bg-gray-100 px-3 py-1 rounded-full">{{ number_format($analytics['overview']['total_calls'] ?? 0) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
