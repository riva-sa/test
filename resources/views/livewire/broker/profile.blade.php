<div>
    <div class="mb-6">
        <h1 class="text-xl font-black text-gray-900">الملف الشخصي</h1>
        <p class="text-sm text-gray-500 mt-1">بياناتك المسجلة لدى ريفا العقارية وعقد الوساطة الخاص بك</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- البيانات الشخصية --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="h-16 w-16 rounded-2xl bg-gray-900 text-white flex items-center justify-center font-black text-2xl">
                        {{ mb_substr($broker->name ?? 'و', 0, 1) }}
                    </div>
                    <div>
                        <div class="text-base font-black text-gray-900">{{ $broker->name }}</div>
                        <div class="text-[11px] font-bold text-gray-400 mt-0.5">{{ $broker->reference_number }} · {{ $broker->brokerTypeLabel() }}</div>
                        <span class="inline-block mt-1.5 px-2.5 py-0.5 text-[10px] font-black rounded-full text-white" style="background-color: {{ $broker->statusColor() }}">
                            {{ $broker->statusLabel() }}
                        </span>
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach ([
                        'البريد الإلكتروني' => $broker->email,
                        'رقم الهوية / الإقامة' => $broker->national_id,
                        'رقم الواتساب' => $broker->whatsapp,
                        'المدينة' => $broker->city,
                        'رقم الآيبان' => $broker->iban,
                        'الحالة الوظيفية' => $broker->employmentStatusLabel(),
                        'تاريخ التسجيل' => $broker->created_at->format('Y-m-d'),
                        'تاريخ الاعتماد' => $broker->approved_at?->format('Y-m-d') ?? '—',
                    ] as $label => $value)
                        <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-50 last:border-0 last:pb-0">
                            <span class="text-[11px] font-bold text-gray-400">{{ $label }}</span>
                            <span class="text-[12px] font-bold text-gray-900 text-left break-all" @if($label === 'رقم الآيبان' || $label === 'رقم الواتساب') dir="ltr" @endif>{{ $value ?? '—' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- المستندات --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-sm font-black text-gray-900 mb-4">مستنداتي</h2>
                <div class="space-y-2">
                    @forelse ($broker->documents as $document)
                        <a href="{{ route('broker.documents.show', $document->id) }}" target="_blank"
                           class="flex items-center justify-between p-3 border border-gray-100 rounded-xl hover:bg-gray-50 transition-all">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-file-alt text-gray-300"></i>
                                <div>
                                    <div class="text-[12px] font-bold text-gray-900">{{ $document->typeLabel() }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $document->original_name }}</div>
                                </div>
                            </div>
                            <i class="fas fa-eye text-gray-400 text-xs"></i>
                        </a>
                    @empty
                        <div class="text-sm text-gray-400">لا توجد مستندات</div>
                    @endforelse
                </div>
            </div>

            {{-- عمولتي --}}
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-6 text-white">
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-hand-holding-dollar"></i>
                    <h2 class="text-sm font-black">عمولتي على المبيعات</h2>
                </div>
                <div class="text-lg font-black leading-tight mb-1">{{ number_format($totalCommission) }} ر.س</div>
                <p class="text-[11px] text-emerald-50/80 font-bold">إجمالي عمولاتك عن {{ $soldUnitsCount }} وحدة مباعة. تختلف نسبة العمولة من مشروع لآخر، وتظهر على صفحة كل مشروع.</p>
            </div>
        </div>

        {{-- العمولات والعقد --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ملخص العمولات والوحدات المباعة --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 hidden">
                <h2 class="text-sm font-black text-gray-900 mb-4">عمولاتي والوحدات المباعة</h2>

                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <div class="text-2xl font-black text-gray-900">{{ $soldUnitsCount }}</div>
                        <div class="text-[11px] font-bold text-gray-400 mt-1">عدد الوحدات المباعة</div>
                    </div>
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100">
                        <div class="text-2xl font-black text-emerald-700">{{ number_format($totalCommission, 2) }} <span class="text-sm">ريال</span></div>
                        <div class="text-[11px] font-bold text-emerald-500 mt-1">إجمالي العمولات المستحقة</div>
                    </div>
                </div>

                @if ($sales->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-right">
                            <thead>
                                <tr class="text-[10px] font-bold text-gray-400 uppercase border-b border-gray-100">
                                    <th class="py-2 pl-3">الوحدة</th>
                                    <th class="py-2 px-3">المشروع</th>
                                    <th class="py-2 px-3">قيمة الوحدة</th>
                                    <th class="py-2 px-3">العمولة</th>
                                    <th class="py-2 pr-3">التاريخ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($sales as $sale)
                                    <tr class="text-[12px]">
                                        <td class="py-3 pl-3 font-bold text-gray-900">{{ $sale['unit'] }}</td>
                                        <td class="py-3 px-3 text-gray-500">{{ $sale['project'] }}</td>
                                        <td class="py-3 px-3 text-gray-500" dir="ltr">{{ number_format($sale['price'], 2) }}</td>
                                        <td class="py-3 px-3 font-black text-emerald-700" dir="ltr">{{ number_format($sale['commission'], 2) }}</td>
                                        <td class="py-3 pr-3 text-gray-400">{{ $sale['date']?->format('Y-m-d') ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center bg-gray-50 rounded-xl">
                        <i class="fas fa-chart-line text-2xl text-gray-300 mb-2"></i>
                        <p class="text-sm text-gray-400 font-bold">لا توجد وحدات مباعة بعد. ستظهر عمولاتك هنا فور إتمام أول عملية بيع.</p>
                    </div>
                @endif
            </div>

            {{-- العقد --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6" x-data="{ tab: 'contract' }">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
                    <div>
                        <h2 class="text-sm font-black text-gray-900">عقد الوساطة</h2>
                        @if ($broker->contractSigned())
                            <p class="text-[11px] text-green-600 font-bold mt-1">
                                <i class="fas fa-circle-check ml-1"></i>
                                موقّع بتاريخ {{ $broker->contract_signed_at->format('Y-m-d') }}
                            </p>
                        @elseif ($broker->contractSent())
                            <p class="text-[11px] text-yellow-600 font-bold mt-1">بانتظار توقيعك</p>
                        @endif
                    </div>

                    @if ($broker->contractSent())
                        <div class="flex items-center gap-2">
                            <div class="flex bg-gray-50 rounded-xl p-1">
                                <button @click="tab = 'contract'" :class="tab === 'contract' ? 'bg-gray-900 text-white' : 'text-gray-500 hover:text-gray-900'"
                                        class="px-3 py-1.5 text-[11px] font-black rounded-lg transition-all">العقد الأصلي</button>
                                @if ($broker->contractSigned())
                                    <button @click="tab = 'signed'" :class="tab === 'signed' ? 'bg-gray-900 text-white' : 'text-gray-500 hover:text-gray-900'"
                                            class="px-3 py-1.5 text-[11px] font-black rounded-lg transition-all">نسختي الموقعة</button>
                                @endif
                            </div>
                            <a href="{{ route('broker.contract.view') }}" download
                               class="px-3 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-100 text-gray-600 text-[11px] font-black rounded-xl transition-all">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    @endif
                </div>

                @if ($broker->contractSent())
                    {{-- معاينة العقد داخل الصفحة --}}
                    <div x-show="tab === 'contract'" class="rounded-xl overflow-hidden border border-gray-100 bg-gray-50">
                        <iframe src="{{ route('broker.contract.view') }}#toolbar=0" class="w-full h-[70vh]" title="عقد الوساطة"></iframe>
                    </div>
                    @if ($broker->contractSigned())
                        <div x-show="tab === 'signed'" x-cloak class="rounded-xl overflow-hidden border border-gray-100 bg-gray-50">
                            <iframe src="{{ route('broker.contract.signed-view') }}#toolbar=0" class="w-full h-[70vh]" title="النسخة الموقعة"></iframe>
                        </div>
                    @endif
                @else
                    <div class="p-10 text-center bg-gray-50 rounded-xl">
                        <i class="fas fa-file-contract text-3xl text-gray-300 mb-3"></i>
                        <p class="text-sm text-gray-400 font-bold">لم يتم إرسال العقد بعد من قبل الإدارة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
