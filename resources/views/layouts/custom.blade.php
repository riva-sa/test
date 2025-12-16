<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ريفا') }} - لوحة التحكم</title>

    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="shortcut icon" href="{{ asset('frontend/img/logoyy.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@100;200;300;400;500;600;700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Styles -->
    @livewireStyles

    <!-- Scripts -->

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#122818',
                            100: '#122818',
                            200: '#122818',
                            300: '#122818',
                            400: '#122818',
                            500: '#122818',
                            600: '#122818',
                            700: '#122818',
                            800: '#122818',
                            900: '#122818',
                        },
                    },
                    fontFamily: {
                        sans: ['IBM Plex Sans Arabic', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    <style>
        .dashboard-card {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        .sidebar-item:hover {
            background-color: rgba(18, 40, 24, 0.1);
        }
        .sidebar-item.active {
            background-color: rgb(249, 250, 251);
        }
        .form-input:focus {
            border-color: #122818;
            box-shadow: 0 0 0 3px rgba(18, 40, 24, 0.15);
        }
        .btn-primary {
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
        }
    </style>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <!-- Chart.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
        <!-- ApexCharts -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.5/apexcharts.min.js"></script>
        <!-- Custom styles -->
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap');

            body {
                font-family: 'Tajawal', sans-serif;
                background-color: #f3f4f6;
            }

            .dashboard-card {
                transition: all 0.3s ease;
            }

            .bg-primary-500 {
                background-color: #4f46e5;
            }

            .text-primary-500 {
                color: #4f46e5;
            }

            .text-primary-600 {
                color: #4338ca;
            }

            .hover\:text-primary-900:hover {
                color: #312e81;
            }

            .bg-blue-100 {
                background-color: #dbeafe;
            }

            .text-blue-800 {
                color: #1e40af;
            }

            .bg-yellow-100 {
                background-color: #fef3c7;
            }

            .text-yellow-800 {
                color: #92400e;
            }

            .bg-green-100 {
                background-color: #d1fae5;
            }

            .text-green-800 {
                color: #065f46;
            }

            .bg-gray-100 {
                background-color: #f3f4f6;
            }

            .text-gray-800 {
                color: #1f2937;
            }

            .bg-blue-500 {
                background-color: #3b82f6;
            }

            .bg-yellow-500 {
                background-color: #eab308;
            }

            .bg-green-500 {
                background-color: #22c55e;
            }

            .bg-gray-500 {
                background-color: #6b7280;
            }

            /* Custom styles for charts */
            .chart-container {
                position: relative;
                min-height: 300px;
            }
        </style>
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar Component -->
            @livewire('Mannager.Partials.Sidebar')

            <!-- Main content -->
            <div class="flex flex-col flex-1 overflow-hidden">

                <!-- Main content area -->
                <main class="flex-1 overflow-y-auto bg-gray-50">

                    {{ $slot }}

                </main>

            </div>
        </div>

        @livewireScripts
        <script>
            Chart.defaults.font.family = 'Cairo, Tajawal, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
            Chart.defaults.plugins.legend.rtl = true;
            Chart.defaults.plugins.legend.textDirection = 'rtl';

            // Arabic number formatter
            function formatArabicNumber(num) {
                return new Intl.NumberFormat('ar-SA').format(num);
            }

            // Arabic date formatter
            function formatArabicDate(date) {
                return new Intl.DateTimeFormat('ar-SA', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                }).format(new Date(date));
            }

            // Color palette for charts
            const chartColors = {
                primary: '#4F46E5',
                secondary: '#10B981',
                accent: '#F59E0B',
                danger: '#EF4444',
                info: '#3B82F6',
                success: '#059669',
                warning: '#D97706',
                purple: '#8B5CF6',
                pink: '#EC4899',
                gray: '#6B7280'
            };

            // Chart gradients
            function createGradient(ctx, color1, color2) {
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, color1);
                gradient.addColorStop(1, color2);
                return gradient;
            }

            // Daily Performance Chart
            class DailyPerformanceChart {
                constructor(canvasId) {
                    this.canvas = document.getElementById(canvasId);
                    this.ctx = this.canvas.getContext('2d');
                    this.chart = null;
                    this.init();
                }

                init() {
                    if (!this.canvas) return;

                    const gradient1 = createGradient(this.ctx, 'rgba(79, 70, 229, 0.3)', 'rgba(79, 70, 229, 0.05)');
                    const gradient2 = createGradient(this.ctx, 'rgba(16, 185, 129, 0.3)', 'rgba(16, 185, 129, 0.05)');
                    const gradient3 = createGradient(this.ctx, 'rgba(245, 158, 11, 0.3)', 'rgba(245, 158, 11, 0.05)');

                    this.chart = new Chart(this.ctx, {
                        type: 'line',
                        data: {
                            labels: [],
                            datasets: [
                                {
                                    label: 'زيارات',
                                    data: [],
                                    borderColor: chartColors.primary,
                                    backgroundColor: gradient1,
                                    borderWidth: 3,
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: chartColors.primary,
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointRadius: 5,
                                    pointHoverRadius: 7
                                },
                                {
                                    label: 'مشاهدات',
                                    data: [],
                                    borderColor: chartColors.secondary,
                                    backgroundColor: gradient2,
                                    borderWidth: 3,
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: chartColors.secondary,
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointRadius: 5,
                                    pointHoverRadius: 7
                                },
                                {
                                    label: 'طلبات',
                                    data: [],
                                    borderColor: chartColors.accent,
                                    backgroundColor: gradient3,
                                    borderWidth: 3,
                                    fill: false,
                                    tension: 0.4,
                                    pointBackgroundColor: chartColors.accent,
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointRadius: 5,
                                    pointHoverRadius: 7
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    align: 'end',
                                    rtl: true,
                                    textDirection: 'rtl',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20,
                                        font: {
                                            size: 12,
                                            weight: '500'
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
                                    displayColors: true,
                                    rtl: true,
                                    textDirection: 'rtl',
                                    callbacks: {
                                        title: function(context) {
                                            return formatArabicDate(context[0].label);
                                        },
                                        label: function(context) {
                                            return context.dataset.label + ': ' + formatArabicNumber(context.parsed.y);
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 11
                                        },
                                        callback: function(value, index, values) {
                                            const date = this.getLabelForValue(value);
                                            return formatArabicDate(date);
                                        }
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)'
                                    },
                                    ticks: {
                                        font: {
                                            size: 11
                                        },
                                        callback: function(value) {
                                            return formatArabicNumber(value);
                                        }
                                    }
                                }
                            },
                            elements: {
                                point: {
                                    hoverBackgroundColor: '#fff'
                                }
                            }
                        }
                    });
                }

                updateData(data) {
                    if (!this.chart || !data) return;

                    const labels = data.map(item => item.date);
                    const visits = data.map(item => item.visit || 0);
                    const views = data.map(item => item.view || 0);
                    const orders = data.map(item => item.order || 0);

                    this.chart.data.labels = labels;
                    this.chart.data.datasets[0].data = visits;
                    this.chart.data.datasets[1].data = views;
                    this.chart.data.datasets[2].data = orders;

                    this.chart.update('active');
                }

                destroy() {
                    if (this.chart) {
                        this.chart.destroy();
                    }
                }
            }

            // Conversion Funnel Chart
            class ConversionFunnelChart {
                constructor(canvasId) {
                    this.canvas = document.getElementById(canvasId);
                    this.ctx = this.canvas.getContext('2d');
                    this.chart = null;
                    this.init();
                }

                init() {
                    if (!this.canvas) return;

                    this.chart = new Chart(this.ctx, {
                        type: 'bar',
                        data: {
                            labels: [],
                            datasets: [{
                                label: 'العدد',
                                data: [],
                                backgroundColor: [
                                    chartColors.primary,
                                    chartColors.secondary,
                                    chartColors.accent,
                                    chartColors.info,
                                    chartColors.purple
                                ],
                                borderColor: [
                                    chartColors.primary,
                                    chartColors.secondary,
                                    chartColors.accent,
                                    chartColors.info,
                                    chartColors.purple
                                ],
                                borderWidth: 2,
                                borderRadius: 8,
                                borderSkipped: false
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    titleColor: '#fff',
                                    bodyColor: '#fff',
                                    borderColor: 'rgba(255, 255, 255, 0.1)',
                                    borderWidth: 1,
                                    cornerRadius: 8,
                                    rtl: true,
                                    textDirection: 'rtl',
                                    callbacks: {
                                        label: function(context) {
                                            const percentage = context.raw.percentage || 0;
                                            return context.dataset.label + ': ' + formatArabicNumber(context.parsed.x) + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)'
                                    },
                                    ticks: {
                                        font: {
                                            size: 11
                                        },
                                        callback: function(value) {
                                            return formatArabicNumber(value);
                                        }
                                    }
                                },
                                y: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 12,
                                            weight: '500'
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                updateData(data) {
                    if (!this.chart || !data) return;

                    const labels = data.map(item => item.stage);
                    const values = data.map(item => ({
                        ...item.count,
                        percentage: item.percentage
                    }));

                    this.chart.data.labels = labels;
                    this.chart.data.datasets[0].data = values;

                    this.chart.update('active');
                }

                destroy() {
                    if (this.chart) {
                        this.chart.destroy();
                    }
                }
            }

            // Hourly Activity Chart
            class HourlyActivityChart {
                constructor(canvasId) {
                    this.canvas = document.getElementById(canvasId);
                    this.ctx = this.canvas.getContext('2d');
                    this.chart = null;
                    this.init();
                }

                init() {
                    if (!this.canvas) return;

                    const gradient = createGradient(this.ctx, chartColors.info, 'rgba(59, 130, 246, 0.1)');

                    this.chart = new Chart(this.ctx, {
                        type: 'bar',
                        data: {
                            labels: Array.from({length: 24}, (_, i) => i + ':00'),
                            datasets: [{
                                label: 'النشاط',
                                data: Array(24).fill(0),
                                backgroundColor: gradient,
                                borderColor: chartColors.info,
                                borderWidth: 2,
                                borderRadius: 6,
                                borderSkipped: false
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    titleColor: '#fff',
                                    bodyColor: '#fff',
                                    borderColor: 'rgba(255, 255, 255, 0.1)',
                                    borderWidth: 1,
                                    cornerRadius: 8,
                                    rtl: true,
                                    textDirection: 'rtl',
                                    callbacks: {
                                        title: function(context) {
                                            return 'الساعة ' + context[0].label;
                                        },
                                        label: function(context) {
                                            return 'النشاط: ' + formatArabicNumber(context.parsed.y);
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 10
                                        },
                                        maxRotation: 0
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)'
                                    },
                                    ticks: {
                                        font: {
                                            size: 11
                                        },
                                        callback: function(value) {
                                            return formatArabicNumber(value);
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                updateData(data) {
                    if (!this.chart || !data) return;

                    const hourlyData = Array(24).fill(0);
                    data.forEach(item => {
                        if (item.hour !== undefined) {
                            hourlyData[item.hour] = (item.visit || 0) + (item.view || 0) + 
                                                (item.show || 0) + (item.order || 0) + 
                                                (item.whatsapp || 0) + (item.call || 0);
                        }
                    });

                    this.chart.data.datasets[0].data = hourlyData;
                    this.chart.update('active');
                }

                destroy() {
                    if (this.chart) {
                        this.chart.destroy();
                    }
                }
            }

            // Traffic Sources Pie Chart
            class TrafficSourcesChart {
                constructor(canvasId) {
                    this.canvas = document.getElementById(canvasId);
                    this.ctx = this.canvas.getContext('2d');
                    this.chart = null;
                    this.init();
                }

                init() {
                    if (!this.canvas) return;

                    this.chart = new Chart(this.ctx, {
                        type: 'doughnut',
                        data: {
                            labels: [],
                            datasets: [{
                                data: [],
                                backgroundColor: [
                                    chartColors.primary,
                                    chartColors.secondary,
                                    chartColors.accent,
                                    chartColors.info,
                                    chartColors.purple,
                                    chartColors.pink,
                                    chartColors.gray
                                ],
                                borderColor: '#fff',
                                borderWidth: 3,
                                hoverBorderWidth: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '60%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    rtl: true,
                                    textDirection: 'rtl',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 15,
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
                                    textDirection: 'rtl',
                                    callbacks: {
                                        label: function(context) {
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                                            return context.label + ': ' + formatArabicNumber(context.parsed) + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                updateData(data) {
                    if (!this.chart || !data) return;

                    const labels = data.map(item => item.source);
                    const values = data.map(item => item.count);

                    this.chart.data.labels = labels;
                    this.chart.data.datasets[0].data = values;

                    this.chart.update('active');
                }

                destroy() {
                    if (this.chart) {
                        this.chart.destroy();
                    }
                }
            }

            // Campaign Comparison Chart
            class CampaignComparisonChart {
                constructor(canvasId) {
                    this.canvas = document.getElementById(canvasId);
                    this.ctx = this.canvas.getContext('2d');
                    this.chart = null;
                    this.init();
                }

                init() {
                    if (!this.canvas) return;

                    this.chart = new Chart(this.ctx, {
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
                                    textDirection: 'rtl',
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': ' + formatArabicNumber(context.parsed.r);
                                        }
                                    }
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
                }

                updateData(campaigns) {
                    if (!this.chart || !campaigns) return;

                    const colors = [chartColors.primary, chartColors.secondary, chartColors.accent, chartColors.info, chartColors.purple];
                    const datasets = [];

                    campaigns.forEach((campaign, index) => {
                        const color = colors[index % colors.length];
                        datasets.push({
                            label: campaign.name,
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
                    this.chart.update('active');
                }

                destroy() {
                    if (this.chart) {
                        this.chart.destroy();
                    }
                }
            }

            // Global chart instances
            window.chartInstances = {};

            // Initialize charts when DOM is ready
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize charts based on available canvas elements
                if (document.getElementById('dailyPerformanceChart')) {
                    window.chartInstances.dailyPerformance = new DailyPerformanceChart('dailyPerformanceChart');
                }
                
                if (document.getElementById('conversionFunnelChart')) {
                    window.chartInstances.conversionFunnel = new ConversionFunnelChart('conversionFunnelChart');
                }
                
                if (document.getElementById('hourlyBreakdownChart')) {
                    window.chartInstances.hourlyActivity = new HourlyActivityChart('hourlyBreakdownChart');
                }
                
                if (document.getElementById('trafficSourcesChart')) {
                    window.chartInstances.trafficSources = new TrafficSourcesChart('trafficSourcesChart');
                }
                
                if (document.getElementById('campaignComparisonChart')) {
                    window.chartInstances.campaignComparison = new CampaignComparisonChart('campaignComparisonChart');
                }
            });

            // Livewire event listeners
            document.addEventListener('livewire:initialized', () => {
                // Listen for chart updates
                Livewire.on('updateCharts', (data) => {
                    if (data.daily_breakdown && window.chartInstances.dailyPerformance) {
                        window.chartInstances.dailyPerformance.updateData(data.daily_breakdown);
                    }
                    
                    if (data.conversion_funnel && window.chartInstances.conversionFunnel) {
                        window.chartInstances.conversionFunnel.updateData(data.conversion_funnel);
                    }
                    
                    if (data.hourly_breakdown && window.chartInstances.hourlyActivity) {
                        window.chartInstances.hourlyActivity.updateData(data.hourly_breakdown);
                    }
                    
                    if (data.traffic_sources && window.chartInstances.trafficSources) {
                        window.chartInstances.trafficSources.updateData(data.traffic_sources);
                    }
                });
                
                // Listen for comparison chart updates
                Livewire.on('updateComparisonChart', (campaigns) => {
                    if (window.chartInstances.campaignComparison) {
                        window.chartInstances.campaignComparison.updateData(campaigns);
                    }
                });
                
                // Clean up charts when component is destroyed
                Livewire.on('destroyCharts', () => {
                    Object.values(window.chartInstances).forEach(chart => {
                        if (chart && typeof chart.destroy === 'function') {
                            chart.destroy();
                        }
                    });
                    window.chartInstances = {};
                });
            });

            // Export for use in other files
            window.ChartClasses = {
                DailyPerformanceChart,
                ConversionFunnelChart,
                HourlyActivityChart,
                TrafficSourcesChart,
                CampaignComparisonChart
            };


    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js "></script>
    {{-- <script src="{{ asset('frontend/js/tracking.js') }}"></script> --}}
    {{-- inter scripts here --}}
    @stack('scripts')

</body>
</html>
