{{-- resources/views/livewire/mannager/partials/campaign-management.blade.php --}}

<div class="space-y-6">
    {{-- Management Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">إدارة الحملات</h2>
            <p class="text-sm text-gray-600 mt-1">إدارة شاملة لجميع الحملات الإعلانية</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                إنشاء حملة جديدة
            </button>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg border border-gray-200">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div class="mr-3">
                    <p class="text-sm font-medium text-gray-600">إجمالي الحملات</p>
                    <p class="text-lg font-bold text-gray-900">{{ $this->campaigns->total() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg border border-gray-200">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="mr-3">
                    <p class="text-sm font-medium text-gray-600">نشطة</p>
                    <p class="text-lg font-bold text-green-600">{{ $this->campaigns->where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg border border-gray-200">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="mr-3">
                    <p class="text-sm font-medium text-gray-600">متوقفة</p>
                    <p class="text-lg font-bold text-yellow-600">{{ $this->campaigns->where('status', 'paused')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg border border-gray-200">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="mr-3">
                    <p class="text-sm font-medium text-gray-600">مكتملة</p>
                    <p class="text-lg font-bold text-gray-600">{{ $this->campaigns->where('status', 'completed')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Campaigns Grid --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">قائمة الحملات</h3>
                <div class="flex items-center space-x-3 space-x-reverse">
                    {{-- View Toggle --}}
                    {{-- <div class="flex rounded-lg border border-gray-300 bg-white">
                        <button class="px-3 py-1 text-xs font-medium bg-indigo-600 text-white rounded-r-lg">
                            شبكة
                        </button>
                        <button class="px-3 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50 rounded-l-lg">
                            قائمة
                        </button>
                    </div> --}}
                </div>
            </div>
        </div>

        @if($this->campaigns->count() > 0)
            {{-- Grid View --}}
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($this->campaigns as $campaign)
                        <div class="bg-white border border-gray-200 rounded-xl hover:shadow-lg transition-all duration-200 group">
                            {{-- Campaign Header --}}
                            <div class="p-6 pb-4">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors line-clamp-2">
                                            {{ $campaign->name }}
                                        </h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ $campaign->project->name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="flex items-center space-x-1 space-x-reverse">
                                        {{-- Status Badge --}}
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $campaign->status === 'active' ? 'bg-green-100 text-green-800' : ($campaign->status === 'paused' ? 'bg-yellow-100 text-yellow-800' : ($campaign->status === 'completed' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800')) }}">
                                            {{ $campaignStatuses[$campaign->status] ?? $campaign->status }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Campaign Details --}}
                                <div class="space-y-2 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-9 0a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V6a2 2 0 00-2-2"></path>
                                        </svg>
                                        <span>{{ $availableSources[$campaign->source] ?? $campaign->source }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>{{ $campaign->start_date->format('Y/m/d') }} - {{ $campaign->end_date ? $campaign->end_date->format('Y/m/d') : 'مستمرة' }}</span>
                                    </div>
                                    @if($campaign->budget)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <span>{{ number_format($campaign->budget) }} ر.س</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Campaign Actions --}}
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    {{-- View Details --}}
                                    <button wire:click="selectCampaign({{ $campaign->id }})" 
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        عرض
                                    </button>

                                    {{-- Edit --}}
                                    <button wire:click="openEditModal({{ $campaign->id }})" 
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        تعديل
                                    </button>
                                </div>

                                {{-- More Actions Dropdown --}}
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" 
                                            class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-gray-400 hover:text-gray-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                        </svg>
                                    </button>

                                    <div x-show="open" 
                                         @click.away="open = false"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                        <div class="py-2">
                                            {{-- Toggle Status --}}
                                            <button wire:click="toggleCampaignStatus({{ $campaign->id }})" 
                                                    class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                {{ $campaign->status === 'active' ? 'إيقاف الحملة' : 'تفعيل الحملة' }}
                                            </button>
                                            
                                            {{-- Duplicate --}}
                                            <button wire:click="duplicateCampaign({{ $campaign->id }})" 
                                                    class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                نسخ الحملة
                                            </button>
                                            
                                            {{-- Add to Comparison --}}
                                            <button wire:click="addToComparison({{ $campaign->id }})" 
                                                    class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                إضافة للمقارنة
                                            </button>
                                            
                                            <div class="border-t border-gray-200 my-1"></div>
                                            
                                            {{-- Delete --}}
                                            <button wire:click="confirmDelete({{ $campaign->id }})" 
                                                    class="block w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                حذف الحملة
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $this->campaigns->links() }}
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد حملات</h3>
                <p class="mt-1 text-sm text-gray-500">ابدأ بإنشاء حملتك الأولى لتتبع الأداء وتحليل النتائج</p>
                <div class="mt-6">
                    <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        إنشاء حملة جديدة
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

