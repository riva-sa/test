<div>
    <a href="{{ route('broker.leads') }}" class="inline-flex items-center gap-2 text-xs font-bold text-gray-400 hover:text-gray-900 mb-4 transition-colors">
        <i class="fas fa-arrow-right"></i> العودة لطلباتي
    </a>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-xl font-black text-gray-900">الطلب #{{ $order->id }}</h1>
            <p class="text-sm text-gray-500 mt-1">أُرسل بتاريخ {{ $order->created_at->format('Y-m-d H:i') }} · آخر تحديث {{ $order->updated_at->diffForHumans() }}</p>
        </div>
        <span class="px-4 py-2 text-[12px] font-black rounded-full text-white w-fit" style="background-color: {{ $order->statusColor() }}">
            {{ $order->statusLabel() }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-1 space-y-6">
            {{-- بيانات العميل --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-sm font-black text-gray-900 mb-4"><i class="fas fa-user ml-2 text-gray-300"></i>بيانات العميل</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-50">
                        <span class="text-[11px] font-bold text-gray-400">اسم العميل</span>
                        <span class="text-[13px] font-black text-gray-900">{{ $order->name }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-400">رقم الهاتف</span>
                        <span class="text-[13px] font-bold text-gray-900" dir="ltr">{{ $order->phone }}</span>
                    </div>
                </div>
            </div>

            {{-- تفاصيل الطلب --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-sm font-black text-gray-900 mb-4"><i class="fas fa-clipboard-list ml-2 text-gray-300"></i>تفاصيل الطلب</h2>
                <div class="space-y-4">
                    @foreach ([
                        'المشروع' => $order->project->name ?? '—',
                        'الوحدة' => $order->unit->title ?? '—',
                        'نوع الوحدة' => $order->unit->unit_type ?? '—',
                        'المساحة' => $order->unit?->unit_area ? $order->unit->unit_area.' م²' : '—',
                        'طريقة الشراء' => $order->purchaseTypeLabel(),
                        'الغرض من الشراء' => $order->purchasePurposeLabel(),
                        'نوع الدعم' => $order->support_type ?? '—',
                    ] as $label => $value)
                        <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-50 last:border-0 last:pb-0">
                            <span class="text-[11px] font-bold text-gray-400 whitespace-nowrap">{{ $label }}</span>
                            <span class="text-[12px] font-bold text-gray-900 text-left">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>

                @if ($order->message)
                    <div class="mt-4 p-4 bg-gray-50 rounded-xl">
                        <div class="text-[10px] font-bold text-gray-400 uppercase mb-1.5">الملاحظات</div>
                        <p class="text-[12px] text-gray-700 leading-relaxed whitespace-pre-line">{{ $order->message }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- تايم لاين التحديثات --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-sm font-black text-gray-900 mb-6"><i class="fas fa-timeline ml-2 text-gray-300"></i>سجل تحديثات الطلب</h2>

                <div class="relative">
                    {{-- الخط الرأسي --}}
                    <div class="absolute right-[15px] top-2 bottom-2 w-0.5 bg-gray-100"></div>

                    <div class="space-y-6">
                        @foreach ($this->timeline as $index => $event)
                            <div class="relative flex gap-4">
                                <div class="relative z-10 h-8 w-8 rounded-full border-4 border-white shadow-sm flex-shrink-0 flex items-center justify-center"
                                     style="background-color: {{ $event['color'] }}">
                                    @if ($index === 0)
                                        <span class="h-2 w-2 bg-white rounded-full animate-pulse"></span>
                                    @endif
                                </div>
                                <div class="flex-1 pb-1 {{ $index === 0 ? '' : 'opacity-80' }}">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-1">
                                        <span class="text-[13px] font-black text-gray-900">{{ $event['title'] }}</span>
                                        <span class="text-[10px] font-bold text-gray-400">{{ $event['date']->format('Y-m-d H:i') }} · {{ $event['date']->diffForHumans() }}</span>
                                    </div>
                                    @if ($event['description'])
                                        <p class="text-[12px] text-gray-500 mt-1 leading-relaxed">{{ $event['description'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if ($this->timeline->count() === 1)
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-100 rounded-xl text-[12px] text-blue-700 font-bold">
                        <i class="fas fa-circle-info ml-1"></i>
                        طلبك قيد المراجعة — سيظهر هنا أي تحديث يقوم به فريق المبيعات فور حدوثه.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
