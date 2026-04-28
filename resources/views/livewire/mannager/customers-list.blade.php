<div class="min-h-screen py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="space-y-1">
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900">إدارة العملاء</h1>
                <p class="text-sm text-gray-500">عرض وإدارة جميع العملاء وطلباتهم في النظام.</p>
            </div>
            
            <div class="relative max-w-sm w-full">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text" wire:model.live="search" 
                    class="flex h-10 w-full rounded-md border border-gray-200 bg-white pr-10 pl-3 py-2 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-colors shadow-sm" 
                    placeholder="بحث باسم العميل أو رقم الهاتف...">
            </div>
        </div>

        <!-- Customers Table Card -->
        <div class="rounded-lg border border-gray-200 bg-white text-gray-950 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="h-12 px-6 text-right align-middle font-medium text-gray-500">العميل</th>
                            <th class="h-12 px-6 text-right align-middle font-medium text-gray-500">رقم الهاتف</th>
                            <th class="h-12 px-6 text-center align-middle font-medium text-gray-500">عدد الطلبات</th>
                            <th class="h-12 px-6 text-right align-middle font-medium text-gray-500">آخر طلب</th>
                            <th class="h-12 px-6 text-center align-middle font-medium text-gray-500">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($customers as $customer)
                            <tr class="transition-colors hover:bg-gray-50">
                                <td class="px-6 py-4 align-middle">
                                    <div class="flex items-center">
                                        <div class="h-9 w-9 flex-shrink-0 rounded-full bg-gray-100 flex items-center justify-center text-gray-900 font-semibold border border-gray-200 text-xs">
                                            {{ substr($customer->name, 0, 1) }}
                                        </div>
                                        <div class="mr-3">
                                            <div class="font-medium text-gray-900">{{ $customer->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $customer->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 align-middle">
                                    <span class="font-mono text-xs text-gray-700" dir="ltr">{{ $customer->phone }}</span>
                                </td>
                                <td class="px-6 py-4 align-middle text-center">
                                    <span class="inline-flex items-center rounded-full border border-transparent bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-900">
                                        {{ $customer->orders_count }} طلبات
                                    </span>
                                </td>
                                <td class="px-6 py-4 align-middle text-gray-500 text-xs">
                                    {{ \Carbon\Carbon::parse($customer->last_order_at)->format('Y/m/d') }}
                                </td>
                                <td class="px-6 py-4 align-middle text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('manager.customer-profile', $customer->phone) }}" 
                                            class="inline-flex items-center justify-center rounded-md text-xs font-medium transition-colors border border-gray-200 bg-white hover:bg-gray-100 h-8 px-3">
                                            عرض الملف
                                        </a>
                                        
                                        <div class="flex items-center gap-1">
                                            <a href="tel:{{ $customer->phone }}" class="inline-flex items-center justify-center rounded-md text-sm transition-colors hover:bg-gray-100 h-8 w-8 text-gray-400" title="اتصال">
                                                <i class="fas fa-phone-alt h-3 w-3"></i>
                                            </a>
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone) }}" target="_blank" class="inline-flex items-center justify-center rounded-md text-sm transition-colors hover:bg-gray-100 h-8 w-8 text-gray-400" title="واتساب">
                                                <i class="fab fa-whatsapp h-3 w-3"></i>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center align-middle">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="h-8 w-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-400">لم يتم العثور على أي عملاء</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($customers->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>