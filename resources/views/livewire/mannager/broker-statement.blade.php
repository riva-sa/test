<div>
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-start justify-between">
                <div>
                    <a href="{{ route('manager.brokers') }}" class="text-xs text-gray-400 hover:text-gray-600">← كل الوسطاء</a>
                    <h1 class="text-2xl font-bold text-gray-900 mt-1">كشف حساب: {{ $broker->name }}</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $broker->reference_number }} · {{ $broker->statusLabel() }}
                        @if ($broker->iban) · آيبان: {{ $broker->iban }} @endif
                    </p>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="mt-4 p-4 text-sm text-green-800 rounded-lg bg-green-50">{{ session('message') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="mt-4 p-4 text-sm text-red-800 rounded-lg bg-red-50">{{ session('error') }}</div>
            @endif
        </div>
    </div>

    <div class="bg-white min-h-screen p-2 sm:p-4">
        <!-- Summary -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                <p class="text-xs text-gray-500 font-semibold">صفقات مكتملة</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($soldCount) }}</p>
            </div>
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                <p class="text-xs text-blue-700 font-semibold">إجمالي مكتسب</p>
                <p class="text-xl font-bold text-blue-800 mt-1">{{ number_format($earnedTotal, 2) }} <span class="text-xs">ريال</span></p>
            </div>
            <div class="bg-green-50 border border-green-100 rounded-xl p-4">
                <p class="text-xs text-green-700 font-semibold">مدفوع</p>
                <p class="text-xl font-bold text-green-800 mt-1">{{ number_format($paidTotal, 2) }} <span class="text-xs">ريال</span></p>
            </div>
            <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4">
                <p class="text-xs text-yellow-700 font-semibold">مستحق (غير مدفوع)</p>
                <p class="text-xl font-bold text-yellow-800 mt-1">{{ number_format($outstandingTotal, 2) }} <span class="text-xs">ريال</span></p>
            </div>
        </div>

        <!-- Filter -->
        <div class="mb-4">
            <select wire:model.live="statusFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 pr-10">
                <option value="">كل الحالات</option>
                @foreach ($statusLabels as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right text-gray-700">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 font-semibold">المشروع / الوحدة</th>
                            <th class="px-4 py-3 font-semibold">سعر الوحدة</th>
                            <th class="px-4 py-3 font-semibold">النسبة</th>
                            <th class="px-4 py-3 font-semibold">العمولة</th>
                            <th class="px-4 py-3 font-semibold">الحالة</th>
                            <th class="px-4 py-3 font-semibold">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($commissions as $commission)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50" wire:key="commission-{{ $commission->id }}">
                                <td class="px-4 py-3">
                                    <div class="text-gray-900">{{ $commission->project?->name ?? '—' }}</div>
                                    <div class="text-xs text-gray-400">{{ $commission->unit?->title ?? 'طلب #'.$commission->unit_order_id }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ number_format((float) $commission->unit_price, 2) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $commission->commission_type === 'fixed' ? number_format((float) $commission->commission_value, 2).' ثابت' : rtrim(rtrim(number_format((float) $commission->commission_value, 2, '.', ''), '0'), '.').'%' }}
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-900 whitespace-nowrap">{{ number_format((float) $commission->commission_amount, 2) }} ريال</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full"
                                        style="color: {{ $commission->statusColor() }}; background-color: {{ $commission->statusColor() }}20;">
                                        {{ $commission->statusLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        @if ($commission->isPending())
                                            <button wire:click="approveCommission({{ $commission->id }})" class="text-blue-600 hover:text-blue-800 text-xs font-medium">اعتماد</button>
                                            <button wire:click="openVoidModal({{ $commission->id }})" class="text-red-500 hover:text-red-700 text-xs font-medium">إلغاء</button>
                                        @elseif ($commission->isApproved())
                                            <button wire:click="openPayModal({{ $commission->id }})" class="text-green-600 hover:text-green-800 text-xs font-medium">تسجيل دفع</button>
                                            <button wire:click="openVoidModal({{ $commission->id }})" class="text-red-500 hover:text-red-700 text-xs font-medium">إلغاء</button>
                                        @elseif ($commission->isPaid())
                                            <span class="text-xs text-gray-400">{{ $commission->payment_reference ? 'مرجع: '.$commission->payment_reference : 'مدفوعة' }}</span>
                                            @can('reverse-broker-commissions')
                                                <button wire:click="openReverseModal({{ $commission->id }})" class="text-red-500 hover:text-red-700 text-xs font-medium">عكس الدفعة</button>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">لا توجد عمولات لهذا الوسيط بعد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Immutable audit trail: every payment / reversal event -->
        @if ($payments->isNotEmpty())
            <div class="mt-6">
                <h2 class="text-sm font-bold text-gray-900 mb-3">سجلّ الحركات المالية</h2>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right text-gray-700">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">الحدث</th>
                                    <th class="px-4 py-3 font-semibold">المبلغ</th>
                                    <th class="px-4 py-3 font-semibold">المرجع / السبب</th>
                                    <th class="px-4 py-3 font-semibold">بواسطة</th>
                                    <th class="px-4 py-3 font-semibold">التاريخ</th>
                                    <th class="px-4 py-3 font-semibold">الإيصال</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $payment)
                                    <tr class="border-b border-gray-50" wire:key="payment-{{ $payment->id }}">
                                        <td class="px-4 py-3">
                                            <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $payment->action === 'reversed' ? 'text-red-700 bg-red-50' : 'text-green-700 bg-green-50' }}">
                                                {{ $payment->actionLabel() }}
                                            </span>
                                            <span class="text-xs text-gray-400 mr-1">طلب #{{ $payment->commission?->unit_order_id }}</span>
                                        </td>
                                        <td class="px-4 py-3 font-semibold whitespace-nowrap">{{ number_format((float) $payment->amount, 2) }} ريال</td>
                                        <td class="px-4 py-3 text-xs text-gray-600 max-w-xs">{{ $payment->reason ?: $payment->payment_reference }}</td>
                                        <td class="px-4 py-3 text-xs text-gray-500">{{ $payment->performed_by_name ?? $payment->performedBy?->name ?? '—' }}</td>
                                        <td class="px-4 py-3 text-xs text-gray-400 whitespace-nowrap">{{ $payment->created_at?->format('Y-m-d H:i') }}</td>
                                        <td class="px-4 py-3">
                                            @if ($payment->receipt_path)
                                                <a href="{{ route('manager.commission-receipt.show', $payment->id) }}" target="_blank" class="text-primary-600 hover:underline text-xs font-medium">عرض الإيصال</a>
                                            @else
                                                <span class="text-xs text-gray-300">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @include('livewire.mannager.partials.commission-payment-modals')
</div>
