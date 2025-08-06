<div>
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                    إدارة صلاحيات الطلب
                    <span class="text-blue-600">#{{ $order->id }}</span>
                </h1>
                <a href="{{ route('manager.order-details', $order->id) }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 bg-gray-50 hover:bg-gray-100 transition">
                    <!-- Icon for Back -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    العودة إلى الطلب
                </a>
            </div>
        </div>
    </div>

    <div class="p-6">
        <!-- Success Message -->
        @if (session()->has('success'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-6 animate-fade-in-down flex items-center">
                <!-- Icon for success message -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ml-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Order Info -->
        <div class="bg-white border border-gray-200 p-6 rounded-lg mb-8 shadow-sm">
            <h3 class="text-sm font-medium text-gray-500 mb-2">بيانات الطلب</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-700">
                <div>
                    <p class="font-medium text-gray-500 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        العميل
                    </p>
                    <p class="mt-1 font-semibold">{{ $order->name }}</p>
                </div>
                <div>
                    <p class="font-medium text-gray-500 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        المشروع
                    </p>
                    <p class="mt-1 font-semibold">{{ $order->project?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="font-medium text-gray-500 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                        </svg>
                        الوحدة
                    </p>
                    <p class="mt-1 font-semibold">{{ $order->unit?->title ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Grant Permission Form -->
        <div class="bg-white border border-gray-200 p-6 rounded-lg mb-8 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800 mb-5 border-b pb-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ml-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                منح صلاحية جديدة
            </h3>

            <form wire:submit.prevent="grantPermission" class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- User -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        المستخدم
                    </label>
                    <select wire:model.defer="user_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                        <option value="">اختر المستخدم</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }}
                                @if($user->hasRole('sales_manager')) (مدير مبيعات)
                                @elseif($user->hasRole('sales')) (مندوب مبيعات)
                                @elseif($user->hasRole('follow_up')) (متابعة)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('user_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Permission Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        نوع الصلاحية
                    </label>
                    <select wire:model.defer="permission_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                        <option value="">اختر نوع الصلاحية</option>
                        {{-- <option value="view">عرض فقط</option>
                        <option value="edit">تعديل</option> --}}
                        <option value="manage">إدارة كاملة</option>
                    </select>
                    @error('permission_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Expiry -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        تاريخ الانتهاء (اختياري)
                    </label>
                    <input type="date" wire:model.defer="expires_at" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full  p-2.5">
                    @error('expires_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Submit & Reset -->
                <div class="md:col-span-3 flex flex-wrap gap-3 mt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md shadow-sm transition flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        منح الصلاحية
                    </button>
                    <button type="reset" wire:click="$set('user_id', null)" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-md shadow-sm transition flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        إعادة تعيين
                    </button>
                </div>
            </form>
        </div>

        <!-- Current Permissions Table -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ml-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    الصلاحيات الحالية
                </h3>
                @if ($permissions->isNotEmpty())
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">{{ $permissions->count() }} صلاحية</span>
                @endif
            </div>

            @if ($permissions->isEmpty())
                <div class="p-6 text-center text-gray-500 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ml-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    لا توجد صلاحيات ممنوحة لهذا الطلب بعد.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المستخدم</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الصلاحية</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">منح بواسطة</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ المنح</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الانتهاء</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($permissions as $permission)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="font-medium">{{ $permission->user->name }}</div>
                                            @if($permission->user->hasRole('sales_manager'))
                                                <span class="mr-2 text-xs bg-blue-500 text-white rounded-full px-2 py-0.5">مدير مبيعات</span>
                                            @elseif($permission->user->hasRole('sales'))
                                                <span class="mr-2 text-xs bg-green-500 text-white rounded-full px-2 py-0.5">مندوب مبيعات</span>
                                            @elseif($permission->user->hasRole('follow_up'))
                                                <span class="mr-2 text-xs bg-yellow-500 text-white rounded-full px-2 py-0.5">متابعة</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @switch($permission->permission_type)
                                            @case('view') عرض فقط @break
                                            @case('edit') تعديل @break
                                            @case('manage') إدارة كاملة @break
                                            @default غير معروف
                                        @endswitch
                                    </td>
                                    <td class="px-4 py-3">{{ $permission->grantedBy->name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $permission->created_at->format('Y-m-d') }}</td>
                                    <td class="px-4 py-3">{{ $permission->expires_at ? \Carbon\Carbon::parse($permission->expires_at)->format('Y-m-d') : '-' }}</td>
                                    <td class="px-4 py-3">
                                        <button wire:click="revokePermission({{ $permission->id }})"
                                                class="text-red-600 hover:text-red-900 font-medium transition flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            إلغاء
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>