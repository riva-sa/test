<div>
    <div class="mb-6">
        <h1 class="text-xl font-black text-gray-900">طلباتي</h1>
        <p class="text-sm text-gray-500 mt-1">تابع حالة العملاء الذين أرسلتهم — تتحدث الحالة تلقائياً مع أي تغيير من فريق المبيعات</p>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col md:flex-row gap-3 mb-5">
        <div class="relative flex-1">
            <i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
            <input type="text" wire:model.live.debounce.400ms="search"
                   class="w-full pr-10 pl-4 py-2.5 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm"
                   placeholder="بحث برقم الطلب، اسم العميل أو رقم الهاتف...">
        </div>
        <select wire:model.live="statusFilter" class="px-4 py-2.5 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm">
            <option value="">كل الحالات</option>
            @foreach ($statusLabels as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        <select wire:model.live="projectFilter" class="px-4 py-2.5 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm">
            <option value="">كل المشاريع</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Leads table --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-gray-50/50 text-[11px] text-gray-400 font-bold uppercase">
                        <th class="px-5 py-3">رقم الطلب</th>
                        <th class="px-5 py-3">اسم العميل</th>
                        <th class="px-5 py-3">المشروع</th>
                        <th class="px-5 py-3">الوحدة</th>
                        <th class="px-5 py-3">تاريخ الإرسال</th>
                        <th class="px-5 py-3">الحالة الحالية</th>
                        <th class="px-5 py-3">آخر تحديث</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($leads as $lead)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-4 text-[12px] font-black text-gray-900">#{{ $lead->id }}</td>
                            <td class="px-5 py-4">
                                <div class="text-[13px] font-bold text-gray-900">{{ $lead->name }}</div>
                                <div class="text-[11px] text-gray-400" dir="ltr">{{ $lead->phone }}</div>
                            </td>
                            <td class="px-5 py-4 text-[12px] text-gray-600 font-bold">{{ $lead->project->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-[12px] text-gray-600">{{ $lead->unit->title ?? '—' }}</td>
                            <td class="px-5 py-4 text-[12px] text-gray-600">{{ $lead->created_at->format('Y-m-d') }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-black rounded-full text-white" style="background-color: {{ $lead->statusColor() }}">
                                    {{ $lead->statusLabel() }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-[11px] text-gray-400">{{ $lead->updated_at->diffForHumans() }}</td>
                            <td class="px-5 py-4">
                                <a href="{{ route('broker.leads.show', $lead->id) }}" class="px-3 py-1.5 text-[11px] font-bold text-gray-600 bg-gray-50 hover:bg-gray-900 hover:text-white rounded-lg transition-all whitespace-nowrap">
                                    التفاصيل <i class="fas fa-arrow-left mr-1 text-[9px]"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">
                                لا توجد طلبات بعد —
                                <a href="{{ route('broker.leads.create') }}" class="font-black text-gray-900 hover:underline">أرسل أول عميل الآن</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-50">
            {{ $leads->links() }}
        </div>
    </div>
</div>
