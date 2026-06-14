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
                        'كيف سمعت عنا' => $broker->heardAboutUsLabel(),
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
        </div>

        {{-- العقد --}}
        <div class="lg:col-span-2">
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
