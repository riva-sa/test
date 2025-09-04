@if($selectedCampaignId && !empty($campaignAnalytics))
    {{-- Campaign Header --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <button wire:click="clearSelection" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $campaignAnalytics['campaign']->name }}</h2>
                        <div class="flex items-center space-x-4 space-x-reverse text-sm text-gray-500 mt-1">
                            <span>المشروع: {{ $campaignAnalytics['campaign']->project->name }}</span>
                            <span>•</span>
                            <span>المصدر: {{ $availableSources[$campaignAnalytics['campaign']->source] ?? $campaignAnalytics['campaign']->source }}</span>
                            <span>•</span>
                            <span>{{ $campaignAnalytics['campaign']->start_date->format('Y/m/d') }} - {{ $campaignAnalytics['campaign']->end_date ? $campaignAnalytics['campaign']->end_date->format('Y/m/d') : 'مستمرة' }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3 space-x-reverse">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $campaignAnalytics['campaign']->status === 'active' ? 'bg-green-100 text-green-800' : ($campaignAnalytics['campaign']->status === 'paused' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ $campaignStatuses[$campaignAnalytics['campaign']->status] ?? $campaignAnalytics['campaign']->status }}
                    </span>
                    <button wire:click="openEditModal({{ $campaignAnalytics['campaign']->id }})" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                        تعديل
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Campaign Overview Metrics --}}
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($campaignAnalytics['overview']['total_events'] ?? 0) }}</div>
                    <div class="text-sm text-gray-500">إجمالي الأحداث</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($campaignAnalytics['overview']['visits'] ?? 0) }}</div>
                    <div class="text-sm text-gray-500">زيارات</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ number_format($campaignAnalytics['overview']['views'] ?? 0) }}</div>
                    <div class="text-sm text-gray-500">مشاهدات</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ number_format($campaignAnalytics['overview']['shows'] ?? 0) }}</div>
                    <div class="text-sm text-gray-500">عروض</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ number_format($campaignAnalytics['overview']['orders'] ?? 0) }}</div>
                    <div class="text-sm text-gray-500">طلبات</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ number_format($campaignAnalytics['overview']['whatsapp'] ?? 0) }}</div>
                    <div class="text-sm text-gray-500">واتساب</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($campaignAnalytics['overview']['calls'] ?? 0) }}</div>
                    <div class="text-sm text-gray-500">مكالمات</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts and Analytics --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Daily Performance Chart --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">الأداء اليومي</h3>
            </div>
            <div class="p-6">
                <div class="h-80" x-data="dailyChart()" x-init="initChart()" @update-charts.window="updateChart($event.detail.data.daily_breakdown)">
                    <canvas id="dailyPerformanceChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Conversion Funnel --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">قمع التحويل</h3>
            </div>
            <div class="p-6">
                @if(!empty($campaignAnalytics['conversion_funnel']))
                    <div class="space-y-4">
                        @foreach($campaignAnalytics['conversion_funnel'] as $index => $stage)
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-16 text-right">
                                    <span class="text-sm font-medium text-gray-600">{{ $stage['stage'] }}</span>
                                </div>
                                <div class="flex-1 mx-4">
                                    <div class="bg-gray-200 rounded-full h-4 relative overflow-hidden">
                                        <div class="bg-gradient-to-l from-indigo-500 to-indigo-600 h-full rounded-full transition-all duration-500" 
                                             style="width: {{ $stage['percentage'] }}%"></div>
                                        <div class="absolute inset-0 flex items-center justify-center text-xs font-medium text-white">
                                            {{ $stage['percentage'] }}%
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 w-16">
                                    <span class="text-sm font-bold text-gray-900">{{ number_format($stage['count']) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">لا توجد بيانات كافية</div>
                @endif
            </div>
        </div>

        {{-- Hourly Breakdown --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">التوزيع الساعي</h3>
            </div>
            <div class="p-6">
                <div class="h-64" x-data="hourlyChart()" x-init="initChart()" @update-charts.window="updateChart($event.detail.data.hourly_breakdown)">
                    <canvas id="hourlyBreakdownChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Additional Analytics --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Traffic Sources --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">مصادر الزيارات</h3>
            </div>
            <div class="p-6">
                @if(!empty($campaignAnalytics['traffic_sources']))
                    <div class="space-y-3">
                        @foreach($campaignAnalytics['traffic_sources'] as $source)
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-600">{{ $availableSources[$source['source']] ?? $source['source'] }}</span>
                                <span class="text-sm font-bold text-gray-900">{{ number_format($source['count']) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">لا توجد بيانات</div>
                @endif
            </div>
        </div>

        {{-- Device Breakdown --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">الأجهزة</h3>
            </div>
            <div class="p-6">
                @if(!empty($campaignAnalytics['device_breakdown']))
                    <div class="space-y-3">
                        @foreach($campaignAnalytics['device_breakdown'] as $device)
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-600">{{ ucfirst($device['device_type']) }}</span>
                                <span class="text-sm font-bold text-gray-900">{{ number_format($device['count']) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">لا توجد بيانات</div>
                @endif
            </div>
        </div>

        {{-- ROI Analysis --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">تحليل العائد على الاستثمار</h3>
            </div>
            <div class="p-6">
                @if(!empty($campaignAnalytics['roi_analysis']))
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">الميزانية</span>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($campaignAnalytics['roi_analysis']['budget']) }} ر.س</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">تكلفة النقرة</span>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($campaignAnalytics['roi_analysis']['cost_per_click'], 2) }} ر.س</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">تكلفة الاكتساب</span>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($campaignAnalytics['roi_analysis']['cost_per_acquisition'], 2) }} ر.س</span>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                            <span class="text-sm font-medium text-gray-600">العائد على الاستثمار</span>
                            <span class="text-sm font-bold {{ $campaignAnalytics['roi_analysis']['return_on_investment'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $campaignAnalytics['roi_analysis']['return_on_investment'] }}%
                            </span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">لا توجد بيانات كافية</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Top Content --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">أفضل المحتوى أداءً</h3>
        </div>
        <div class="p-6">
            @if(!empty($campaignAnalytics['top_content']))
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($campaignAnalytics['top_content'] as $content)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <h4 class="font-medium text-gray-900 mb-2">{{ $content['unit']->title ?? 'وحدة #' . $content['unit']->id }}</h4>
                            <div class="text-sm text-gray-600 mb-3">
                                <p>المساحة: {{ $content['unit']->area ?? 'N/A' }} م²</p>
                                <p>السعر: {{ number_format($content['unit']->price ?? 0) }} ر.س</p>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">التفاعلات</span>
                                <span class="text-sm font-bold text-indigo-600">{{ number_format($content['interactions']) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">لا توجد بيانات</div>
            @endif
        </div>
    </div>

@else
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">لم يتم اختيار حملة</h3>
        <p class="mt-1 text-sm text-gray-500">اختر حملة من القائمة لعرض التحليل التفصيلي</p>
        <div class="mt-6">
            <button wire:click="switchView('overview')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                العودة للنظرة العامة
            </button>
        </div>
    </div>
@endif

@push('scripts')
<script>
function dailyChart() {
    return {
        chart: null,
        initChart() {
            const ctx = document.getElementById('dailyPerformanceChart');
            if (!ctx) return;
            
            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: 'زيارات',
                            data: [],
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'طلبات',
                            data: [],
                            borderColor: 'rgb(147, 51, 234)',
                            backgroundColor: 'rgba(147, 51, 234, 0.1)',
                            tension: 0.3,
                            type: 'bar'
                        },
                        {
                            label: 'واتساب',
                            data: [],
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        },
        updateChart(data) {
            if (!this.chart || !data) return;
            
            const labels = data.map(d => d.date);
            this.chart.data.labels = labels;
            this.chart.data.datasets[0].data = data.map(d => d.visit || 0);
            this.chart.data.datasets[1].data = data.map(d => d.order || 0);
            this.chart.data.datasets[2].data = data.map(d => d.whatsapp || 0);
            this.chart.update();
        }
    }
}

function hourlyChart() {
    return {
        chart: null,
        initChart() {
            const ctx = document.getElementById('hourlyBreakdownChart');
            if (!ctx) return;
            
            this.chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Array.from({length: 24}, (_, i) => i + ':00'),
                    datasets: [{
                        label: 'الأحداث',
                        data: Array(24).fill(0),
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        },
        updateChart(data) {
            if (!this.chart || !data) return;
            
            const hourlyData = Array(24).fill(0);
            data.forEach(item => {
                if (item.hour !== undefined) {
                    hourlyData[item.hour] = (item.visit || 0) + (item.view || 0) + (item.show || 0) + (item.order || 0) + (item.whatsapp || 0) + (item.call || 0);
                }
            });
            
            this.chart.data.datasets[0].data = hourlyData;
            this.chart.update();
        }
    }
}
</script>
@endpush

