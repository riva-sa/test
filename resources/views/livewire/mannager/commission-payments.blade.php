<div>
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">سجلّ الحركات المالية</h1>
                <p class="text-sm text-gray-500 mt-1">سجلّ دائم وغير قابل للتعديل لكل عمليات دفع وعكس عمولات الوسطاء</p>
            </div>
        </div>
    </div>

    <div class="bg-white min-h-screen p-2 sm:p-4">
        <!-- Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div class="bg-green-50 border border-green-100 rounded-xl p-4">
                <p class="text-xs text-green-700 font-semibold">إجمالي المدفوع</p>
                <p class="text-2xl font-bold text-green-800 mt-1">{{ number_format($totalPaid, 2) }} <span class="text-sm">ريال</span></p>
            </div>
            <div class="bg-red-50 border border-red-100 rounded-xl p-4">
                <p class="text-xs text-red-700 font-semibold">إجمالي المعكوس</p>
                <p class="text-2xl font-bold text-red-800 mt-1">{{ number_format($totalReversed, 2) }} <span class="text-sm">ريال</span></p>
            </div>
            <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                <p class="text-xs text-gray-500 font-semibold">الصافي المدفوع فعلياً</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalPaid - $totalReversed, 2) }} <span class="text-sm">ريال</span></p>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-col md:flex-row gap-3 mb-4">
            <input type="text" wire:model.live.debounce.400ms="search"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full md:w-1/3 p-2.5"
                placeholder="ابحث باسم الوسيط أو رقم الحوالة...">
            <select wire:model.live="actionFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 pr-10">
                <option value="">كل الحركات</option>
                @foreach ($actionLabels as $value => $label)
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
                            <th class="px-4 py-3 font-semibold">الحركة</th>
                            <th class="px-4 py-3 font-semibold">الوسيط</th>
                            <th class="px-4 py-3 font-semibold">المبلغ</th>
                            <th class="px-4 py-3 font-semibold">المرجع / السبب</th>
                            <th class="px-4 py-3 font-semibold">بواسطة</th>
                            <th class="px-4 py-3 font-semibold">التاريخ</th>
                            <th class="px-4 py-3 font-semibold">الإيصال</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50" wire:key="payment-{{ $payment->id }}">
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $payment->action === 'reversed' ? 'text-red-700 bg-red-50' : 'text-green-700 bg-green-50' }}">
                                        {{ $payment->actionLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('manager.broker-statement', $payment->broker_id) }}" class="font-medium text-primary-600 hover:underline">
                                        {{ $payment->broker?->name ?? '—' }}
                                    </a>
                                    <div class="text-xs text-gray-400">{{ $payment->broker?->reference_number }} · طلب #{{ $payment->commission?->unit_order_id }}</div>
                                </td>
                                <td class="px-4 py-3 font-semibold whitespace-nowrap {{ $payment->action === 'reversed' ? 'text-red-700' : 'text-gray-900' }}">
                                    {{ $payment->action === 'reversed' ? '−' : '' }}{{ number_format((float) $payment->amount, 2) }} ريال
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600 max-w-xs">{{ $payment->reason ?: $payment->payment_reference }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500">{{ $payment->performed_by_name ?? $payment->performedBy?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-400 whitespace-nowrap">{{ $payment->created_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-3">
                                    @if ($payment->receipt_path)
                                        <a href="{{ route('manager.commission-receipt.show', $payment->id) }}" target="_blank" class="text-primary-600 hover:underline text-xs font-medium">عرض</a>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">لا توجد حركات مالية بعد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $payments->links() }}</div>
    </div>
</div>
