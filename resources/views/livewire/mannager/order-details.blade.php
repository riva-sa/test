<div>
    <div class="px-4 py-6 sm:px-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-800">تفاصيل الطلب #{{ $order->id }}</h1>
                        <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">
                            @if($order->status == 0) جديد
                            @elseif($order->status == 1) طلب مفتوح
                            @elseif($order->status == 2) معاملات بيعية
                            @elseif($order->status == 3) مغلق
                            @elseif($order->status == 4) مكتمل
                            @endif
                        </span>
                        @if ($this->isDelayed())
                        <p class="text-red-600 text-sm mt-2">⚠️ الطلب متأخر (لم يتم التعديل منذ أكثر من 3 أيام)</p>
                    @endif
                    </div>

                    <div class="mt-3 flex flex-wrap gap-x-4 gap-y-2 text-sm text-gray-600">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="font-medium text-gray-700 mr-2 ml-2">أنشئ في:</span>
                            {{ $order->created_at->format('Y-m-d H:i') }}
                        </div>

                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <span class="font-medium text-gray-700 mr-2 ml-2">آخر تعديل:</span>
                            {{ $order->updated_at->format('Y-m-d H:i') }}
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('manager.orders') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <svg class="w-5 h-5 ml-1 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        رجوع للقائمة
                    </a>

                    <a href="{{ route('manager.create-order') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <svg class="w-5 h-5 ml-1 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        انشاء طلب
                    </a>
                </div>
            </div>

            @if (auth()->user()->hasRole('sales_manager') || auth()->user()->hasRole('follow_up'))
            <div class="mt-4 pt-4 border-t border-gray-100">
                <h3 class="text-sm font-medium text-gray-700 mb-2">فريق المبيعات</h3>
                <div class="flex flex-wrap gap-3">
                    @if($order->project->salesManager)
                    <div class="flex items-center">
                        <div class="relative">
                            <span class="inline-block h-8 w-8 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center font-medium">
                                {{ substr($order->project->salesManager->name, 0, 1) }}
                            </span>
                            <span class="absolute -bottom-1 -right-1 bg-blue-500 rounded-full p-1 border-2 border-white">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="mr-2">
                            <p class="text-sm font-medium text-gray-900">{{ $order->project->salesManager->name }}</p>
                            <span class="text-xs text-blue-600">مندوب المبيعات المسؤل</span>
                        </div>
                    </div>
                    @endif

                    @foreach ($permissions as $permission)
                    <div class="flex items-center">
                        <div class="relative">
                            <span class="inline-block h-8 w-8 rounded-full bg-gray-100 text-gray-800 flex items-center justify-center font-medium">
                                {{ substr($permission->user->name, 0, 1) }}
                            </span>
                            @if($permission->user->hasRole('sales_manager'))
                            <span class="absolute -bottom-1 -right-1 bg-blue-500 rounded-full p-1 border-2 border-white"></span>
                            @elseif($permission->user->hasRole('sales'))
                            <span class="absolute -bottom-1 -right-1 bg-green-500 rounded-full p-1 border-2 border-white"></span>
                            @elseif($permission->user->hasRole('follow_up'))
                            <span class="absolute -bottom-1 -right-1 bg-yellow-500 rounded-full p-1 border-2 border-white"></span>
                            @endif
                        </div>
                        <div class="mr-2">
                            <p class="text-sm font-medium text-gray-900">{{ $permission->user->name }}</p>
                            <span class="text-xs text-gray-500">
                                @if($permission->user->hasRole('sales_manager')) مدير مبيعات
                                @elseif($permission->user->hasRole('sales')) مندوب مبيعات
                                @elseif($permission->user->hasRole('follow_up')) متابعة
                                @endif
                                له صلاحية الوصول
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- بيانات العميل -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    معلومات العميل
                </h3>
            </div>

            @if (auth()->user()->hasRole('sales') || auth()->user()->hasRole('sales_manager') || auth()->user()->hasRole('follow_up'))
                @if ($isEditingClient)
                    <form wire:submit.prevent="saveClientData" class="px-5 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div>
                                <label for="client-name" class="block text-sm font-medium text-gray-700 mb-1">الاسم الكامل</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="client-name" wire:model="clientData.name"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pr-10 p-2.5">
                                </div>
                                @error('clientData.name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="client-email" class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="email" id="client-email" wire:model="clientData.email"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pr-10 p-2.5">
                                </div>
                                @error('clientData.email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="client-phone" class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="client-phone" wire:model="clientData.phone"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pr-10 p-2.5">
                                </div>
                                @error('clientData.phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end space-x-3 space-x-reverse">
                            <button type="button" wire:click="$set('isEditingClient', false)"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                إلغاء التعديل
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                حفظ التغييرات
                            </button>
                        </div>
                    </form>
                @else
                    <div class="px-5 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">الاسم الكامل</p>
                                <p class="text-sm font-medium text-gray-900">{{ $order->name }}</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">البريد الإلكتروني</p>
                                <p class="text-sm font-medium text-gray-900">{{ $order->email }}</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">رقم الهاتف</p>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900">{{ $order->phone }}</p>
                                    <div class="flex space-x-2 space-x-reverse">
                                        <a href="tel:{{ $order->phone }}" class="p-1.5 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                        </a>
                                        <a href="https://wa.me/{{ $order->phone }}" target="_blank" class="p-1.5 rounded-full bg-green-50 text-green-600 hover:bg-green-100">
                                            <svg fill="green"  class="h-5 w-5" fill="none" viewBox="0 0 24 24" version="1.1" id="Icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="WA_Logo"> <g> <path fill-rule="evenodd" clip-rule="evenodd" d="M20.5,3.5C18.25,1.25,15.2,0,12,0C5.41,0,0,5.41,0,12c0,2.11,0.65,4.11,1.7,5.92 L0,24l6.33-1.55C8.08,23.41,10,24,12,24c6.59,0,12-5.41,12-12C24,8.81,22.76,5.76,20.5,3.5z M12,22c-1.78,0-3.48-0.59-5.01-1.49 l-0.36-0.22l-3.76,0.99l1-3.67l-0.24-0.38C2.64,15.65,2,13.88,2,12C2,6.52,6.52,2,12,2c2.65,0,5.2,1.05,7.08,2.93S22,9.35,22,12 C22,17.48,17.47,22,12,22z M17.5,14.45c-0.3-0.15-1.77-0.87-2.04-0.97c-0.27-0.1-0.47-0.15-0.67,0.15 c-0.2,0.3-0.77,0.97-0.95,1.17c-0.17,0.2-0.35,0.22-0.65,0.07c-0.3-0.15-1.26-0.46-2.4-1.48c-0.89-0.79-1.49-1.77-1.66-2.07 c-0.17-0.3-0.02-0.46,0.13-0.61c0.13-0.13,0.3-0.35,0.45-0.52s0.2-0.3,0.3-0.5c0.1-0.2,0.05-0.37-0.02-0.52 C9.91,9.02,9.31,7.55,9.06,6.95c-0.24-0.58-0.49-0.5-0.67-0.51C8.22,6.43,8.02,6.43,7.82,6.43S7.3,6.51,7.02,6.8 C6.75,7.1,5.98,7.83,5.98,9.3c0,1.47,1.07,2.89,1.22,3.09c0.15,0.2,2.11,3.22,5.1,4.51c0.71,0.31,1.27,0.49,1.7,0.63 c0.72,0.23,1.37,0.2,1.88,0.12c0.57-0.09,1.77-0.72,2.02-1.42c0.25-0.7,0.25-1.3,0.17-1.42C18,14.68,17.8,14.6,17.5,14.45z"></path> </g> </g> </g></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button wire:click="startEditClient" type="button"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                تعديل بيانات العميل
                            </button>
                        </div>
                    </div>
                @endif
            @else
                <div class="px-5 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">الاسم الكامل</p>
                            <p class="text-sm font-medium text-gray-900">{{ $order->name }}</p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">البريد الإلكتروني</p>
                            <p class="text-sm font-medium text-gray-900">{{ $order->email }}</p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">رقم الهاتف</p>
                            <p class="text-sm font-medium text-gray-900">{{ $order->phone }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- معلومات الطلب -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- معلومات الوحدة -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        معلومات الوحدة
                    </h3>
                </div>
                <div class="px-5 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">المشروع</p>
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">{{ $order->project?->name ?? '-' }}</p>
                                <a href="{{ route('frontend.projects.single', $order->project?->slug) }}" target="_blanck" class="text-primary-600 hover:text-primary-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">الوحدة</p>
                            <p class="text-sm font-medium text-gray-900">{{ $order->unit?->title ?? '-' }}</p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">حالة الوحدة</p>

                            @if($isEditingUnitCase)
                                <div class="mt-1">
                                    <select wire:model="unitCase"
                                            class="block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                        <option value="">اختر حالة الوحدة</option>
                                        <option value="0">متاح</option>
                                        <option value="1">محجوزة</option>
                                        <option value="2">مباعة</option>
                                    </select>
                                    @error('unitCase') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            @else
                                <p class="text-sm font-medium text-gray-900">
                                    @if ($this->order->unit->case == 0 )
                                    متاح
                                    @elseif ($this->order->unit->case == 1)
                                    محجوزة
                                    @elseif ($this->order->unit->case == 2)
                                    مباعة
                                    @endif
                                </p>
                            @endif

                            <div class="mt-3 flex justify-end">
                                @if(!$isEditingUnitCase)
                                    <button wire:click="startEditUnitCase"
                                            type="button"
                                            class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-md transition duration-150 ease-in-out">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        تعديل
                                    </button>
                                @else
                                    <div class="flex space-x-2 space-x-reverse">
                                        <button wire:click="$set('isEditingUnitCase', false)"
                                                type="button"
                                                class="inline-flex items-center px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs rounded-md transition duration-150 ease-in-out">
                                            إلغاء
                                        </button>
                                        <button wire:click="saveUnitCase"
                                                type="button"
                                                class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs rounded-md transition duration-150 ease-in-out">
                                            حفظ
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">نوع الشراء</p>
                            <p class="text-sm font-medium text-gray-900">{{ $purchaseTypes[$order->PurchaseType] ?? $order->PurchaseType }}</p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">الغرض من الشراء</p>
                            <p class="text-sm font-medium text-gray-900">{{ $purchasePurposes[$order->PurchasePurpose] ?? $order->PurchasePurpose }}</p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">نوع الدعم المطلوب</p>
                            <p class="text-sm font-medium text-gray-900">{{ $supportTypes[$order->support_type] ?? $order->support_type }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- حالة الطلب -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        حالة الطلب
                    </h3>
                </div>
                <div class="px-5 py-4">
                    @if (session()->has('messageStatus'))
                        <div class="mb-4 p-3 bg-green-50 text-green-700 text-sm rounded-lg">
                            {{ session('messageStatus') }}
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الحالة الحالية</label>
                            <select wire:change="updateStatus($event.target.value)"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                                @foreach ($statusLabels as $key => $label)
                                    <option value="{{ $key }}" {{ $order->status == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if ($this->isDelayed())
                            <div class="p-3 bg-red-50 text-red-700 rounded-lg flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <p class="font-medium">تنبيه!</p>
                                    <p class="text-sm">الطلب متأخر (لم يتم التعديل منذ أكثر من 3 أيام)</p>
                                </div>
                            </div>
                        @endif

                        <div class="pt-2">
                            <p class="text-xs text-gray-500 mb-1">آخر تحديث للحالة</p>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $order->updated_at->format('Y-m-d H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ملاحظات الطلب -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    ملاحظات الطلب
                </h3>
            </div>

            <div class="px-5 py-4">
                @if ($order->message || $isEditingMessage)
                    <div class="p-4 bg-gray-50 rounded-lg border border-primary-500 mb-3">
                        <div class="flex justify-between items-start mb-2">
                            <p class="text-sm font-medium flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                </svg>
                                ملاحظة عن انشاء الطلب
                            </p>
                            @if (!$isEditingMessage && $order->message)
                                <div class="flex space-x-1 space-x-reverse">
                                    <button wire:click="startEditMessage"
                                            class="text-primary-600 hover:text-white p-1 rounded hover:bg-primary-100"
                                            title="تعديل الملاحظة">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click="deleteOrderMessage"
                                            wire:confirm="هل أنت متأكد من حذف هذه الملاحظة؟"
                                            class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-100"
                                            title="حذف الملاحظة">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>

                        @if ($isEditingMessage)
                            <form wire:submit.prevent="saveOrderMessage">
                                <div class="mb-3">
                                    <textarea wire:model="orderMessage"
                                            rows="4"
                                            class="w-full border border-gray-300 rounded-lg bg-white p-3 text-sm focus:ring-primary-500 focus:border-primary-500 @error('orderMessage') border-red-500 @enderror"
                                            placeholder="اكتب ملاحظة عن الطلب... (اختياري)"></textarea>

                                    @error('orderMessage')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror

                                    <div class="mt-1 flex justify-between">
                                        <span class="text-xs text-gray-500">{{ strlen($orderMessage ?? '') }}/1000 حرف</span>
                                        @if($orderMessage)
                                            <span class="text-xs text-green-600">✓ يوجد محتوى</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-2 space-x-reverse">
                                    <button type="button"
                                            wire:click="cancelEditMessage"
                                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors duration-200">
                                        إلغاء
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors duration-200">
                                        <span wire:loading.remove wire:target="saveOrderMessage">حفظ الملاحظة</span>
                                        <span wire:loading wire:target="saveOrderMessage" class="flex items-center">
                                            <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            جاري الحفظ...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="bg-white p-3 rounded border-r-4 border-primary-500">
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $order->message }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    {{-- إضافة ملاحظة جديدة إذا لم تكن موجودة --}}
                    <div class="p-4 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 mb-3">
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                            <p class="text-sm text-gray-500 mb-3">لا توجد ملاحظة أساسية للطلب</p>
                            <button wire:click="startEditMessage"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                إضافة ملاحظة أساسية
                            </button>
                        </div>
                    </div>
                @endif
                <!-- قائمة الملاحظات -->
                <div class="space-y-4 mb-6">
                    @forelse ($order->notes()->with('user')->get() as $note)
                        <div class="p-4 bg-gray-50 rounded-lg border-l-4 border-primary-500">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-200 text-gray-700 font-medium text-sm">
                                        {{ substr($note->user->name, 0, 1) }}
                                    </span>
                                    <div class="mr-2">
                                        <p class="text-sm font-medium text-gray-900">{{ $note->user->name }}</p>
                                        <div class="flex items-center mt-1">
                                            @if ($note->user->hasRole('sales_manager'))
                                                <span class="text-xs bg-blue-500 text-white rounded-full px-2 py-0.5">مدير مبيعات</span>
                                            @elseif ($note->user->hasRole('sales'))
                                                <span class="text-xs bg-green-500 text-white rounded-full px-2 py-0.5">مندوب مبيعات</span>
                                            @elseif ($note->user->hasRole('super_admin'))
                                                <span class="text-xs bg-gray-700 text-white rounded-full px-2 py-0.5">مدير النظام</span>
                                            @elseif ($note->user->hasRole('follow_up'))
                                                <span class="text-xs bg-yellow-500 text-white rounded-full px-2 py-0.5">متابعة</span>
                                            @endif
                                            <span class="text-xs text-gray-500 mr-2">{{ $note->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500">{{ $note->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            <p class="text-sm text-gray-700 mt-2">{{ $note->note }}</p>
                        </div>
                    @empty
                        <div class="p-4 bg-gray-50 rounded-lg text-center">
                            <p class="text-sm text-gray-500">لا توجد ملاحظات مسجلة</p>
                        </div>
                    @endforelse
                </div>

                <!-- نموذج إضافة ملاحظة -->
                <div class="border-t border-gray-100 pt-4">
                    @if (session()->has('message'))
                        <div class="mb-4 p-3 bg-green-50 text-green-700 text-sm rounded-lg">
                            {{ session('message') }}
                        </div>
                    @endif

                    @error('note'))
                        <div class="mb-4 p-3 bg-red-50 text-red-700 text-sm rounded-lg">
                            {{ $message }}
                        </div>
                    @enderror

                    <label for="note" class="block text-sm font-medium text-gray-700 mb-2">إضافة ملاحظة جديدة</label>
                    <textarea wire:model="note" id="note" rows="3"
                        class="block w-full border border-gray-300 rounded-lg bg-gray-50 p-3 focus:ring-primary-500 focus:border-primary-500 @error('note') border-red-500 @enderror"
                        placeholder="اكتب ملاحظة هنا..."></textarea>

                    <div class="mt-3 flex justify-end">
                        <button wire:click="addNote" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            <span wire:loading.remove wire:target="addNote">
                                حفظ الملاحظة
                            </span>
                            <span wire:loading wire:target="addNote" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                جاري الحفظ...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
