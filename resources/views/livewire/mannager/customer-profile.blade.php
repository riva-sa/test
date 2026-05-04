<div class="min-h-screen bg-white py-10 px-4 sm:px-6 lg:px-8 text-zinc-900" dir="rtl">
    <div class="max-w-7xl mx-auto space-y-10">
        
        <!-- Header Section: Customer Profile -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 pb-6 border-b border-zinc-100">
            <div class="space-y-1">
                <h1 class="text-3xl font-bold tracking-tight text-zinc-900">{{ $customer->name }}</h1>
                <div class="flex items-center gap-4 text-sm text-zinc-500">
                    <span class="flex items-center gap-1.5" dir="ltr">
                        <i class="fas fa-phone text-zinc-400"></i> {{ $customer->phone }}
                    </span>
                    <span class="text-zinc-300">|</span>
                    <span class="flex items-center gap-1.5">
                        <i class="far fa-envelope text-zinc-400"></i> {{ $customer->email }}
                    </span>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="tel:{{ $customer->phone }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-white transition-colors border border-zinc-200 bg-white hover:bg-zinc-100 hover:text-zinc-900 h-10 px-4 py-2">
                    <i class="fas fa-phone-alt ml-2 text-xs"></i> اتصال
                </a>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone) }}" target="_blank" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-white transition-colors bg-zinc-900 text-zinc-50 hover:bg-zinc-900/90 h-10 px-4 py-2 shadow">
                    <i class="fab fa-whatsapp ml-2 text-sm"></i> واتساب
                </a>
            </div>
        </div>

        <!-- Quick Stats Cards (Shadcn Stats Style) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between space-y-0 pb-2">
                    <p class="text-xs font-medium tracking-tight text-zinc-500">إجمالي الطلبات</p>
                    <i class="fas fa-shopping-cart text-[10px] text-zinc-400"></i>
                </div>
                <div class="text-2xl font-bold">{{ $customer->total_orders }}</div>
            </div>
            
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between space-y-0 pb-2">
                    <p class="text-xs font-medium tracking-tight text-zinc-500">حالة العميل</p>
                    <i class="fas fa-signal text-[10px] text-zinc-400"></i>
                </div>
                <div class="inline-flex items-center rounded-full border border-transparent px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-zinc-950 focus:ring-offset-2" 
                     style="background-color: {{ $customer->latest_status_color }}10; color: {{ $customer->latest_status_color }}">
                    {{ $customer->latest_status }}
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between space-y-0 pb-2">
                    <p class="text-xs font-medium tracking-tight text-zinc-500">تاريخ الانضمام</p>
                    <i class="far fa-calendar text-[10px] text-zinc-400"></i>
                </div>
                <div class="text-lg font-semibold">{{ $customer->first_order_date->format('Y/m/d') }}</div>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between space-y-0 pb-2">
                    <p class="text-xs font-medium tracking-tight text-zinc-500">آخر ظهور</p>
                    <i class="far fa-clock text-[10px] text-zinc-400"></i>
                </div>
                <div class="text-lg font-semibold">{{ $customer->last_order_date->diffForHumans() }}</div>
            </div>
        </div>

        <!-- Orders Table Section -->
        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm overflow-hidden">
            <div class="flex flex-col space-y-1.5 p-6 border-b border-zinc-100">
                <h3 class="font-semibold leading-none tracking-tight">سجل الطلبات</h3>
                <p class="text-sm text-zinc-500 font-normal">عرض كافة التفاصيل والتحركات الخاصة بطلبات العميل.</p>
            </div>
            <div class="p-0 overflow-x-auto">
                <table class="w-full caption-bottom text-sm">
                    <thead class="bg-zinc-50/50 border-b border-zinc-200">
                        <tr class="transition-colors hover:bg-zinc-100/50 data-[state=selected]:bg-zinc-100">
                            <th class="h-12 px-4 text-right align-middle font-medium text-zinc-500">رقم الطلب</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-zinc-500">المشروع</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-zinc-500">الحالة</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-zinc-500">مسؤول البيع</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-zinc-500">المصدر</th>
                            <th class="h-12 px-4 text-center align-middle font-medium text-zinc-500">ملاحظات</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-zinc-500">التحديث</th>
                            <th class="h-12 px-4 text-center align-middle font-medium text-zinc-500"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse ($orders as $order)
                        <tr class="transition-colors hover:bg-zinc-50/50">
                            <td class="p-4 align-middle font-bold tracking-tighter">#{{ $order->id }}</td>
                            <td class="p-4 align-middle">
                                <div class="font-medium text-zinc-900">{{ $order->project->name ?? '—' }}</div>
                                <div class="text-[11px] text-zinc-400">{{ $order->unit->title ?? 'طلب عام' }}</div>
                            </td>
                            <td class="p-4 align-middle">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium border" 
                                      style="border-color: {{ $order->statusColor() }}40; color: {{ $order->statusColor() }}">
                                    {{ $order->statusLabel() }}
                                </span>
                            </td>
                            <td class="p-4 align-middle text-zinc-600 font-medium">
                                {{ $order->assignedSalesUser->name ?? 'غير معين' }}
                            </td>
                            <td class="p-4 align-middle">
                                <div class="text-xs">{{ $order->orderSourceLabel() }}</div>
                                <div class="text-[10px] text-zinc-400 italic truncate max-w-[120px]">{{ $order->ad_set }}</div>
                            </td>
                            <td class="p-4 align-middle text-center">
                                <span class="bg-zinc-100 text-zinc-600 px-2 py-0.5 rounded text-[10px] font-bold">
                                    {{ $order->notes_count }}
                                </span>
                            </td>
                            <td class="p-4 align-middle text-zinc-400 text-xs">
                                {{ $order->updated_at->format('Y/m/d') }}
                            </td>
                            <td class="p-4 align-middle text-center">
                                <a href="{{ route('manager.order-details', $order->id) }}" class="inline-flex items-center justify-center rounded-md text-xs font-medium border border-zinc-200 bg-white hover:bg-zinc-100 h-8 px-3">
                                    التفاصيل
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-10 text-center text-zinc-400 italic">لا توجد سجلات حالية.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($orders->hasPages())
                <div class="p-4 border-t border-zinc-100">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>

        <!-- Additional Information Footer -->
        @php $latest = $orders->first(); @endphp
        @if($latest)
        <div class="rounded-xl border border-zinc-200 bg-zinc-50/30 p-6">
            <h4 class="text-sm font-semibold mb-4 text-zinc-900 uppercase tracking-widest text-[11px]">بيانات تقنية (أحدث طلب)</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8">
                <div class="space-y-1">
                    <p class="text-[10px] text-zinc-500 font-medium uppercase">نوع الشراء</p>
                    <p class="text-sm font-semibold">{{ $latest->purchaseTypeLabel() }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] text-zinc-500 font-medium uppercase">الغرض</p>
                    <p class="text-sm font-semibold">{{ $latest->purchasePurposeLabel() }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] text-zinc-500 font-medium uppercase">البنك</p>
                    <p class="text-sm font-semibold">{{ $latest->bank_name ?? '—' }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] text-zinc-500 font-medium uppercase">موظف البنك</p>
                    <p class="text-sm font-semibold">{{ $latest->bank_employee_name ?? '—' }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] text-zinc-500 font-medium uppercase">المصدر التسويقي</p>
                    <div class="flex items-center text-sm font-semibold">
                        @php $marketing = $latest->formattedMarketingSource(); @endphp
                        <i class="{{ $marketing['icon'] }} ml-2 text-zinc-400"></i>
                        <span>{{ $marketing['label'] }}</span>
                    </div>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] text-zinc-500 font-medium uppercase">اسم الحملة</p>
                    <p class="text-sm font-semibold truncate">{{ $latest->ad_set ?? 'مباشر' }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>