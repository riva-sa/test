<div>
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                    الأرقام المحظورة
                </h1>
                <p class="text-sm text-gray-500 mt-1">إدارة الأرقام التي لا يمكنها تقديم اهتمامات</p>
            </div>
            
            @if (session()->has('message'))
                <div class="mt-4 p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                    {{ session('message') }}
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white min-h-screen p-2 sm:p-4">
        <!-- Add New Number Card -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">حظر رقم جديد</h2>

            <form wire:submit.prevent="addNumber" class="grid grid-cols-1 md:grid-cols-3 gap-5 items-end">
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف (مع رمز الدولة)</label>
                    <input type="text" wire:model="phone" id="phone" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="+9665xxxxxxxx">
                    @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">السبب (اختياري)</label>
                    <input type="text" wire:model="reason" id="reason" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="مثال: سبام">
                    @error('reason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <button type="submit" class="w-full bg-red-600 text-white px-4 py-2.5 rounded-lg hover:bg-red-700 transition-colors font-medium">
                        حظر الرقم
                    </button>
                </div>
            </form>
        </div>

        <!-- Search and List Card -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-semibold text-gray-700">قائمة الأرقام المحظورة</h3>
                <div class="relative w-64">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input type="text" wire:model.live="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pr-10 p-2" placeholder="بحث بالرقم...">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم الهاتف</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السبب</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الحظر</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($blockedNumbers as $number)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $number->phone }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $number->reason ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $number->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button wire:click="deleteNumber({{ $number->id }})" wire:confirm="هل أنت متأكد من إلغاء حظر هذا الرقم؟" class="text-red-600 hover:text-red-900">
                                        إلغاء الحظر
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">لا توجد أرقام محظورة حالياً.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
                {{ $blockedNumbers->links() }}
            </div>
        </div>
    </div>
</div>
