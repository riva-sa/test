<div class="p-6 space-y-8 min-h-screen font-sans" dir="rtl">
    <!-- Clean Header -->
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">سجل النشاطات</h1>
            <p class="text-sm text-gray-500 mt-1">متابعة التغييرات والعمليات الجارية في النظام.</p>
        </div>
        <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm">
            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
            <span class="text-xs font-medium text-gray-600">تحديث مباشر</span>
        </div>
    </div>

    <!-- Shadcn-like Filters Bar -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="relative">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="بحث في السجلات..." 
                class="w-full h-10 bg-white border-gray-200 rounded-md text-sm focus:ring-1 focus:ring-zinc-400 focus:border-zinc-400 transition-all placeholder:text-gray-400">
        </div>
        <select wire:model.live="order_id" class="h-10 bg-white border-gray-200 rounded-md text-sm focus:ring-1 focus:ring-zinc-400">
            <option value="">كل الطلبات</option>
            @foreach($orders as $order)
                <option value="{{ $order->id }}">#{{ $order->id }} - {{ $order->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="actor_id" class="h-10 bg-white border-gray-200 rounded-md text-sm focus:ring-1 focus:ring-zinc-400">
            <option value="">كل الموظفين</option>
            @foreach($actors as $actor)
                <option value="{{ $actor->id }}">{{ $actor->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="activity_type" class="h-10 bg-white border-gray-200 rounded-md text-sm focus:ring-1 focus:ring-zinc-400">
            <option value="">كل الأنواع</option>
            @foreach($types as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <!-- Timeline Thread -->
    <div class="max-w-4xl mx-auto space-y-1 relative">
        @forelse($activities as $activity)
            <div class="relative pl-0 pr-12 py-4 group">
                <!-- Thread Line -->
                @if(!$loop->last)
                    <div class="absolute right-[19px] top-10 bottom-0 w-[1px] bg-gray-200 group-hover:bg-gray-300 transition-colors"></div>
                @endif

                <!-- Avatar Circle -->
                <div class="absolute right-0 top-4 w-10 h-10 rounded-full border border-gray-200 bg-white flex items-center justify-center overflow-hidden z-10 shadow-sm group-hover:border-gray-300 transition-all">
                    <span class="text-xs font-medium text-gray-500 uppercase">{{ mb_substr($activity->actor->name ?? 'S', 0, 1) }}</span>
                </div>

                <!-- Content Card -->
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md hover:border-gray-300 transition-all duration-200">
                    <!-- Top Meta -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-900">{{ $activity->actor->name ?? 'النظام الآلي' }}</span>
                            <span class="text-gray-300">•</span>
                            <span class="text-[11px] text-gray-400">{{ $activity->created_at->diffForHumans() }}</span>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-tight
                                {{ $activity->activity_type === 'status_change' ? 'bg-zinc-100 text-zinc-700' : '' }}
                                {{ $activity->activity_type === 'permission_grant' ? 'bg-blue-50 text-blue-700' : '' }}
                                {{ $activity->activity_type === 'note_added' ? 'bg-amber-50 text-amber-700' : '' }}
                                {{ $activity->activity_type === 'leaderboard_adjustment' ? 'bg-emerald-50 text-emerald-700' : '' }}
                            ">
                                {{ $activity->getActivityLabel() }}
                            </span>
                        </div>
                    </div>

                    <!-- Main Description -->
                    <div class="text-sm text-gray-600 leading-relaxed mb-4">
                        @if($activity->activity_type === 'status_change')
                            @php
                                preg_match('/Changed status from (\d+) to (\d+)/', $activity->description, $matches);
                                $from = \App\Models\UnitOrder::STATUS_LABELS[$matches[1] ?? ''] ?? ($matches[1] ?? '');
                                $to = \App\Models\UnitOrder::STATUS_LABELS[$matches[2] ?? ''] ?? ($matches[2] ?? '');
                                $fromColor = \App\Models\UnitOrder::STATUS_COLORS[$matches[1] ?? ''] ?? '#94a3b8';
                                $toColor = \App\Models\UnitOrder::STATUS_COLORS[$matches[2] ?? ''] ?? '#94a3b8';
                            @endphp
                            قام بتغيير حالة الطلب من 
                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 text-gray-700 text-xs font-bold mx-1">{{ $from }}</span>
                            إلى 
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-white text-xs font-bold mx-1 shadow-sm" style="background-color: {{ $toColor }}">{{ $to }}</span>
                        @elseif($activity->activity_type === 'permission_grant')
                            <div class="flex items-center gap-2 bg-blue-50/50 p-3 rounded-lg border border-blue-100/50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                <span>{{ $activity->description }}</span>
                            </div>
                        @else
                            {{ $activity->description }}
                        @endif
                    </div>

                    <!-- Linked Order Section -->
                    @if($activity->order)
                        <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-lg border border-gray-100 group/link cursor-pointer transition-colors hover:bg-gray-100" 
                            onclick="window.location='{{ route('manager.order-details', $activity->order_id) }}'">
                            <div class="w-8 h-8 rounded bg-white border border-gray-200 flex items-center justify-center text-gray-400 group-hover/link:text-zinc-900 shadow-sm transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-[10px] text-gray-400 block font-bold tracking-tight uppercase">الطلب المرتبط</span>
                                <span class="text-sm font-semibold text-gray-800 truncate block">#{{ $activity->order_id }} - {{ $activity->order->name }}</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300 group-hover/link:translate-x-[-4px] transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="py-20 text-center">
                <div class="w-16 h-16 bg-white rounded-full border border-gray-200 flex items-center justify-center mx-auto mb-4 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-gray-500 font-medium">لا توجد سجلات حالياً.</p>
            </div>
        @endforelse
    </div>

    <!-- Simple Pagination -->
    @if($activities->hasPages())
        <div class="max-w-4xl mx-auto pt-10">
            {{ $activities->links() }}
        </div>
    @endif
</div>
