<div>
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                    إدارة العملاء
                </h1>
            </div>
        </div>
    </div>
    <div class="px-4 py-6 sm:px-6">
        @if($selectedCustomer)
            <!-- عرض طلبات عميل معين -->
            <div class="mb-6 flex items-center">
                <button wire:click="resetCustomer" class="flex items-center text-primary-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    رجوع لقائمة العملاء
                </button>
                <h2 class="text-xl font-bold text-gray-900 mr-3">
                    طلبات العميل: {{ $customerPhone }}
                </h2>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المشروع</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوحدة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($customerOrders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $order->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $order->project->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $order->unit->title ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $order->created_at->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $order->status == 0 ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $order->status == 1 ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $order->status == 2 ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $order->status == 3 ? 'bg-gray-100 text-gray-800' : '' }}">
                                            @if($order->status == 0) جديد
                                            @elseif($order->status == 1) طلب مفتوح
                                            @elseif($order->status == 2) معاملات بيعية
                                            @elseif($order->status == 3) مغلق
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('manager.order-details', $order->id) }}" class="text-primary-600 hover:text-primary-900 me-5">عرض</a>
                                        <a href="tel:{{ $order->phone }}" class="text-blue-600 hover:text-green-900 me-5">اتصال</a>
                                        <a href="https://wa.me/{{ $order->phone }}" target="_blank" class="text-green-600 hover:text-green-900">واتساب</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center">لا توجد طلبات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
                    {{ $customerOrders->links() }}
                </div>
            </div>
        @else
            <!-- عرض قائمة العملاء -->
            <div class="mb-6">
                <div class="mt-4 flex flex-col md:flex-row justify-between">
                    <div class="w-full md:w-1/3">
                        <input type="text" wire:model.live="search" placeholder="بحث عن عميل..."
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 p-3 px-4">
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البريد الإلكتروني</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الهاتف</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عدد الطلبات</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($customers as $customer)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $customer->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $customer->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $customer->phone }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $customer->orders_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="selectCustomer('{{ $customer->phone }}')"
                                            class="text-primary-600 hover:text-primary-900">
                                            عرض الطلبات
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center">لا توجد عملاء</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
                    {{ $customers->links() }}
                </div>
            </div>
        @endif

    </div>
</div>