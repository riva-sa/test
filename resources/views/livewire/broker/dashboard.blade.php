<div>
    <div class="mb-8">
        <h1 class="text-xl md:text-2xl font-black text-gray-900">مرحباً، {{ $broker->name }} 👋</h1>
        <p class="text-sm text-gray-500 mt-1">رقم العضوية: <span class="font-bold text-gray-700">{{ $broker->reference_number }}</span></p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="h-9 w-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3"><i class="fas fa-users text-sm"></i></div>
            <div class="text-2xl font-black text-gray-900">{{ $stats['total'] }}</div>
            <div class="text-[11px] font-bold text-gray-400 mt-1">العملاء المرسلين</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="h-9 w-9 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center mb-3"><i class="fas fa-spinner text-sm"></i></div>
            <div class="text-2xl font-black text-gray-900">{{ $stats['processing'] }}</div>
            <div class="text-[11px] font-bold text-gray-400 mt-1">قيد المعالجة</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="h-9 w-9 rounded-xl bg-green-50 text-green-600 flex items-center justify-center mb-3"><i class="fas fa-circle-check text-sm"></i></div>
            <div class="text-2xl font-black text-gray-900">{{ $stats['completed'] }}</div>
            <div class="text-[11px] font-bold text-gray-400 mt-1">المكتملين</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="h-9 w-9 rounded-xl bg-gray-100 text-gray-500 flex items-center justify-center mb-3"><i class="fas fa-user-slash text-sm"></i></div>
            <div class="text-2xl font-black text-gray-900">{{ $stats['not_interested'] }}</div>
            <div class="text-[11px] font-bold text-gray-400 mt-1">غير المهتمين</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="h-9 w-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-3"><i class="fas fa-building text-sm"></i></div>
            <div class="text-2xl font-black text-gray-900">{{ $stats['projects'] }}</div>
            <div class="text-[11px] font-bold text-gray-400 mt-1">المشاريع المتاحة</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="h-9 w-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center mb-3"><i class="fas fa-house text-sm"></i></div>
            <div class="text-2xl font-black text-gray-900">{{ $stats['units'] }}</div>
            <div class="text-[11px] font-bold text-gray-400 mt-1">الوحدات المتاحة</div>
        </div>
    </div>

    {{-- Earnings (only admin-approved commissions are shown as money) --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8">
        <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-100">
            <div class="text-2xl font-black text-emerald-700">{{ number_format($stats['earned'], 2) }} <span class="text-sm">ريال</span></div>
            <div class="text-[11px] font-bold text-emerald-500 mt-1">عمولاتك المعتمدة</div>
        </div>
        <div class="p-5 rounded-2xl bg-green-50 border border-green-100">
            <div class="text-2xl font-black text-green-700">{{ number_format($stats['paid'], 2) }} <span class="text-sm">ريال</span></div>
            <div class="text-[11px] font-bold text-green-500 mt-1">المدفوع لك</div>
        </div>
        <div class="p-5 rounded-2xl bg-amber-50 border border-amber-100">
            <div class="text-2xl font-black text-amber-700">{{ number_format($stats['outstanding'], 2) }} <span class="text-sm">ريال</span></div>
            <div class="text-[11px] font-bold text-amber-500 mt-1">قيد الصرف</div>
        </div>
        <div class="p-5 rounded-2xl bg-gray-50 border border-gray-100">
            <div class="text-2xl font-black text-gray-700">{{ $stats['under_review'] }}</div>
            <div class="text-[11px] font-bold text-gray-400 mt-1">صفقات قيد المراجعة</div>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <a href="{{ route('broker.leads.create') }}" class="flex items-center justify-between p-6 bg-gray-900 text-white rounded-2xl hover:bg-gray-800 transition-all group">
            <div>
                <div class="text-base font-black">إرسال عميل جديد</div>
                <div class="text-xs text-gray-400 mt-1">أرسل عميلاً مهتماً وتابع حالته لحظة بلحظة</div>
            </div>
            <i class="fas fa-arrow-left text-lg opacity-50 group-hover:opacity-100 group-hover:-translate-x-1 transition-all"></i>
        </a>
        <a href="{{ route('broker.projects') }}" class="flex items-center justify-between p-6 bg-white border border-gray-100 rounded-2xl hover:border-gray-300 transition-all group">
            <div>
                <div class="text-base font-black text-gray-900">تصفح المشاريع</div>
                <div class="text-xs text-gray-400 mt-1">استعرض المشاريع والوحدات المتاحة وخطط السداد</div>
            </div>
            <i class="fas fa-arrow-left text-lg text-gray-300 group-hover:text-gray-900 group-hover:-translate-x-1 transition-all"></i>
        </a>
    </div>

    {{-- Latest leads --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-50 flex items-center justify-between">
            <h2 class="text-sm font-black text-gray-900">آخر العملاء المرسلين</h2>
            <a href="{{ route('broker.leads') }}" class="text-xs font-bold text-gray-400 hover:text-gray-900">عرض الكل</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse ($latestLeads as $lead)
                <div class="p-4 flex items-center justify-between">
                    <div>
                        <div class="text-[13px] font-bold text-gray-900">{{ $lead->name }}</div>
                        <div class="text-[11px] text-gray-400 mt-0.5">
                            {{ $lead->project->name ?? 'بدون مشروع' }} · {{ $lead->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-[10px] font-black rounded-full text-white" style="background-color: {{ $lead->statusColor() }}">
                        {{ $lead->statusLabel() }}
                    </span>
                </div>
            @empty
                <div class="p-8 text-center text-sm text-gray-400">لم ترسل أي عملاء بعد</div>
            @endforelse
        </div>
    </div>
</div>
