<div>
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                    إدارة الطلبات
                </h1>
                <p class="text-sm text-gray-500 mt-1">عرض وتتبع جميع طلبات العملاء</p>

            </div>
        </div>
    </div>
    <!-- Header Section -->
    <div class="bg-gray-50 min-h-screen p-4 sm:p-6">
        <!-- Filters Card -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">تصفية النتائج</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
                <!-- Search Field -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">بحث</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <input type="text" wire:model.live="search" id="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pr-10 p-2.5" placeholder="ابحث بالاسم أو الهاتف...">
                    </div>
                </div>

                @if (auth()->user()->hasRole('sales_manager') || auth()->user()->hasRole('follow_up'))
                <!-- Sales Manager Filter -->
                <div>
                    <label for="salesManagerFilter" class="block text-sm font-medium text-gray-700 mb-1">مندوب المبيعات</label>
                    <select wire:model.live="salesManagerFilter" id="salesManagerFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                        <option value="">الكل</option>
                        @foreach ($salesManagers as $manager)
                            <option value="{{ $manager->id }}">{{ $manager->name }} - {{ $manager->phone }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Status Filter -->
                <div>
                    <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-1">حالة الطلب</label>
                    <select wire:model.live="statusFilter" id="statusFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                        <option value="">الكل</option>
                        @foreach($statusLabels as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Project Filter -->
                <div>
                    <label for="projectFilter" class="block text-sm font-medium text-gray-700 mb-1">المشروع</label>
                    <select wire:model.live="projectFilter" id="projectFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                        <option value="">الكل</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Delayed Filter -->
                <div>
                    <label for="delayedFilter" class="block text-sm font-medium text-gray-700 mb-1">الطلبات المتأخرة</label>
                    <select wire:model.live="delayedFilter" id="delayedFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                        <option value="">الكل</option>
                        <option value="1">عرض المتأخرة فقط</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Orders Table Card -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('name')">
                                <div class="flex items-center justify-end gap-1">
                                    <span>العميل</span>
                                    @if($sortField === 'name')
                                        @if($sortDirection === 'asc')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        @endif
                                    @endif
                                </div>
                            </th>

                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                الوحدة
                            </th>

                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                مصدر الطلب
                            </th>

                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('status')">
                                <div class="flex items-center justify-end gap-1">
                                    <span>الحالة</span>
                                    @if($sortField === 'status')
                                        @if($sortDirection === 'asc')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        @endif
                                    @endif
                                </div>
                            </th>

                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('created_at')">
                                <div class="flex items-center justify-end gap-1">
                                    <span>التاريخ</span>
                                    @if($sortField === 'created_at')
                                        @if($sortDirection === 'asc')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        @endif
                                    @endif
                                </div>
                            </th>

                            @if (auth()->user()->hasRole('sales_manager') || auth()->user()->hasRole('follow_up'))
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                مندوب المبيعات
                            </th>
                            @endif

                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                إجراءات
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($orders as $order)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <!-- Customer Info -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('manager.order-details', $order->id) }}" class="flex flex-col">
                                    <span class="font-medium text-gray-900">{{ $order->name }}</span>
                                    <div class="flex flex-col sm:flex-row sm:gap-2 text-sm text-gray-500 mt-1">
                                        <span>{{ $order->phone }}</span>
                                        @if($order->email)
                                        <span class="hidden sm:inline">•</span>
                                        <span>{{ $order->email }}</span>
                                        @endif
                                    </div>
                                </a>
                            </td>

                            <!-- Unit Info -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-medium">{{ $order->unit?->title ?? '-' }}</div>
                                <div class="text-sm text-gray-500">{{ $order->project?->name ?? '-' }}</div>
                            </td>

                            <!-- Order Source -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $user = auth()->user();
                                    $source = '';
                                    $sourceColor = 'bg-gray-100 text-gray-800';
                                    if ($order->user_id == $user->id) {
                                        $source = 'تم إنشاؤه بواسطتي';
                                        $sourceColor = 'bg-indigo-100 text-indigo-800';
                                    } elseif ($order->project && $order->project->sales_manager_id == $user->id) {
                                        if (!$user->hasRole('sales')) {
                                            $source = 'طلب تحت المتابعة';
                                            $sourceColor = 'bg-green-100 text-green-800';
                                        } else {
                                            $source = 'طلب تحت الإدارة';
                                            $sourceColor = 'bg-green-100 text-green-800';
                                        }
                                    } elseif ($user->hasOrderPermission($order->id, 'manage')) {
                                        $source = 'طلب تحت المتابعة';
                                        $sourceColor = 'bg-green-100 text-green-800';
                                    }
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $sourceColor }} ">
                                    {{ $source }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    @php
                                        $statusColors = [
                                            0 => 'bg-blue-100 text-blue-800',
                                            1 => 'bg-yellow-100 text-yellow-800',
                                            2 => 'bg-green-100 text-green-800',
                                            3 => 'bg-gray-100 text-gray-800',
                                            4 => 'bg-green-100 text-green-800',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$order->status] ?? $order->status }}
                                    </span>
                                    @if ($this->isDelayed($order))
                                    <span class="text-red-500 text-xs flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        متأخر
                                    </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Date -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->translatedFormat('d M Y - H:i') }}
                            </td>

                            <!-- Sales Manager -->
                            @if (auth()->user()->hasRole('sales_manager') || auth()->user()->hasRole('follow_up'))
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->project->salesManager->name ?? '-' }}
                            </td>
                            @endif

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('manager.order-details', $order->id) }}" class="text-primary-600 hover:text-primary-900 flex items-center gap-1" title="عرض التفاصيل">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    <a href="tel:{{ $order->phone }}" class="text-blue-600 hover:text-blue-900 flex items-center gap-1" title="اتصال">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </a>

                                    <a href="https://wa.me/{{ $order->phone }}" target="_blank" class="text-green-600 hover:text-green-900 flex items-center gap-1" title="واتساب">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                    </a>

                                    @if (!auth()->user()->hasRole('sales'))
                                    <a href="{{ route('manager.permissions', $order) }}" class="text-purple-600 hover:text-purple-900 flex items-center gap-1" title="الصلاحيات">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->hasRole('sales_manager') || auth()->user()->hasRole('follow_up') ? '7' : '6' }}" class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center justify-center py-8">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-700">لا توجد طلبات</h3>
                                    <p class="mt-1 text-sm text-gray-500">لم يتم العثور على أي طلبات تطابق معايير البحث.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>