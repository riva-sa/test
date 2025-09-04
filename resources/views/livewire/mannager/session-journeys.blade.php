<div class="p-6 bg-gray-50 min-h-screen" dir="rtl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">تحليل سلوك المستخدمين</h1>
            <p class="text-gray-600 mt-1">رؤى حول كيفية تفاعل الزوار مع المنصة.</p>
        </div>
        <a href="{{ route('manager.analytics') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-sm transform hover:scale-105">العودة</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl border border-gray-200">
            <p class="text-sm font-medium text-gray-500">إجمالي الجلسات</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_sessions'] ?? 0) }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200">
            <p class="text-sm font-medium text-gray-500">معدل التحويل</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['conversion_rate'] ?? 0 }}%</p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200">
            <p class="text-sm font-medium text-gray-500">متوسط مدة الجلسة</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ \Carbon\CarbonInterval::seconds($stats['avg_duration'] ?? 0)->cascade()->forHumans(['short' => true]) }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200">
            <p class="text-sm font-medium text-gray-500">متوسط الأحداث/جلسة</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['avg_events'] ?? 0 }}</p>
        </div>
    </div>

    <!-- 2. Top Funnels & Friction Points -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top Conversion Funnels (Redesigned with Icons) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">أكثر مسارات التحويل نجاحاً</h3>
            <div class="space-y-4" dir="ltr">
                @forelse($topFunnels as $path => $count)
                    @php
                        $events = explode(' > ', $path);
                        $eventIcons = [
                            'visit' => ['icon' => '<svg class="h-2 w-2" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.523 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>', 'color' => 'bg-blue-100 text-blue-600', 'label' => 'زيارة صفحة'],
                            'view' => ['icon' => '<svg class="h-2 w-2" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.523 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>', 'color' => 'bg-sky-100 text-sky-600', 'label' => 'عرض تفاصيل'],
                            'show' => ['icon' => '<svg class="h-2 w-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm2-1a1 1 0 00-1 1v10a1 1 0 001 1h12a1 1 0 001-1V5a1 1 0 00-1-1H4z" clip-rule="evenodd" /></svg>', 'color' => 'bg-indigo-100 text-indigo-600', 'label' => 'فتح نافذة عرض'],
                            'call' => ['icon' => '<svg class="h-2 w-2" viewBox="0 0 20 20" fill="currentColor"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" /></svg>', 'color' => 'bg-green-100 text-green-600', 'label' => 'اتصال هاتفي'],
                            'whatsapp' => ['icon' => '<svg class="h-2 w-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.839 8.839 0 01-4.445-1.272L3.317 17.83a.5.5 0 01-.633-.632l1.098-2.894A7.032 7.032 0 012 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM4.773 14.63a.5.5 0 01.29.404l-.81 2.148 2.147-.81a.5.5 0 01.404.29c.36.74.855 1.385 1.441 1.927A6.982 6.982 0 0010 18a6 6 0 006-6c0-3.313-3.134-6-7-6-3.313 0-6 2.687-6 6a6.98 6.98 0 001.227 3.63z" clip-rule="evenodd" /></svg>', 'color' => 'bg-teal-100 text-teal-600', 'label' => 'رسالة واتساب'],
                            'order' => ['icon' => '<svg class="h-2 w-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>', 'color' => 'bg-emerald-100 text-emerald-600', 'label' => 'إتمام طلب'],
                            'unknown' => ['icon' => '<svg class="h-2 w-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>', 'color' => 'bg-gray-100 text-gray-600', 'label' => 'حدث غير معروف'],
                        ];
                    @endphp
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-1 space-x-reverse" style="flex-wrap: wrap;">
                                @foreach($events as $event)
                                    @php $iconData = $eventIcons[$event] ?? $eventIcons['unknown']; @endphp
                                    <div x-data="{ tooltip: false }" class="relative flex items-center">
                                        <div @mouseenter="tooltip = true" @mouseleave="tooltip = false" class="flex items-center justify-center w-4 h-4 rounded-full {{ $iconData['color'] }}">
                                            {!! $iconData['icon'] !!}
                                        </div>
                                        <div x-show="tooltip" class="absolute bottom-full mb-2 w-max px-3 py-1.5 bg-gray-800 text-white text-xs rounded-md shadow-lg z-10" style="display: none;">
                                            {{ $iconData['label'] }}
                                        </div>
                                        @if(!$loop->last)
                                            <div class="text-gray-300 mx-1">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <span class="font-bold text-indigo-600 mr-4 flex-shrink-0">{{ $count }} مرة</span>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">لا توجد مسارات ناجحة لعرضها.</p>
                @endforelse
            </div>
        </div>

        <!-- Friction Points -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">تحليل أداء الوحدات (الأكثر تسرباً)</h3>
            <div class="space-y-4">
                @forelse($frictionPoints as $unit)
                    <div class="p-3 bg-red-50 border border-red-100 rounded-lg">
                        <div class="flex justify-between items-center">
                            <p class="text-sm font-medium text-gray-800">{{ $unit->title ?? ('وحدة ' . $unit->unit_number) }}</p>
                            <span class="text-sm font-bold text-red-600">{{ $unit->drop_offs ?? 0 }} تسرب</span>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-xs text-gray-600">
                            <span>من إجمالي {{ $unit->total_shows ?? 0 }} مشاهدة</span>
                            <span class="font-semibold text-red-700 bg-red-100 px-2 py-0.5 rounded-full">
                                نسبة التسرب: {{ $unit->drop_off_rate ?? 0 }}%
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">لا توجد نقاط تسرب واضحة حالياً.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Journey Details Modal -->
    @if($showJourneyModal)
    <div class="fixed inset-0 bg-gray-900 bg-opacity-60 z-50 flex items-center justify-center p-4" x-data="{ show: @entangle('showJourneyModal') }" x-show="show" @click.self="show = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden" @click.stop>
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">تفاصيل رحلة المستخدم</h3>
                <button @click="show = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="p-6 max-h-[75vh] overflow-y-auto">
                <ol class="relative border-r border-gray-200">
                    @foreach($selectedJourneyEvents as $event)
                    <li class="mb-6 mr-6">
                        <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -right-3 ring-8 ring-white">
                            <!-- Icon based on event type -->
                            @if($event->event_type == 'visit') <svg class="w-3 h-3 text-blue-800" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.523 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path></svg> @endif
                            @if($event->event_type == 'show') <svg class="w-3 h-3 text-purple-800" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-1.707 1.707A1 1 0 003.586 15h12.828a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path></svg> @endif
                            @if(in_array($event->event_type, ['order', 'whatsapp', 'call'])) <svg class="w-3 h-3 text-green-800" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> @endif
                        </span>
                        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-900">{{ ucfirst($event->event_type) }}</span>
                                <time class="text-xs font-normal text-gray-500">{{ $event->created_at->format('H:i:s') }}</time>
                            </div>
                            <p class="text-sm text-gray-700">
                                @if($event->trackable)
                                    {{ $event->trackable_type == 'App\Models\Project' ? 'مشروع' : 'وحدة' }}: 
                                    <span class="font-medium">
                                        @if($event->trackable_type == 'App\Models\Project')
                                            {{ $event->trackable->name }}
                                        @else
                                            {{-- الوحدات تستخدم 'title' وليس 'name' --}}
                                            {{ $event->trackable->title ?? ('وحدة رقم ' . $event->trackable->unit_number) }}
                                        @endif
                                    </span>
                                @endif
                            </p>
                        </div>
                    </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
    @endif
    
</div>

