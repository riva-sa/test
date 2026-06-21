<div>
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">مستحقات عمولات الوسطاء</h1>
                <p class="text-sm text-gray-500 mt-1">اعتماد ودفع العمولات المستحقة للوسطاء عن الصفقات المكتملة</p>
            </div>

            @if (session()->has('message'))
                <div class="mt-4 p-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">{{ session('message') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="mt-4 p-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">{{ session('error') }}</div>
            @endif
        </div>
    </div>

    <div class="bg-white min-h-screen p-2 sm:p-4">
        <!-- Summary cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4">
                <p class="text-xs text-yellow-700 font-semibold">المستحق (غير مدفوع)</p>
                <p class="text-2xl font-bold text-yellow-800 mt-1">{{ number_format($totalOutstanding, 2) }} <span class="text-sm">ريال</span></p>
            </div>
            <div class="bg-green-50 border border-green-100 rounded-xl p-4">
                <p class="text-xs text-green-700 font-semibold">إجمالي المدفوع</p>
                <p class="text-2xl font-bold text-green-800 mt-1">{{ number_format($totalPaid, 2) }} <span class="text-sm">ريال</span></p>
            </div>
            <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                <p class="text-xs text-gray-500 font-semibold">عمولات معلّقة بانتظار الاعتماد</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($pendingCount) }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-col md:flex-row gap-3 mb-4">
            <input type="text" wire:model.live.debounce.400ms="search"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full md:w-1/3 p-2.5"
                placeholder="ابحث باسم الوسيط أو رقمه المرجعي...">
            <select wire:model.live="statusFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 pr-10">
                <option value="">كل الحالات</option>
                @foreach ($statusLabels as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            <select wire:model.live="projectFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 pr-10">
                <option value="">كل المشاريع</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
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
                            <th class="px-4 py-3 font-semibold">المشروع / الوحدة</th>
                            <th class="px-4 py-3 font-semibold">قيمة العمولة</th>
                            <th class="px-4 py-3 font-semibold">الحالة</th>
                            <th class="px-4 py-3 font-semibold">التاريخ</th>
                            <th class="px-4 py-3 font-semibold">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($commissions as $commission)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50" wire:key="commission-{{ $commission->id }}">
                                <td class="px-4 py-3">
                                    <a href="{{ route('manager.broker-statement', $commission->broker_id) }}" class="font-medium text-primary-600 hover:underline">
                                        {{ $commission->broker?->name ?? '—' }}
                                    </a>
                                    <div class="text-xs text-gray-400">{{ $commission->broker?->reference_number }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-gray-900">{{ $commission->project?->name ?? '—' }}</div>
                                    <div class="text-xs text-gray-400">{{ $commission->unit?->title ?? 'طلب #'.$commission->unit_order_id }}</div>
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-900 whitespace-nowrap">{{ number_format((float) $commission->commission_amount, 2) }} ريال</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full"
                                        style="color: {{ $commission->statusColor() }}; background-color: {{ $commission->statusColor() }}20;">
                                        {{ $commission->statusLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">{{ $commission->created_at?->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        @if ($commission->isPending())
                                            <button wire:click="approveCommission({{ $commission->id }})"
                                                class="text-blue-600 hover:text-blue-800 text-xs font-medium">اعتماد</button>
                                            <button wire:click="openVoidModal({{ $commission->id }})"
                                                class="text-red-500 hover:text-red-700 text-xs font-medium">إلغاء</button>
                                        @elseif ($commission->isApproved())
                                            <button wire:click="openPayModal({{ $commission->id }})"
                                                class="text-green-600 hover:text-green-800 text-xs font-medium">تسجيل دفع</button>
                                            <button wire:click="openVoidModal({{ $commission->id }})"
                                                class="text-red-500 hover:text-red-700 text-xs font-medium">إلغاء</button>
                                        @elseif ($commission->isPaid())
                                            <span class="text-xs text-gray-400">{{ $commission->payment_reference ? 'مرجع: '.$commission->payment_reference : 'مدفوعة' }}</span>
                                            @can('reverse-broker-commissions')
                                                <button wire:click="openReverseModal({{ $commission->id }})"
                                                    class="text-red-500 hover:text-red-700 text-xs font-medium">عكس الدفعة</button>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">لا توجد عمولات مطابقة.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $commissions->links() }}</div>
    </div>

    @include('livewire.mannager.partials.commission-payment-modals')
</div>
