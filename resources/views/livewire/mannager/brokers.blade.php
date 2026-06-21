<div>
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">الوسطاء</h1>
                <p class="text-sm text-gray-500 mt-1">أداء الوسطاء وعمولاتهم المستحقة والمدفوعة</p>
            </div>
        </div>
    </div>

    <div class="bg-white min-h-screen p-2 sm:p-4">
        <!-- Filters -->
        <div class="flex flex-col md:flex-row gap-3 mb-4">
            <input type="text" wire:model.live.debounce.400ms="search"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full md:w-1/3 p-2.5"
                placeholder="ابحث باسم الوسيط أو بريده أو رقمه المرجعي...">
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
                            <th class="px-4 py-3 font-semibold">الوسيط</th>
                            <th class="px-4 py-3 font-semibold">الحالة</th>
                            <th class="px-4 py-3 font-semibold">صفقات مكتملة</th>
                            <th class="px-4 py-3 font-semibold">إجمالي مكتسب</th>
                            <th class="px-4 py-3 font-semibold">مدفوع</th>
                            <th class="px-4 py-3 font-semibold">مستحق</th>
                            <th class="px-4 py-3 font-semibold"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($brokers as $broker)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50" wire:key="broker-{{ $broker->id }}">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $broker->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $broker->reference_number }} · {{ $broker->whatsapp }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full"
                                        style="color: {{ $broker->statusColor() }}; background-color: {{ $broker->statusColor() }}20;">
                                        {{ $broker->statusLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ number_format($broker->sold_deals_count) }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-900 whitespace-nowrap">{{ number_format((float) $broker->earned_total, 2) }}</td>
                                <td class="px-4 py-3 text-green-700 whitespace-nowrap">{{ number_format((float) $broker->paid_total, 2) }}</td>
                                <td class="px-4 py-3 font-semibold text-yellow-700 whitespace-nowrap">{{ number_format((float) $broker->outstanding_total, 2) }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('manager.broker-statement', $broker->id) }}"
                                        class="text-primary-600 hover:text-primary-800 text-xs font-medium">كشف الحساب ←</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">لا يوجد وسطاء.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $brokers->links() }}</div>
    </div>
</div>
