<div class="p-4 md:p-6 space-y-6 min-h-screen bg-slate-50/50 font-sans" dir="rtl">
    <!-- Compact Header -->
    <div class="max-w-5xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">سجل النشاطات</h1>
            <p class="text-[11px] text-slate-500 font-medium">متابعة مجمعة للعمليات حسب الطلب.</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="resetFilters" class="text-[10px] font-bold text-slate-400 hover:text-rose-500 transition-colors uppercase tracking-widest bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-sm">
                إعادة ضبط الفلاتر
            </button>
        </div>
    </div>

    <!-- Tighter Filter Bar -->
    <div class="max-w-5xl mx-auto bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x md:divide-x-reverse divide-slate-100">
            <!-- Search -->
            <div class="p-3">
                <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1">البحث العام</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="ابحث عن أي شيء..." class="w-full h-8 bg-transparent border-none p-0 text-xs focus:ring-0 placeholder:text-slate-300">
            </div>

            <!-- Order Select -->
            <div class="p-3" x-data="{ open: false }">
                <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1">تصفية بالطلب</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="orderSearch" placeholder="اختر الطلب..." @focus="open = true" @click.away="open = false" class="w-full h-8 bg-transparent border-none p-0 text-xs focus:ring-0 placeholder:text-slate-300 font-medium">
                    <div x-show="open" class="absolute top-full mt-1 right-0 left-0 bg-white border border-slate-200 rounded-lg shadow-xl z-50 max-h-48 overflow-y-auto p-1 scrollbar-thin">
                        @forelse($orders as $order)
                            <button wire:click="$set('order_id', '{{ $order->id }}'); orderSearch = '{{ $order->name }}'; open = false" class="w-full text-right px-2 py-1.5 text-[11px] hover:bg-slate-50 rounded transition-colors text-slate-600 flex justify-between items-center">
                                <span>#{{ $order->id }} - {{ $order->name }}</span>
                                @if($order_id == $order->id) <svg class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> @endif
                            </button>
                        @empty
                            <div class="p-2 text-[10px] text-slate-400 text-center">لا توجد نتائج</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Actor Select -->
            <div class="p-3" x-data="{ open: false }">
                <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1">تصفية بالموظف</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="actorSearch" placeholder="اختر الموظف..." @focus="open = true" @click.away="open = false" class="w-full h-8 bg-transparent border-none p-0 text-xs focus:ring-0 placeholder:text-slate-300 font-medium">
                    <div x-show="open" class="absolute top-full mt-1 right-0 left-0 bg-white border border-slate-200 rounded-lg shadow-xl z-50 max-h-48 overflow-y-auto p-1 scrollbar-thin">
                        @forelse($actors as $actor)
                            <button wire:click="$set('actor_id', '{{ $actor->id }}'); actorSearch = '{{ $actor->name }}'; open = false" class="w-full text-right px-2 py-1.5 text-[11px] hover:bg-slate-50 rounded transition-colors text-slate-600 flex justify-between items-center">
                                <span>{{ $actor->name }}</span>
                                @if($actor_id == $actor->id) <svg class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> @endif
                            </button>
                        @empty
                            <div class="p-2 text-[10px] text-slate-400 text-center">لا توجد نتائج</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Activity Type -->
            <div class="p-3">
                <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1">نوع النشاط</label>
                <select wire:model.live="activity_type" class="w-full h-8 bg-transparent border-none p-0 text-xs focus:ring-0 font-medium text-slate-700 appearance-none">
                    <option value="">جميع العمليات</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Compact Grouped Feed -->
    <div class="max-w-5xl mx-auto space-y-8">
        @forelse($groupedActivities as $date => $ordersGroup)
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $date }}</span>
                    <div class="h-[1px] flex-1 bg-slate-200"></div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    @foreach($ordersGroup as $orderKey => $group)
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm transition-all hover:border-slate-300 overflow-hidden">
                            <!-- Card Header - Compact -->
                            <div class="px-4 py-3 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-400 shadow-sm shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        @if($group['order'])
                                            <h3 class="text-[12px] font-bold text-slate-900">الطلب #{{ $group['order_id'] }} - {{ $group['order']->name }}</h3>
                                            <span class="text-[9px] font-bold text-emerald-600 bg-emerald-50/50 px-1.5 py-0.5 rounded border border-emerald-100 self-start">{{ $group['order']->project->name ?? 'مشروع غير محدد' }}</span>
                                        @else
                                            <h3 class="text-[12px] font-bold text-slate-900">نشاطات عامة</h3>
                                            <span class="text-[9px] text-slate-400 font-bold">عمليات إدارية</span>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($group['order'])
                                    <a href="{{ route('manager.order-details', $group['order_id']) }}" class="w-7 h-7 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                @endif
                            </div>

                            <!-- Compact Actions List -->
                            <div class="p-4 space-y-6 relative">
                                <div class="absolute right-[31px] top-6 bottom-6 w-[1px] bg-slate-100"></div>

                                @foreach($group['items'] as $activity)
                                    <div class="relative pr-9">
                                        <!-- Mini Node -->
                                        <div class="absolute right-0 top-0 w-7 h-7 rounded-full bg-white border border-slate-100 flex items-center justify-center shadow-sm z-10">
                                            <div class="w-5 h-5 rounded-full bg-slate-900 flex items-center justify-center text-white text-[8px] font-black">
                                                {{ mb_substr($activity->actor->name ?? 'S', 0, 1) }}
                                            </div>
                                        </div>

                                        <div class="space-y-1">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[11px] font-bold text-slate-800">{{ $activity->actor->name ?? 'النظام' }}</span>
                                                    <span class="text-[9px] text-slate-400 font-bold">{{ $activity->created_at->translatedFormat('h:i A') }}</span>
                                                </div>
                                                <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-tighter
                                                    {{ $activity->activity_type === 'status_change' ? 'bg-orange-50 text-orange-600' : '' }}
                                                    {{ $activity->activity_type === 'data_change' ? 'bg-indigo-50 text-indigo-600' : '' }}
                                                    {{ $activity->activity_type === 'permission_grant' ? 'bg-blue-50 text-blue-600' : '' }}
                                                    {{ $activity->activity_type === 'note_added' ? 'bg-amber-50 text-amber-600' : '' }}
                                                    {{ $activity->activity_type === 'leaderboard_adjustment' ? 'bg-emerald-50 text-emerald-600' : '' }}
                                                ">
                                                    {{ $activity->getActivityLabel() }}
                                                </span>
                                            </div>

                                            <div class="text-[11px] text-slate-500 leading-relaxed font-medium">
                                                @if($activity->activity_type === 'status_change')
                                                    @php
                                                        preg_match('/Changed status from (\d+) to (\d+)/', $activity->description, $matches);
                                                        $from = \App\Models\UnitOrder::STATUS_LABELS[$matches[1] ?? ''] ?? ($matches[1] ?? '');
                                                        $to = \App\Models\UnitOrder::STATUS_LABELS[$matches[2] ?? ''] ?? ($matches[2] ?? '');
                                                        $toColor = \App\Models\UnitOrder::STATUS_COLORS[$matches[2] ?? ''] ?? '#000000';
                                                    @endphp
                                                    تغيير الحالة من <span class="text-slate-400 line-through">{{ $from }}</span> 
                                                    إلى <span class="px-1.5 py-0.5 rounded-md text-white text-[9px] font-black" style="background-color: {{ $toColor }}">{{ $to }}</span>
                                                @elseif($activity->activity_type === 'data_change')
                                                    @php
                                                        preg_match('/Modified field \[(.*?)\] from "(.*?)" to "(.*?)"/', $activity->description, $matches);
                                                        $field = $matches[1] ?? '';
                                                        $old = $matches[2] ?? '';
                                                        $new = $matches[3] ?? '';
                                                        
                                                        $fieldMap = [
                                                            'name' => 'الاسم',
                                                            'phone' => 'الهاتف',
                                                            'PurchasePurpose' => 'غرض الشراء',
                                                            'PurchaseType' => 'نوع الشراء',
                                                            'bank_name' => 'البنك',
                                                            'price' => 'السعر'
                                                        ];
                                                        $valueMap = [
                                                            'personal' => 'شخصي',
                                                            'investment' => 'استثماري',
                                                            'bank' => 'بنك',
                                                            'cash' => 'كاش'
                                                        ];
                                                        $fieldName = $fieldMap[$field] ?? $field;
                                                        $oldValue = $valueMap[$old] ?? $old;
                                                        $newValue = $valueMap[$new] ?? $new;
                                                    @endphp
                                                    تعديل حقل [{{ $fieldName }}] من "{{ $oldValue }}" إلى "{{ $newValue }}"
                                                @elseif($activity->activity_type === 'permission_grant')
                                                    @php
                                                        preg_match('/Granted (.*?) permission to user ID (\d+)/', $activity->description, $matches);
                                                        $perm = $matches[1] ?? '';
                                                        $userId = $matches[2] ?? '';
                                                        $permMap = ['manage' => 'إدارة', 'view' => 'عرض'];
                                                        $permLabel = $permMap[$perm] ?? $perm;
                                                        $user = \App\Models\User::find($userId);
                                                        $userName = $user ? $user->name : "رقم $userId";
                                                    @endphp
                                                    منح صلاحية [{{ $permLabel }}] للموظف ({{ $userName }})
                                                @else
                                                    {{ $activity->description }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="py-20 text-center bg-white rounded-xl border border-slate-200">
                <p class="text-xs text-slate-400 font-bold">لا توجد سجلات حالياً تطابق البحث.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($activities->hasPages())
        <div class="max-w-5xl mx-auto pt-8 pb-16">
            {{ $activities->links() }}
        </div>
    @endif
</div>
