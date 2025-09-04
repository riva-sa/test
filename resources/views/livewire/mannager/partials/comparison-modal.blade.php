{{-- resources/views/livewire/mannager/partials/comparison-modal.blade.php --}}

@if($showComparisonModal)
<div class="fixed inset-0 bg-gray-900 bg-opacity-60 z-50 flex items-center justify-center p-4 backdrop-blur-sm" 
     x-data="{ show: @entangle('showComparisonModal') }" 
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
        
        {{-- Enhanced Modal Header --}}
        <div class="flex justify-between items-center px-8 py-6 border-b border-gray-200 bg-gradient-to-l from-purple-50 to-white">
            <div class="flex items-center space-x-4 space-x-reverse">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">مقارنة الحملات</h3>
                    <p class="text-sm text-gray-600 mt-1">اختر الحملات للمقارنة بينها (حتى 5 حملات)</p>
                </div>
            </div>
            <button type="button" @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors p-2 rounded-lg hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Enhanced Modal Body --}}
        <div class="p-8 max-h-[70vh] overflow-y-auto">
            {{-- Selected Campaigns Section --}}
            @if(count($comparisonCampaignIds) > 0)
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900">الحملات المحددة للمقارنة</h4>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            {{ count($comparisonCampaignIds) }}/5
                        </span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($comparisonCampaignIds as $campaignId)
                            @php
                                $campaign = App\Models\Campaign::with('project')->find($campaignId);
                            @endphp
                            @if($campaign)
                                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-xl hover:shadow-md transition-all duration-200">
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $campaign->name }}</div>
                                            <div class="text-sm text-gray-600 flex items-center space-x-2 space-x-reverse">
                                                <span>{{ $campaign->project->name ?? 'N/A' }}</span>
                                                <span class="w-1 h-1 bg-gray-400 rounded-full"></span>
                                                <span>{{ $availableSources[$campaign->source] ?? $campaign->source }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button wire:click="removeFromComparison({{ $campaignId }})" 
                                            class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-all duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Available Campaigns Section --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900">الحملات المتاحة</h4>
                    <div class="text-sm text-gray-600">
                        {{ $this->campaigns->count() }} حملة متاحة
                    </div>
                </div>
                
                <div class="space-y-3 max-h-96 overflow-y-auto border border-gray-200 rounded-xl p-4">
                    @forelse($this->campaigns->take(20) as $campaign)
                        @if(!in_array($campaign->id, $comparisonCampaignIds))
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 group">
                                <div class="flex items-center space-x-3 space-x-reverse flex-1">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-indigo-100 transition-colors duration-200">
                                        <svg class="w-5 h-5 text-gray-600 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-gray-900 truncate">{{ $campaign->name }}</div>
                                        <div class="text-sm text-gray-600 flex items-center space-x-4 space-x-reverse mt-1">
                                            <span class="truncate">{{ $campaign->project->name ?? 'N/A' }}</span>
                                            <span class="w-1 h-1 bg-gray-400 rounded-full flex-shrink-0"></span>
                                            <span class="truncate">{{ $availableSources[$campaign->source] ?? $campaign->source }}</span>
                                            <span class="w-1 h-1 bg-gray-400 rounded-full flex-shrink-0"></span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium flex-shrink-0 {{ $campaign->status === 'active' ? 'bg-green-100 text-green-800' : ($campaign->status === 'paused' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ $campaignStatuses[$campaign->status] ?? $campaign->status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <button wire:click="addToComparison({{ $campaign->id }})" 
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ count($comparisonCampaignIds) >= 5 ? 'opacity-50 cursor-not-allowed bg-gray-100 text-gray-400' : 'text-purple-600 bg-purple-50 hover:bg-purple-100 hover:text-purple-700' }}"
                                        {{ count($comparisonCampaignIds) >= 5 ? 'disabled' : '' }}>
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    إضافة
                                </button>
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد حملات متاحة</h3>
                            <p class="mt-1 text-sm text-gray-500">أنشئ حملات جديدة لتتمكن من مقارنتها</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Comparison Instructions --}}
            @if(count($comparisonCampaignIds) < 2)
                <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-600 mt-0.5 ml-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-blue-900">كيفية المقارنة</h4>
                            <div class="mt-3 text-sm text-blue-800 space-y-2">
                                <p class="flex items-center">
                                    <svg class="w-4 h-4 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    اختر حملتين على الأقل للبدء في المقارنة
                                </p>
                                <p class="flex items-center">
                                    <svg class="w-4 h-4 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    ستتمكن من مقارنة الأداء ومعدلات التحويل والمقاييس الأخرى
                                </p>
                                <p class="flex items-center">
                                    <svg class="w-4 h-4 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    يمكنك مقارنة حتى 5 حملات في نفس الوقت
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Enhanced Modal Footer --}}
        <div class="flex justify-between items-center px-8 py-6 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center space-x-3 space-x-reverse">
                @if(count($comparisonCampaignIds) > 0)
                    <button wire:click="clearComparison" 
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 hover:text-red-700 transition-all duration-200 border border-red-200">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        مسح الكل
                    </button>
                @endif
                
                @if(count($comparisonCampaignIds) >= 2)
                    <div class="text-sm text-gray-600 bg-green-50 px-3 py-2 rounded-lg border border-green-200">
                        <svg class="w-4 h-4 inline ml-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        جاهز للمقارنة!
                    </div>
                @endif
            </div>
            
            <div class="flex items-center space-x-3 space-x-reverse">
                <button type="button" 
                        @click="show = false" 
                        class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    إلغاء
                </button>
                
                <button wire:click="switchView('comparison'); show = false" 
                        class="inline-flex items-center px-8 py-3 rounded-xl text-sm font-medium transition-all duration-200 shadow-lg transform hover:scale-105 {{ count($comparisonCampaignIds) < 2 ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-purple-600 text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500' }}"
                        {{ count($comparisonCampaignIds) < 2 ? 'disabled' : '' }}>
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    بدء المقارنة
                </button>
            </div>
        </div>
    </div>
</div>
@endif

