<div class="p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-black text-gray-900">طلبات الوسطاء</h1>
            <p class="text-xs text-gray-500 mt-1">مراجعة واعتماد طلبات تسجيل الوسطاء العقاريين</p>
        </div>
        <div class="flex items-center gap-3">
            @if ($pendingCount > 0)
                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs font-black rounded-full w-fit">
                    <span class="h-2 w-2 bg-yellow-500 rounded-full animate-pulse"></span>
                    {{ $pendingCount }} طلب بانتظار المراجعة
                </span>
            @endif
            <a href="{{ route('manager.broker-contract-template') }}" 
               class="px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-black rounded-xl transition-all flex items-center gap-1.5">
                <i class="fas fa-file-contract"></i> إعداد قالب العقد
            </a>
        </div>
    </div>

    @if (session('message'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 text-sm font-bold rounded-xl">
            {{ session('message') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="flex flex-col md:flex-row gap-3 mb-5">
        <div class="relative flex-1">
            <i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
            <input type="text" wire:model.live.debounce.400ms="search"
                   class="w-full pr-10 pl-4 py-2.5 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm"
                   placeholder="بحث بالاسم، البريد، الواتساب أو رقم العضوية...">
        </div>
        <select wire:model.live="statusFilter" class="px-4 py-2.5 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm">
            <option value="">كل الحالات</option>
            <option value="pending">قيد المراجعة</option>
            <option value="approved">معتمد</option>
            <option value="rejected">مرفوض</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-gray-50/50 text-[11px] text-gray-400 font-bold uppercase">
                        <th class="px-5 py-3">الوسيط</th>
                        <th class="px-5 py-3">رقم العضوية</th>
                        <th class="px-5 py-3">المدينة</th>
                        <th class="px-5 py-3">الواتساب</th>
                        <th class="px-5 py-3">تاريخ التسجيل</th>
                        <th class="px-5 py-3">العملاء</th>
                        <th class="px-5 py-3">الحالة</th>
                        <th class="px-5 py-3">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($brokers as $broker)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-4">
                                <div class="text-[13px] font-bold text-gray-900">{{ $broker->name ?? '—' }}</div>
                                <div class="text-[11px] text-gray-400">{{ $broker->email }}</div>
                            </td>
                            <td class="px-5 py-4 text-[12px] font-bold text-gray-600">{{ $broker->reference_number }}</td>
                            <td class="px-5 py-4 text-[12px] text-gray-600">{{ $broker->city ?? '—' }}</td>
                            <td class="px-5 py-4 text-[12px] text-gray-600" dir="ltr">{{ $broker->whatsapp ?? '—' }}</td>
                            <td class="px-5 py-4 text-[12px] text-gray-600">{{ $broker->created_at->format('Y-m-d') }}</td>
                            <td class="px-5 py-4 text-[12px] font-bold text-gray-600">{{ $broker->orders_count }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-black rounded-full text-white" style="background-color: {{ $broker->statusColor() }}">
                                    {{ $broker->statusLabel() }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-1.5">
                                    <button wire:click="viewBroker({{ $broker->id }})" class="px-3 py-1.5 text-[11px] font-bold text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg transition-all">
                                        التفاصيل
                                    </button>
                                    @if (! $broker->isApproved())
                                        <button wire:click="approve({{ $broker->id }})" wire:confirm="هل أنت متأكد من اعتماد هذا الوسيط؟" class="px-3 py-1.5 text-[11px] font-bold text-white bg-green-600 hover:bg-green-700 rounded-lg transition-all">
                                            اعتماد
                                        </button>
                                    @endif
                                    @if (! $broker->isRejected())
                                        <button wire:click="openRejectModal({{ $broker->id }})" class="px-3 py-1.5 text-[11px] font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-all">
                                            رفض
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">لا توجد طلبات تسجيل</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-50">
            {{ $brokers->links() }}
        </div>
    </div>

    {{-- Details modal --}}
    @if ($selectedBroker && ! $showRejectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40" wire:click="closeDetails"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-50 flex items-center justify-between sticky top-0 bg-white">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">{{ $selectedBroker->name }}</h3>
                        <span class="text-xs text-gray-400">{{ $selectedBroker->reference_number }} · {{ $selectedBroker->brokerTypeLabel() }}</span>
                    </div>
                    <button wire:click="closeDetails" class="p-2 text-gray-400 hover:text-gray-900"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase mb-1">البريد الإلكتروني</div>
                            <div class="text-sm font-bold text-gray-900">{{ $selectedBroker->email }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase mb-1">رقم الواتساب</div>
                            <div class="text-sm font-bold text-gray-900" dir="ltr">{{ $selectedBroker->whatsapp ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase mb-1">رقم الهوية / الإقامة</div>
                            <div class="text-sm font-bold text-gray-900">{{ $selectedBroker->national_id ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase mb-1">المدينة</div>
                            <div class="text-sm font-bold text-gray-900">{{ $selectedBroker->city ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase mb-1">الآيبان</div>
                            <div class="text-sm font-bold text-gray-900" dir="ltr">{{ $selectedBroker->iban ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase mb-1">الحالة الوظيفية</div>
                            <div class="text-sm font-bold text-gray-900">{{ $selectedBroker->employmentStatusLabel() }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase mb-1">كيف سمع عنا</div>
                            <div class="text-sm font-bold text-gray-900">{{ $selectedBroker->heardAboutUsLabel() }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase mb-1">تاريخ التسجيل</div>
                            <div class="text-sm font-bold text-gray-900">{{ $selectedBroker->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                    </div>

                    {{-- Documents --}}
                    <div>
                        <div class="text-[10px] font-bold text-gray-400 uppercase mb-2">المستندات</div>
                        <div class="space-y-2">
                            @forelse ($selectedBroker->documents as $document)
                                <a href="{{ route('manager.broker-documents.show', $document->id) }}" target="_blank"
                                   class="flex items-center justify-between p-3 border border-gray-100 rounded-xl hover:bg-gray-50 transition-all">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-file-alt text-gray-300"></i>
                                        <div>
                                            <div class="text-[13px] font-bold text-gray-900">{{ $document->typeLabel() }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $document->original_name }}</div>
                                        </div>
                                    </div>
                                    <i class="fas fa-download text-gray-400 text-xs"></i>
                                </a>
                            @empty
                                <div class="text-sm text-gray-400">لا توجد مستندات مرفوعة</div>
                            @endforelse
                        </div>
                    </div>

                    @if ($selectedBroker->isRejected() && $selectedBroker->rejection_reason)
                        <div class="p-4 bg-red-50 border border-red-100 rounded-xl">
                            <div class="text-[10px] font-bold text-red-400 uppercase mb-1">سبب الرفض</div>
                            <div class="text-sm text-red-800">{{ $selectedBroker->rejection_reason }}</div>
                        </div>
                    @endif

                    @if ($selectedBroker->isApproved())
                        <div class="p-4 bg-green-50 border border-green-100 rounded-xl text-sm text-green-800 font-bold">
                            تم الاعتماد بتاريخ {{ $selectedBroker->approved_at?->format('Y-m-d H:i') }}
                            @if ($selectedBroker->approvedBy) بواسطة {{ $selectedBroker->approvedBy->name }} @endif
                        </div>

                        {{-- عقد الوساطة --}}
                        <div class="p-4 border border-gray-100 rounded-xl">
                            <div class="flex items-center justify-between mb-3">
                                <div class="text-[10px] font-bold text-gray-400 uppercase">عقد الوساطة</div>
                                @if ($selectedBroker->contractSigned())
                                    <span class="px-2.5 py-1 bg-green-500 text-white text-[10px] font-black rounded-full">موقّع ✓</span>
                                @elseif ($selectedBroker->contractSent())
                                    <span class="px-2.5 py-1 bg-yellow-500 text-white text-[10px] font-black rounded-full">بانتظار توقيع الوسيط</span>
                                @else
                                    <span class="px-2.5 py-1 bg-gray-400 text-white text-[10px] font-black rounded-full">جاري التجهيز</span>
                                @endif
                            </div>

                            {{-- Auto-generated badge --}}
                            @if ($selectedBroker->contractSent())
                                <div class="flex items-center gap-1.5 mb-3 p-2 bg-gray-50 rounded-lg">
                                    <i class="fas fa-magic text-indigo-400 text-xs"></i>
                                    <span class="text-[10px] text-gray-500 font-bold">مولَّد تلقائياً من القالب الثابت</span>
                                </div>
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <a href="{{ route('manager.broker-contract.show', ['broker' => $selectedBroker->id, 'type' => 'contract']) }}" target="_blank"
                                       class="inline-flex items-center gap-2 px-3 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-100 text-gray-700 text-[11px] font-bold rounded-lg transition-all">
                                        <i class="fas fa-file-pdf text-red-400"></i> عقد الوسيط ({{ $selectedBroker->contract_sent_at?->format('Y-m-d') }})
                                    </a>
                                    @if ($selectedBroker->contractSigned())
                                        <a href="{{ route('manager.broker-contract.show', ['broker' => $selectedBroker->id, 'type' => 'signed']) }}" target="_blank"
                                           class="inline-flex items-center gap-2 px-3 py-2 bg-green-50 hover:bg-green-100 border border-green-100 text-green-700 text-[11px] font-bold rounded-lg transition-all">
                                            <i class="fas fa-file-signature"></i> النسخة الموقعة ({{ $selectedBroker->contract_signed_at?->format('Y-m-d') }})
                                        </a>
                                    @endif
                                </div>
                            @else
                                <div class="flex items-center gap-2 p-3 bg-blue-50 border border-blue-100 rounded-xl mb-3">
                                    <i class="fas fa-info-circle text-blue-400 text-sm"></i>
                                    <p class="text-[11px] text-blue-700">سيُولَّد العقد تلقائياً لدى الاعتماد من القالب الثابت وتُرسل للوسيط مباشرةً.</p>
                                </div>
                            @endif

                            {{-- Regenerate / resend action --}}
                            @if (! $selectedBroker->contractSigned())
                                <button wire:click="regenerateContract({{ $selectedBroker->id }})"
                                        wire:confirm="هل أنت متأكد؟ سيُلغى أي توقيع سابق ويُرسل عقد جديد للوسيط."
                                        wire:loading.attr="disabled"
                                        class="w-full py-2.5 bg-gray-900 hover:bg-gray-800 disabled:opacity-50 text-white text-xs font-black rounded-xl transition-all">
                                    <span wire:loading.remove wire:target="regenerateContract">
                                        <i class="fas fa-sync-alt ml-1"></i>
                                        {{ $selectedBroker->contractSent() ? 'إعادة توليد وإرسال عقد جديد' : 'توليد وإرسال العقد الآن' }}
                                    </span>
                                    <span wire:loading wire:target="regenerateContract">جاري التوليد...</span>
                                </button>
                                @if ($selectedBroker->contractSent())
                                    <p class="text-[10px] text-gray-400 mt-1.5">إعادة التوليد تُلغي أي توقيع سابق وتتطلب توقيع الوسيط من جديد.</p>
                                @endif
                            @endif
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex gap-3 pt-2">
                        @if (! $selectedBroker->isApproved())
                            <button wire:click="approve({{ $selectedBroker->id }})" wire:confirm="هل أنت متأكد من اعتماد هذا الوسيط؟"
                                    class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-black rounded-xl transition-all">
                                <i class="fas fa-check ml-2"></i> اعتماد الحساب
                            </button>
                        @endif
                        @if (! $selectedBroker->isRejected())
                            <button wire:click="openRejectModal({{ $selectedBroker->id }})"
                                    class="flex-1 py-3 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-black rounded-xl transition-all">
                                <i class="fas fa-times ml-2"></i> رفض الطلب
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Reject modal --}}
    @if ($showRejectModal && $selectedBroker)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40" wire:click="closeDetails"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                <h3 class="text-lg font-black text-gray-900 mb-1">رفض طلب التسجيل</h3>
                <p class="text-xs text-gray-500 mb-5">سيتم إرسال إشعار بالرفض إلى {{ $selectedBroker->name }}</p>

                <label class="block text-sm font-bold text-gray-700 mb-2">سبب الرفض <span class="text-gray-400 text-xs font-medium">(اختياري)</span></label>
                <textarea wire:model="rejectionReason" rows="4"
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm"
                          placeholder="مثال: المستندات المرفوعة غير واضحة..."></textarea>
                @error('rejectionReason') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror

                <div class="flex gap-3 mt-6">
                    <button wire:click="closeDetails" class="px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-black rounded-xl transition-all">
                        إلغاء
                    </button>
                    <button wire:click="reject" wire:loading.attr="disabled"
                            class="flex-1 py-3 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white text-sm font-black rounded-xl transition-all">
                        <span wire:loading.remove wire:target="reject">تأكيد الرفض</span>
                        <span wire:loading wire:target="reject">جاري الرفض...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
