{{-- resources/views/livewire/mannager/partials/campaign-modal.blade.php --}}

@if($showCampaignModal)
<div class="fixed inset-0 bg-gray-900 bg-opacity-60 z-50 flex items-center justify-center p-4 backdrop-blur-sm" 
     x-data="{ show: @entangle('showCampaignModal') }" 
     x-show="show" 
     x-transition:enter="ease-out duration-300" 
     x-transition:enter-start="opacity-0" 
     x-transition:enter-end="opacity-100" 
     x-transition:leave="ease-in duration-200" 
     x-transition:leave-start="opacity-100" 
     x-transition:leave-end="opacity-0" 
     @click.self="show = false">
    
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden transform transition-all" 
         x-show="show" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         @click.stop>
        
        <form wire:submit.prevent="saveCampaign">
            {{-- Enhanced Modal Header --}}
            <div class="flex justify-between items-center px-4 py-5 border-b border-gray-200 bg-gradient-to-l from-indigo-50 to-white">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $isEditMode ? 'تعديل الحملة' : 'إنشاء حملة جديدة' }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $isEditMode ? 'قم بتعديل بيانات الحملة' : 'أدخل بيانات الحملة الجديدة' }}</p>
                    </div>
                </div>
                <button type="button" @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors p-2 rounded-lg hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Enhanced Modal Body --}}
            <div class="p-4 space-y-8 max-h-[50vh] overflow-y-auto">
                {{-- Basic Information Section --}}
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-4 border border-gray-200">
                    <h4 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 text-indigo-600 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        المعلومات الأساسية
                    </h4>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        {{-- Campaign Name --}}
                        <div class="lg:col-span-2">
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-3">
                                اسم الحملة <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       wire:model.defer="name" 
                                       id="name" 
                                       class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-lg @error('name') border-red-300 ring-2 ring-red-200 @enderror" 
                                       placeholder="مثال: حملة العروض الصيفية 2024">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 flex items-center bg-red-50 px-3 py-2 rounded-lg">
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Project Selection --}}
                        <div>
                            <label for="project_id" class="block text-sm font-semibold text-gray-700 mb-3">
                                المشروع المستهدف <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select wire:model.defer="project_id" 
                                        id="project_id" 
                                        class="block w-full py-2 px-2 pr-10 border border-gray-300 bg-white rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-lg appearance-none @error('project_id') border-red-300 ring-2 ring-red-200 @enderror">
                                    <option value="">اختر مشروعًا</option>
                                    @foreach($this->projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('project_id')
                                <p class="mt-2 text-sm text-red-600 flex items-center bg-red-50 px-3 py-2 rounded-lg">
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Source Selection --}}
                        <div>
                            <label for="source" class="block text-sm font-semibold text-gray-700 mb-3">
                                مصدر الحملة <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select wire:model.defer="source" 
                                        id="source" 
                                        class="block w-full py-2 px-2 pr-10 border border-gray-300 bg-white rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-lg appearance-none @error('source') border-red-300 ring-2 ring-red-200 @enderror">
                                    <option value="">اختر المصدر</option>
                                    @foreach($availableSources as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('source')
                                <p class="mt-2 text-sm text-red-600 flex items-center bg-red-50 px-3 py-2 rounded-lg">
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Status Selection --}}
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-3">
                                حالة الحملة <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select wire:model.defer="status" 
                                        id="status" 
                                        class="block w-full py-2 px-2 pr-10 border border-gray-300 bg-white rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-lg appearance-none">
                                    @foreach($campaignStatuses as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Budget --}}
                        <div>
                            <label for="budget" class="block text-sm font-semibold text-gray-700 mb-3">
                                الميزانية (ريال سعودي)
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       step="0.01" 
                                       wire:model.defer="budget" 
                                       id="budget" 
                                       class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-lg @error('budget') border-red-300 ring-2 ring-red-200 @enderror" 
                                       placeholder="0.00">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('budget')
                                <p class="mt-2 text-sm text-red-600 flex items-center bg-red-50 px-3 py-2 rounded-lg">
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Timeline Section --}}
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-4 border border-blue-200">
                    <h4 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 text-blue-600 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        الجدول الزمني
                    </h4>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        {{-- Start Date --}}
                        <div>
                            <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-3">
                                تاريخ البدء <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   wire:model.defer="start_date" 
                                   id="start_date" 
                                   class="block w-full py-2 px-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-lg @error('start_date') border-red-300 ring-2 ring-red-200 @enderror">
                            @error('start_date')
                                <p class="mt-2 text-sm text-red-600 flex items-center bg-red-50 px-3 py-2 rounded-lg">
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- End Date --}}
                        <div>
                            <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-3">
                                تاريخ الانتهاء <span class="text-gray-500 font-normal">(اختياري)</span>
                            </label>
                            <input type="date" 
                                   wire:model.defer="end_date" 
                                   id="end_date" 
                                   class="block w-full py-2 px-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-lg @error('end_date') border-red-300 ring-2 ring-red-200 @enderror">
                            @error('end_date')
                                <p class="mt-2 text-sm text-red-600 flex items-center bg-red-50 px-3 py-2 rounded-lg">
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-600 bg-blue-50 px-3 py-2 rounded-lg">اتركه فارغاً إذا كانت الحملة مستمرة</p>
                        </div>
                    </div>
                </div>

                {{-- Description Section --}}
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-4 border border-green-200">
                    <h4 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 text-green-600 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        الوصف والتفاصيل
                    </h4>
                    
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-3">
                            وصف الحملة
                        </label>
                        <textarea wire:model.defer="description" 
                                  id="description" 
                                  rows="5" 
                                  class="block w-full py-2 px-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-lg resize-none" 
                                  placeholder="اكتب وصفاً تفصيلياً للحملة، أهدافها، والجمهور المستهدف..."></textarea>
                        <p class="mt-2 text-sm text-gray-600 bg-green-50 px-3 py-2 rounded-lg">وصف مفصل يساعد في تتبع أداء الحملة وتحليل النتائج</p>
                    </div>
                </div>
            </div>

            {{-- Enhanced Modal Footer --}}
            <div class="flex justify-between items-center px-4 py-5 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center text-sm text-gray-600">
                    <svg class="w-4 h-4 ml-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    الحقول المطلوبة مميزة بـ <span class="text-red-500 font-semibold">*</span>
                </div>
                
                <div class="flex items-center space-x-4 space-x-reverse">
                    <button type="button" 
                            @click="show = false" 
                            class="inline-flex items-center px-6 py-2 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        إلغاء
                    </button>
                    
                    <button type="submit" 
                            class="inline-flex items-center px-8 py-2 bg-indigo-600 border border-transparent rounded-xl text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg transform hover:scale-105"
                            wire:loading.attr="disabled"
                            wire:target="saveCampaign">
                        <svg wire:loading.remove wire:target="saveCampaign" class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <svg wire:loading wire:target="saveCampaign" class="w-5 h-5 ml-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="saveCampaign">{{ $isEditMode ? 'حفظ التعديلات' : 'إنشاء الحملة' }}</span>
                        <span wire:loading wire:target="saveCampaign">جاري الحفظ...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

