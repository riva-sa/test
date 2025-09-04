@if($showExportModal)
<div class="fixed inset-0 bg-gray-900 bg-opacity-60 z-50 flex items-center justify-center p-4" 
     x-data="{ show: @entangle('showExportModal') }" 
     x-show="show" 
     x-transition:enter="ease-out duration-300" 
     x-transition:enter-start="opacity-0" 
     x-transition:enter-end="opacity-100" 
     x-transition:leave="ease-in duration-200" 
     x-transition:leave-start="opacity-100" 
     x-transition:leave-end="opacity-0" 
     @click.self="show = false">
    
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform transition-all" 
         x-show="show" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         @click.stop>
        
        {{-- Modal Header --}}
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center space-x-3 space-x-reverse">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">تصدير البيانات</h3>
                    <p class="text-sm text-gray-500">اختر تنسيق التصدير والبيانات المطلوبة</p>
                </div>
            </div>
            <button type="button" @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="p-6 space-y-6">
            {{-- Export Type Selection --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">نوع البيانات المراد تصديرها</label>
                <div class="space-y-3">
                    <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                        <input type="radio" 
                               name="exportType" 
                               value="current_view" 
                               checked 
                               class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                        <div class="mr-3 flex-1">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span class="font-medium text-gray-900">العرض الحالي</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                @if($selectedCampaignId)
                                    تصدير بيانات الحملة المحددة حاليًا
                                @else
                                    تصدير نظرة عامة على جميع الحملات
                                @endif
                            </p>
                        </div>
                    </label>

                    @if($selectedCampaignId)
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                            <input type="radio" 
                                   name="exportType" 
                                   value="detailed_campaign" 
                                   class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            <div class="mr-3 flex-1">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <span class="font-medium text-gray-900">تقرير مفصل للحملة</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">تقرير شامل يتضمن جميع التحليلات والرسوم البيانية</p>
                            </div>
                        </label>
                    @endif

                    <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                        <input type="radio" 
                               name="exportType" 
                               value="all_campaigns" 
                               class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                        <div class="mr-3 flex-1">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <span class="font-medium text-gray-900">جميع الحملات</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">قائمة شاملة بجميع الحملات مع الإحصائيات الأساسية</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Format Selection --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">تنسيق التصدير</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                        <input type="radio" 
                               wire:model.defer="exportFormat" 
                               value="pdf" 
                               class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                        <div class="mr-3 text-center flex-1">
                            <svg class="w-8 h-8 text-red-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">PDF</span>
                            <p class="text-xs text-gray-500 mt-1">تقرير منسق للطباعة</p>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                        <input type="radio" 
                               wire:model.defer="exportFormat" 
                               value="excel" 
                               class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                        <div class="mr-3 text-center flex-1">
                            <svg class="w-8 h-8 text-green-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h2a2 2 0 002-2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Excel</span>
                            <p class="text-xs text-gray-500 mt-1">جداول بيانات للتحليل</p>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                        <input type="radio" 
                               wire:model.defer="exportFormat" 
                               value="csv" 
                               class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                        <div class="mr-3 text-center flex-1">
                            <svg class="w-8 h-8 text-blue-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">CSV</span>
                            <p class="text-xs text-gray-500 mt-1">بيانات خام للاستيراد</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Date Range for Export --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">نطاق البيانات</label>
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="radio" 
                               wire:model.defer="exportDateRange" 
                               value="current" 
                               class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                        <span class="mr-2 text-sm text-gray-700">الفترة المحددة حاليًا في الفلاتر</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" 
                               wire:model.defer="exportDateRange" 
                               value="all_time" 
                               class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                        <span class="mr-2 text-sm text-gray-700">جميع البيانات المتاحة</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" 
                               wire:model.defer="exportDateRange" 
                               value="last_30_days" 
                               class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                        <span class="mr-2 text-sm text-gray-700">آخر 30 يوم</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" 
                               wire:model.defer="exportDateRange" 
                               value="last_90_days" 
                               class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                        <span class="mr-2 text-sm text-gray-700">آخر 90 يوم</span>
                    </label>
                </div>
            </div>

            {{-- Export Options --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3">خيارات إضافية</h4>
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               checked 
                               class="form-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="mr-2 text-sm text-gray-700">تضمين الرسوم البيانية (PDF فقط)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               checked 
                               class="form-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="mr-2 text-sm text-gray-700">تضمين معلومات الحملة التفصيلية</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               class="form-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="mr-2 text-sm text-gray-700">تضمين البيانات الخام للأحداث</span>
                    </label>
                </div>
            </div>

            {{-- Preview Info --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-blue-900">معاينة التصدير</h4>
                        <div class="mt-2 text-sm text-blue-800">
                            <p>سيتم تصدير البيانات بالتنسيق المحدد وإرسال رابط التحميل.</p>
                            <p class="mt-1">حجم الملف المتوقع: ~2-5 ميجابايت</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="flex justify-end items-center px-6 py-4 bg-gray-50 border-t border-gray-200 space-x-3 space-x-reverse">
            <button type="button" 
                    @click="show = false" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                إلغاء
            </button>
            
            <button wire:click="exportData" 
                    class="inline-flex items-center px-6 py-2 bg-green-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed min-w-[140px] justify-center"
                    wire:loading.attr="disabled"
                    wire:target="exportData">
                <span wire:loading.remove wire:target="exportData" class="flex items-center">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    بدء التصدير
                </span>
                <span wire:loading wire:target="exportData" class="flex items-center">
                    <svg class="animate-spin w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    جاري التصدير...
                </span>
            </button>
        </div>
    </div>
</div>
@endif

