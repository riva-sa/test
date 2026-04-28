<div class="min-h-screen bg-gray-50/50 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-10">
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-gray-500" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 rtl:space-x-reverse">
                <li><a href="{{ route('manager.dashboard') }}" class="hover:text-gray-900 transition-colors">الرئيسية</a></li>
                <li class="flex items-center"><svg class="h-4 w-4 mx-1" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg></li>
                <li><a href="{{ route('manager.customerlist') }}" class="hover:text-gray-900 transition-colors">العملاء</a></li>
                <li class="flex items-center"><svg class="h-4 w-4 mx-1" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg></li>
                <li class="font-medium text-gray-900">ملف العميل</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
            <!-- Left Column: Profile -->
            <div class="lg:col-span-1 space-y-6">
                <div class="rounded-lg border border-gray-200 bg-white text-gray-950 shadow-sm">
                    <div class="p-6 text-center space-y-4">
                        <div class="mx-auto h-20 w-20 rounded-full bg-gray-900 flex items-center justify-center text-white text-2xl font-bold shadow-sm">
                            {{ substr($customer->name, 0, 1) }}
                        </div>
                        <div class="space-y-1">
                            <h2 class="text-xl font-semibold tracking-tight">{{ $customer->name }}</h2>
                            <p class="text-xs text-gray-500">{{ $customer->email }}</p>
                        </div>
                        <div class="flex justify-center gap-2 pt-2">
                            <a href="tel:{{ $customer->phone }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-gray-100 h-9 w-9 border border-gray-200 bg-white" title="اتصال">
                                <i class="fas fa-phone-alt"></i>
                            </a>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone) }}" target="_blank" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-gray-100 h-9 w-9 border border-gray-200 bg-white" title="واتساب">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 p-6 space-y-4 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-500">رقم الهاتف</span>
                            <span class="font-mono text-gray-900" dir="ltr">{{ $customer->phone }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">تاريخ الانضمام</span>
                            <span class="font-medium text-gray-900">{{ $customer->first_order_date->format('Y/m/d') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">آخر ظهور</span>
                            <span class="font-medium text-gray-900">{{ $customer->last_order_date->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Stats Overview -->
                <div class="grid grid-cols-1 gap-4">
                    <div class="rounded-lg border border-gray-200 bg-white p-4 space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">إجمالي الطلبات</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $customer->total_orders }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-4 space-y-2">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">آخر حالة</p>
                        <span class="inline-flex items-center rounded-full border border-gray-200 px-2.5 py-0.5 text-xs font-semibold bg-gray-50 text-gray-900">
                            {{ $customer->latest_status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Details & History -->
            <div class="lg:col-span-3 space-y-8">
                <!-- Orders Table -->
                <div class="rounded-lg border border-gray-200 bg-white text-gray-950 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="text-base font-semibold tracking-tight text-gray-900">سجل الطلبات</h3>
                        <span class="text-[10px] font-bold text-gray-500 px-2 py-0.5 rounded-full border border-gray-200 bg-white">
                            {{ $orders->total() }} طلب
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50/80 border-b border-gray-200">
                                <tr>
                                    <th class="h-10 px-6 text-right align-middle font-medium text-gray-500 text-xs">الطلب</th>
                                    <th class="h-10 px-6 text-right align-middle font-medium text-gray-500 text-xs">المشروع / الوحدة</th>
                                    <th class="h-10 px-6 text-right align-middle font-medium text-gray-500 text-xs">الحالة</th>
                                    <th class="h-10 px-6 text-right align-middle font-medium text-gray-500 text-xs">بواسطة</th>
                                    <th class="h-10 px-6 text-center align-middle font-medium text-gray-500 text-xs">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($orders as $order)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-gray-900">#{{ $order->id }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $order->created_at->format('Y/m/d') }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900">{{ $order->project->name ?? '—' }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->unit->title ?? 'طلب عام' }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center rounded-full border border-transparent px-2 py-0.5 text-[10px] font-bold bg-gray-100 text-gray-900 uppercase">
                                                {{ $order->statusLabel() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-xs text-gray-500">
                                            {{ $order->assignedSalesUser->name ?? 'غير معين' }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('manager.order-details', $order->id) }}" class="inline-flex items-center justify-center rounded-md text-xs font-medium border border-gray-200 bg-white hover:bg-gray-100 h-8 px-3">
                                                التفاصيل
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm font-medium">
                                            لا توجد طلبات سابقة لهذا العميل
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($orders->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/30">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>

                <!-- Additional Info -->
                <div class="rounded-lg border border-gray-200 bg-white text-gray-950 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                        <h3 class="text-base font-semibold tracking-tight text-gray-900">بيانات إضافية (أحدث طلب)</h3>
                    </div>
                    @php $latest = $orders->first(); @endphp
                    @if($latest)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-px bg-gray-200">
                        <div class="bg-white p-6 space-y-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">نوع الشراء</p>
                            <p class="text-sm font-medium text-gray-900">{{ $latest->PurchaseType ?? '—' }}</p>
                        </div>
                        <div class="bg-white p-6 space-y-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">الغرض من الشراء</p>
                            <p class="text-sm font-medium text-gray-900">{{ $latest->PurchasePurpose ?? '—' }}</p>
                        </div>
                        <div class="bg-white p-6 space-y-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">البنك</p>
                            <p class="text-sm font-medium text-gray-900">{{ $latest->bank_name ?? '—' }}</p>
                        </div>
                        <div class="bg-white p-6 space-y-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">موظف البنك</p>
                            <p class="text-sm font-medium text-gray-900">{{ $latest->bank_employee_name ?? '—' }}</p>
                        </div>
                        <div class="bg-white p-6 space-y-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">المصدر</p>
                            <div class="flex items-center text-sm font-medium text-gray-900">
                                @php $marketing = $latest->formattedMarketingSource(); @endphp
                                <i class="{{ $marketing['icon'] }} ml-2 text-gray-400"></i>
                                <span>{{ $marketing['label'] }}</span>
                            </div>
                        </div>
                        <div class="bg-white p-6 space-y-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">الحملة</p>
                            <p class="text-sm font-medium text-gray-900">{{ $latest->campaign_name ?? 'مباشر' }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
