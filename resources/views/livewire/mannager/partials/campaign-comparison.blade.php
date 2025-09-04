@if(count($comparisonCampaignIds) >= 2 && !empty($comparisonData))
    {{-- Comparison Header --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <button wire:click="switchView('overview')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">مقارنة الحملات</h2>
                        <p class="text-sm text-gray-500 mt-1">مقارنة أداء {{ count($comparisonCampaignIds) }} حملات</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 space-x-reverse">
                    <button wire:click="openComparisonModal" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                        تعديل المقارنة
                    </button>
                    <button wire:click="clearComparison" class="text-red-600 hover:text-red-800 text-sm font-medium">
                        مسح المقارنة
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Campaign Pills --}}
        <div class="p-6">
            <div class="flex flex-wrap gap-3">
                @foreach($comparisonData as $campaignId => $data)
                    <div class="inline-flex items-center px-4 py-2 bg-purple-50 border border-purple-200 rounded-full">
                        <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                        <span class="text-sm font-medium text-purple-900">{{ $data['campaign']->name }}</span>
                        <button wire:click="removeFromComparison({{ $campaignId }})" class="mr-2 text-purple-400 hover:text-purple-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Comparison Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Performance Radar Chart --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">مقارنة الأداء الشامل</h3>
            </div>
            <div class="p-6">
                <div class="h-80" x-data="comparisonRadarChart()" x-init="initChart()" @update-comparison-chart.window="updateChart($event.detail)">
                    <canvas id="campaignComparisonChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Conversion Rates Comparison --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">معدلات التحويل</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($comparisonData as $campaignId => $data)
                        @php
                            $visits = $data['metrics']['visits'] ?? 0;
                            $orders = $data['metrics']['orders'] ?? 0;
                            $conversionRate = $visits > 0 ? round(($orders / $visits) * 100, 2) : 0;
                        @endphp
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <div class="w-4 h-4 bg-purple-500 rounded-full"></div>
                                <span class="text-sm font-medium text-gray-900">{{ $data['campaign']->name }}</span>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-gray-900">{{ $conversionRate }}%</div>
                                <div class="text-xs text-gray-500">{{ number_format($orders) }} من {{ number_format($visits) }}</div>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-500 h-2 rounded-full transition-all duration-500" style="width: {{ min($conversionRate * 10, 100) }}%"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Metrics Comparison --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">مقارنة المقاييس التفصيلية</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحملة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">زيارات</th> <!-- موحد -->
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عرض تفاصيل</th> <!-- موحد -->
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">طلبات اهتمام</th> <!-- موحد -->
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">واتساب</th> <!-- موحد -->
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اتصالات</th> <!-- موحد -->
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">معدل التحويل</th> <!-- موحد -->
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نقاط الأداء</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($comparisonData as $campaignId => $data)
                        @php
                            $metrics = $data['metrics'];
                            $visits = $metrics['visits'] ?? 0;
                            $orders = $metrics['orders'] ?? 0;
                            $conversionRate = $visits > 0 ? round(($orders / $visits) * 100, 2) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-purple-500 rounded-full ml-3"></div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $data['campaign']->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $data['campaign']->project->name ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                {{ number_format($metrics['visits'] ?? 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($metrics['views'] ?? 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($metrics['shows'] ?? 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                {{ number_format($metrics['orders'] ?? 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($metrics['whatsapp'] ?? 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($metrics['calls'] ?? 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $conversionRate >= 5 ? 'bg-green-100 text-green-800' : ($conversionRate >= 2 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $conversionRate }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-purple-600">
                                {{ number_format($data['performance_score'] ?? 0) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Campaign Details Comparison --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($comparisonData as $campaignId => $data)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h4 class="text-lg font-semibold text-gray-900">{{ $data['campaign']->name }}</h4>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $data['campaign']->status === 'active' ? 'bg-green-100 text-green-800' : ($data['campaign']->status === 'paused' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ $campaignStatuses[$data['campaign']->status] ?? $data['campaign']->status }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">{{ $data['campaign']->project->name ?? 'N/A' }}</p>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        {{-- Key Metrics --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-blue-50 rounded-lg">
                                <div class="text-xl font-bold text-blue-600">{{ number_format($data['metrics']['visits'] ?? 0) }}</div>
                                <div class="text-xs text-gray-600">زيارات</div>
                            </div>
                            <div class="text-center p-3 bg-purple-50 rounded-lg">
                                <div class="text-xl font-bold text-purple-600">{{ number_format($data['metrics']['orders'] ?? 0) }}</div>
                                <div class="text-xs text-gray-600">طلبات</div>
                            </div>
                        </div>

                        {{-- Campaign Info --}}
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">المصدر:</span>
                                <span class="font-medium">{{ $availableSources[$data['campaign']->source] ?? $data['campaign']->source }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">تاريخ البدء:</span>
                                <span class="font-medium">{{ $data['campaign']->start_date->format('Y/m/d') }}</span>
                            </div>
                            @if($data['campaign']->budget)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">الميزانية:</span>
                                    <span class="font-medium">{{ number_format($data['campaign']->budget) }} ر.س</span>
                                </div>
                            @endif
                            <div class="flex justify-between pt-2 border-t border-gray-200">
                                <span class="text-gray-600">نقاط الأداء:</span>
                                <span class="font-bold text-purple-600">{{ number_format($data['performance_score'] ?? 0) }}</span>
                            </div>
                        </div>

                        {{-- Quick Actions --}}
                        <div class="flex space-x-2 space-x-reverse pt-4 border-t border-gray-200">
                            <button wire:click="selectCampaign({{ $data['campaign']->id }})"
                                    class="flex-1 inline-flex justify-center items-center px-3 py-2 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                                عرض التفاصيل
                            </button>
                            <button wire:click="removeFromComparison({{ $data['campaign']->id }})" 
                                    class="inline-flex items-center px-3 py-2 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                إزالة
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@else
    {{-- No Comparison Data --}}
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد حملات للمقارنة</h3>
        <p class="mt-1 text-sm text-gray-500">اختر حملتين على الأقل لبدء المقارنة</p>
        <div class="mt-6">
            <button wire:click="openComparisonModal" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                اختيار الحملات للمقارنة
            </button>
        </div>
    </div>
@endif

@push('scripts')
<script>
function comparisonRadarChart() {
    return {
        chart: null,
        initChart() {
            const ctx = document.getElementById('campaignComparisonChart');
            if (!ctx) return;
            
            this.chart = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: ['زيارات', 'مشاهدات', 'عروض', 'طلبات', 'واتساب', 'مكالمات'],
                    datasets: []
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            rtl: true,
                            textDirection: 'rtl',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            rtl: true,
                            textDirection: 'rtl'
                        }
                    },
                    scales: {
                        r: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            pointLabels: {
                                font: {
                                    size: 12,
                                    weight: '500'
                                }
                            },
                            ticks: {
                                display: false
                            }
                        }
                    }
                }
            });
            
            // Update with current comparison data
            this.updateChart(@json($comparisonData ?? []));
        },
        updateChart(comparisonData) {
            if (!this.chart || !comparisonData) return;

            const colors = ['#4F46E5', '#10B981', '#F59E0B', '#3B82F6', '#8B5CF6'];
            const datasets = [];

            Object.values(comparisonData).forEach((campaign, index) => {
                const color = colors[index % colors.length];
                datasets.push({
                    label: campaign.campaign.name,
                    data: [
                        campaign.metrics.visits || 0,
                        campaign.metrics.views || 0,
                        campaign.metrics.shows || 0,
                        campaign.metrics.orders || 0,
                        campaign.metrics.whatsapp || 0,
                        campaign.metrics.calls || 0
                    ],
                    borderColor: color,
                    backgroundColor: color + '20',
                    borderWidth: 2,
                    pointBackgroundColor: color,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                });
            });

            this.chart.data.datasets = datasets;
            this.chart.update();
        }
    }
}

// Trigger chart update when comparison data changes
document.addEventListener('livewire:initialized', () => {
    Livewire.on('comparisonDataUpdated', (data) => {
        window.dispatchEvent(new CustomEvent('update-comparison-chart', { detail: data }));
    });
});
</script>
@endpush

