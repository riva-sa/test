<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100" dir="rtl">
    {{-- Header Section --}}
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                {{-- Title and Breadcrumb --}}
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">لوحة تحكم الحملات <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">تجريبي</span></h1>
                        <p class="text-sm text-gray-600 mt-2 flex items-center">
                            <svg class="w-4 h-4 ml-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            آخر تحديث: {{ $lastUpdateTime }}
                            @if($enableRealTime)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-3">
                                    <span class="w-2 h-2 bg-green-400 rounded-full ml-1 animate-pulse"></span>
                                    مباشر
                                </span>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center space-x-3 space-x-reverse">
                    {{-- Real-time Toggle --}}
                    {{-- <label class="inline-flex items-center bg-white px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors cursor-pointer">
                        <input type="checkbox" wire:model.live="enableRealTime" class="form-checkbox h-4 w-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                        <span class="mr-3 text-sm font-medium text-gray-700">التحديث المباشر</span>
                    </label> --}}

                    {{-- Create Campaign Button --}}
                    <button wire:click="openCreateModal" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        إنشاء حملة جديدة
                    </button>

                    {{-- Refresh Button --}}
                    <button wire:click="refreshDashboardData" class="inline-flex items-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.class="animate-spin" wire:target="refreshDashboardData">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Enhanced Filter Bar --}}
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            {{-- Primary Filters Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-6 gap-2 items-end mb-3">
                {{-- Search --}}
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">البحث في الحملات</label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                               class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm placeholder-gray-400 transition-all duration-200"
                               placeholder="ابحث بالاسم أو الوصف...">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Date Filter --}}
                <select wire:model.live="datePreset"
                    class="inline-flex items-center bg-gray-50 px-4 py-3 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors cursor-pointer text-sm">
                    <option value="today">اليوم</option>
                    <option value="yesterday">أمس</option>
                    <option value="last_7_days">آخر 7 أيام</option>
                    <option value="last_30_days">آخر 30 يوم</option>
                    <option value="this_month">هذا الشهر</option>
                    <option value="last_month">الشهر الماضي</option>
                </select>


                {{-- Custom Date Toggle --}}
                <div class="flex items-center justify-center">
                    <label class="inline-flex items-center bg-gray-50 px-4 py-3 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors cursor-pointer">
                        <input type="checkbox" wire:model.live="useCustomDate" class="form-checkbox h-4 w-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                        <span class="mr-3 text-sm font-medium text-gray-700">تاريخ مخصص</span>
                    </label>
                </div>

                {{-- Custom Date Inputs --}}
                @if($useCustomDate)
                <div class="lg:col-span-2 grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">من</label>
                        <input type="date" wire:model.live="customStartDate" class="custom-select">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">إلى</label>
                        <input type="date" wire:model.live="customEndDate" class="custom-select">
                    </div>
                </div>
                @endif

                {{-- Clear Filters --}}
                <div>
                    <button wire:click="clearFilters" class="w-full inline-flex justify-center items-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-red-50 hover:text-red-700 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        مسح الفلاتر
                    </button>
                </div>
            </div>

            {{-- Advanced Filters Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {{-- Project Filter --}}
                {{-- <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المشاريع</label>
                    <select wire:model.live="selectedProjects" multiple class="custom-multiselect" size="3">
                        @foreach($this->projects as $project)
                            <option value="{{ $project->id }}" class="px-3 py-2 hover:bg-indigo-50">{{ $project->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">اضغط Ctrl للاختيار المتعدد</p>
                </div> --}}

                {{-- Sources Filter --}}
                {{-- <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المصادر</label>
                    <select wire:model.live="selectedSources" multiple class="custom-multiselect" size="3">
                        @foreach($availableSources as $key => $value)
                            <option value="{{ $key }}" class="px-3 py-2 hover:bg-indigo-50">{{ $value }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">اضغط Ctrl للاختيار المتعدد</p>
                </div> --}}

                {{-- Event Types Filter --}}
                {{-- <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع الأحداث</label>
                    <select wire:model.live="selectedEventTypes" multiple class="custom-multiselect" size="3">
                        <option value="visit" class="px-3 py-2 hover:bg-indigo-50">زيارات</option>
                        <option value="view" class="px-3 py-2 hover:bg-indigo-50">مشاهدات</option>
                        <option value="show" class="px-3 py-2 hover:bg-indigo-50">عروض</option>
                        <option value="order" class="px-3 py-2 hover:bg-indigo-50">طلبات</option>
                        <option value="whatsapp" class="px-3 py-2 hover:bg-indigo-50">واتساب</option>
                        <option value="call" class="px-3 py-2 hover:bg-indigo-50">مكالمات</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">اضغط Ctrl للاختيار المتعدد</p>
                </div> --}}

                {{-- View Mode Selector --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">وضع العرض</label>
                    <div class="grid grid-cols-4 gap-1 p-1 bg-gray-100 rounded-lg">
                        <button wire:click="switchView('overview')" 
                                class="px-3 py-2 text-xs font-medium rounded-md transition-all duration-200 {{ $viewMode === 'overview' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-sm' }}">
                            عام
                        </button>
                        <button wire:click="switchView('detailed')" 
                                class="px-3 py-2 text-xs font-medium rounded-md transition-all duration-200 {{ $viewMode === 'detailed' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-sm' }}">
                            تفصيلي
                        </button>
                        <button wire:click="switchView('management')" 
                                class="px-3 py-2 text-xs font-medium rounded-md transition-all duration-200 {{ $viewMode === 'management' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-sm' }}">
                            إدارة
                        </button>
                        <button wire:click="switchView('comparison')" 
                                class="px-3 py-2 text-xs font-medium rounded-md transition-all duration-200 {{ $viewMode === 'comparison' ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-700 hover:bg-white hover:shadow-sm' }}">
                            مقارنة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Enhanced Loading Overlay --}}
        <div wire:loading.flex wire:target="refreshDashboardData,selectedCampaignId,switchView" 
             class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 items-center justify-center backdrop-blur-sm">
            <div class="bg-white rounded-xl p-8 shadow-2xl max-w-sm mx-4">
                <div class="flex flex-col items-center space-y-4">
                    <svg class="loading-spinner w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <div class="text-center">
                        <h3 class="text-lg font-semibold text-gray-900">جاري تحديث البيانات</h3>
                        <p class="text-sm text-gray-600 mt-1">يرجى الانتظار...</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Overview Mode --}}
        @if($viewMode === 'overview')
            @include('livewire.mannager.partials.dashboard-overview')
        @endif

        {{-- Detailed Mode --}}
        @if($viewMode === 'detailed')
            @include('livewire.mannager.partials.campaign-detailed')
        @endif

        {{-- Management Mode --}}
        @if($viewMode === 'management')
            @include('livewire.mannager.partials.campaign-management')
        @endif

        {{-- Comparison Mode --}}
        @if($viewMode === 'comparison')
            @include('livewire.mannager.partials.campaign-comparison')
        @endif
    </div>

    {{-- Modals --}}
    @include('livewire.mannager.partials.campaign-modal')
    @include('livewire.mannager.partials.comparison-modal')

    {{-- Enhanced Notification Area --}}
    <div id="notification-area" class="fixed top-4 left-4 z-50 space-y-2"></div>
</div>

{{-- Enhanced Scripts --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('livewire:initialized', () => {
    // Enhanced Notification System
    Livewire.on('showNotification', (data) => {
        const notification = document.createElement('div');
        const typeClass = data.type === 'success' ? 'notification-success' : 
                         data.type === 'error' ? 'notification-error' : 'notification-info';
        
        notification.className = `${typeClass} mb-4 transform transition-all duration-500 translate-x-full opacity-0`;
        notification.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${data.type === 'success' ? 
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' :
                            data.type === 'error' ?
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>' :
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                        }
                    </svg>
                    <span class="font-medium">${data.message}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="mr-4 text-white hover:text-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        const container = document.getElementById('notification-area');
        container.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full', 'opacity-0');
            notification.classList.add('translate-x-0', 'opacity-100');
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => notification.remove(), 500);
        }, 5000);
    });

    // Delete Confirmation
    Livewire.on('showDeleteConfirmation', (data) => {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'لن تتمكن من التراجع عن هذا الإجراء!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'نعم، احذف!',
            cancelButtonText: 'إلغاء',
            reverseButtons: true,
            customClass: {
                popup: 'text-right',
                title: 'text-right',
                content: 'text-right'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch('deleteConfirmed', { campaignId: data.campaignId });
            }
        });
    });

    // Data Refreshed Event
    Livewire.on('dataRefreshed', () => {
        console.log('Data refreshed successfully');
    });

    // Chart Updates
    Livewire.on('updateCharts', (data) => {
        // Update charts with new data
        if (window.dailyChart && data.data.daily_breakdown) {
            window.dailyChart.updateData(data.data.daily_breakdown);
        }
    });
});

// Enhanced Multi-select functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add visual feedback for multi-select options
    const multiSelects = document.querySelectorAll('.custom-multiselect');
    multiSelects.forEach(select => {
        select.addEventListener('change', function() {
            const selectedOptions = Array.from(this.selectedOptions);
            selectedOptions.forEach(option => {
                option.classList.add('bg-indigo-100', 'text-indigo-800');
            });
            
            const unselectedOptions = Array.from(this.options).filter(option => !option.selected);
            unselectedOptions.forEach(option => {
                option.classList.remove('bg-indigo-100', 'text-indigo-800');
            });
        });
    });
});
</script>
@endpush