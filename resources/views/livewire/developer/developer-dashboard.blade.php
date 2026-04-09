<div class="p-4 sm:p-6" wire:poll.30s>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">لوحة المطوّر العقاري</h1>
        <p class="text-sm text-gray-600 mt-1">مشاريعك وأداء المبيعات وزيارات الموقع (يتم التحديث كل 30 ثانية)</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-sm text-gray-500">المشاريع</p>
            <p class="text-3xl font-semibold text-gray-900 mt-1">{{ number_format($stats['projects_count']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-sm text-gray-500">إجمالي الطلبات</p>
            <p class="text-3xl font-semibold text-gray-900 mt-1">{{ number_format($stats['orders_total']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-sm text-gray-500">طلبات آخر 30 يوماً</p>
            <p class="text-3xl font-semibold text-gray-900 mt-1">{{ number_format($stats['orders_last_30']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-sm text-gray-500">زيارات الموقع (مشاريع + وحدات)</p>
            <p class="text-3xl font-semibold text-gray-900 mt-1">{{ number_format($stats['site_visits_total']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">أداء المبيعات حسب الحالة</h2>
            <ul class="space-y-2 text-sm">
                @php
                    $labels = [0 => 'جديد', 1 => 'طلب مفتوح', 2 => 'معاملات بيعية', 3 => 'مغلق', 4 => 'مكتمل', 5 => 'قائمة انتظار'];
                @endphp
                @foreach($labels as $k => $label)
                    <li class="flex justify-between border-b border-gray-100 py-2">
                        <span class="text-gray-600">{{ $label }}</span>
                        <span class="font-medium text-gray-900">{{ number_format($stats['orders_by_status'][$k] ?? 0) }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">إحصاءات التتبع (إجمالي)</h2>
            <ul class="space-y-2 text-sm">
                <li class="flex justify-between border-b border-gray-100 py-2">
                    <span class="text-gray-600">مشاهدات (views)</span>
                    <span class="font-medium text-gray-900">{{ number_format($stats['views_total']) }}</span>
                </li>
                <li class="flex justify-between border-b border-gray-100 py-2">
                    <span class="text-gray-600">عروض (shows)</span>
                    <span class="font-medium text-gray-900">{{ number_format($stats['shows_total']) }}</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">مشاريع حديثة</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right font-medium text-gray-600">المشروع</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600">الوحدات</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600">زيارات</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600">آخر تحديث</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentProjects as $project)
                        <tr>
                            <td class="px-4 py-3 text-gray-900">{{ $project->name }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ number_format($project->units_count) }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ number_format($project->visits_count) }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $project->updated_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">لا توجد مشاريع مرتبطة بهذا المطوّر.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
