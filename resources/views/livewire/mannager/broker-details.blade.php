<div>
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-start justify-between">
                <div>
                    <a href="{{ route('manager.brokers') }}" class="text-xs text-gray-400 hover:text-gray-600">← كل الوسطاء</a>
                    <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $broker->name }}</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $broker->reference_number }} · {{ $broker->statusLabel() }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('manager.broker-statement', $broker->id) }}"
                        class="px-4 py-2 text-sm font-medium text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100">
                        كشف الحساب
                    </a>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="mt-4 p-4 text-sm text-green-800 rounded-lg bg-green-50">{{ session('message') }}</div>
            @endif
        </div>
    </div>

    <div class="bg-white min-h-screen p-2 sm:p-4">
        <!-- Broker Info Card -->
        <div class="bg-gray-50 border border-gray-100 rounded-xl p-6 mb-6">
            <h2 class="text-sm font-bold text-gray-900 mb-4">بيانات الوسيط</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <p class="text-xs text-gray-500">الاسم</p>
                    <p class="text-sm font-medium text-gray-900">{{ $broker->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">البريد الإلكتروني</p>
                    <p class="text-sm font-medium text-gray-900">{{ $broker->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">الرقم المرجعي</p>
                    <p class="text-sm font-medium text-gray-900">{{ $broker->reference_number }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">الهاتف (واتساب)</p>
                    <p class="text-sm font-medium text-gray-900">{{ $broker->whatsapp }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">المدينة</p>
                    <p class="text-sm font-medium text-gray-900">{{ $broker->city ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">النوع</p>
                    <p class="text-sm font-medium text-gray-900">{{ $broker->brokerTypeLabel() }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">الحالة</p>
                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full"
                        style="color: {{ $broker->statusColor() }}; background-color: {{ $broker->statusColor() }}20;">
                        {{ $broker->statusLabel() }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-500">الرقم الوطني</p>
                    <p class="text-sm font-medium text-gray-900">{{ $broker->national_id ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">الآيبان</p>
                    <p class="text-sm font-medium text-gray-900">{{ $broker->iban ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">حالة التوظيف</p>
                    <p class="text-sm font-medium text-gray-900">{{ $broker->employmentStatusLabel() }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">كيف عرفنا بك</p>
                    <p class="text-sm font-medium text-gray-900">{{ $broker->heardAboutUsLabel() }}</p>
                </div>
                @if ($broker->approved_at)
                    <div>
                        <p class="text-xs text-gray-500">تاريخ الاعتماد</p>
                        <p class="text-sm font-medium text-gray-900">{{ $broker->approved_at->format('Y-m-d H:i') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                <p class="text-xs text-gray-500 font-semibold">إجمالي العملاء</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalOrders) }}</p>
            </div>
            <div class="bg-green-50 border border-green-100 rounded-xl p-4">
                <p class="text-xs text-green-700 font-semibold">مكتمل</p>
                <p class="text-2xl font-bold text-green-800 mt-1">{{ number_format($completedOrders) }}</p>
            </div>
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                <p class="text-xs text-blue-700 font-semibold">نشط</p>
                <p class="text-2xl font-bold text-blue-800 mt-1">{{ number_format($activeOrders) }}</p>
            </div>
            <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4">
                <p class="text-xs text-yellow-700 font-semibold">جديد</p>
                <p class="text-2xl font-bold text-yellow-800 mt-1">{{ number_format($pendingOrders) }}</p>
            </div>
        </div>

        <!-- Clients/Orders Section -->
        <div class="mb-4">
            <h2 class="text-sm font-bold text-gray-900 mb-3">العملاء المضافين</h2>

            <!-- Filter -->
            <select wire:model.live="statusFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 pr-10">
                <option value="">كل الحالات</option>
                @foreach ($statusLabels as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right text-gray-700">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 font-semibold">اسم العميل</th>
                            <th class="px-4 py-3 font-semibold">الهاتف</th>
                            <th class="px-4 py-3 font-semibold">المشروع</th>
                            <th class="px-4 py-3 font-semibold">الوحدة</th>
                            <th class="px-4 py-3 font-semibold">نوع الشراء</th>
                            <th class="px-4 py-3 font-semibold">الحالة</th>
                            <th class="px-4 py-3 font-semibold">التاريخ</th>
                            <th class="px-4 py-3 font-semibold">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50" wire:key="order-{{ $order->id }}">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $order->name }}</div>
                                    @if ($order->email)
                                        <div class="text-xs text-gray-400">{{ $order->email }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3" dir="ltr">{{ $order->phone }}</td>
                                <td class="px-4 py-3">{{ $order->project?->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $order->unit?->title ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs">{{ $order->purchaseTypeLabel() }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full"
                                        style="color: {{ $order->statusColor() }}; background-color: {{ $order->statusColor() }}20;">
                                        {{ $order->statusLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-400 whitespace-nowrap">{{ $order->created_at->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('manager.order-details', $order->id) }}"
                                        class="text-primary-600 hover:text-primary-800 text-xs font-medium">عرض ←</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">لا يوجد عملاء مضافين من هذا الوسيط.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $orders->links() }}</div>
    </div>
</div>
