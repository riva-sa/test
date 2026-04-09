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
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fafafa',
                            100: '#f4f4f5',
                            200: '#e4e4e7',
                            300: '#d4d4d8',
                            400: '#a1a1aa',
                            500: '#71717a',
                            600: '#52525b',
                            700: '#3f3f46',
                            800: '#27272a',
                            900: '#18181b'
                        }
                    },
                    fontFamily: {
                        sans: ['IBM Plex Sans Arabic', 'IBM Plex Sans Arabic', 'sans-serif']
                    }
                }
            }
        }
    </script>

    @livewireStyles

    <style>
        :root {
            --manager-bg: #fafafa;
            --manager-surface: #ffffff;
            --manager-surface-soft: #f4f4f5;
            --manager-border: #e4e4e7;
            --manager-text: #18181b;
            --manager-muted: #71717a;
            --manager-primary: #27272a;
            --manager-primary-hover: #18181b;
            --manager-ring: rgba(63, 63, 70, 0.2);
            --manager-success: #16a34a;
            --manager-danger: #dc2626;
            --manager-warning: #d97706;
            --manager-info: #2563eb;
        }

        body {
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            background: #fff !important;
            color: var(--manager-text);
        }

        .manager-theme {
            /* background-color: var(--manager-bg); */
            color: var(--manager-text);
        }

        .manager-theme .bg-white {
            background-color: #fff !important;
        }

        .manager-theme .bg-gray-50 {
            background-color: #fff !important;
        }

        .manager-theme .border-gray-100,
        .manager-theme .border-gray-200,
        .manager-theme .border-gray-300 {
            border-color: var(--manager-border) !important;
        }

        .manager-theme .text-gray-400 {
            color: #a1a1aa !important;
        }

        .manager-theme .text-gray-500,
        .manager-theme .text-gray-600 {
            color: var(--manager-muted) !important;
        }

        .manager-theme .text-gray-700,
        .manager-theme .text-gray-800,
        .manager-theme .text-gray-900 {
            color: var(--manager-text) !important;
        }

        .manager-theme .shadow,
        .manager-theme .shadow-sm,
        .manager-theme .shadow-md,
        .manager-theme .shadow-lg,
        .manager-theme .shadow-xl {
            box-shadow: 0 1px 2px rgba(24, 24, 27, 0.04), 0 8px 20px rgba(24, 24, 27, 0.05) !important;
        }

        .manager-theme .rounded-lg {
            border-radius: 0.55rem !important;
        }

        .manager-theme .rounded-xl {
            border-radius: 1rem !important;
        }

        .manager-theme .bg-indigo-600,
        .manager-theme .bg-primary-500,
        .manager-theme .bg-primary-600 {
            background-color: var(--manager-primary) !important;
            color: #fafafa !important;
        }

        .manager-theme .hover\:bg-indigo-700:hover,
        .manager-theme .hover\:bg-primary-700:hover {
            background-color: var(--manager-primary-hover) !important;
        }

        .manager-theme .text-indigo-600,
        .manager-theme .text-primary-500,
        .manager-theme .text-primary-600 {
            color: #27272a !important;
        }

        .manager-theme .bg-indigo-50 {
            background-color: #f4f4f5 !important;
        }

        .manager-theme .text-indigo-800 {
            color: #27272a !important;
        }

        .manager-theme .bg-blue-50,
        .manager-theme .bg-blue-100,
        .manager-theme .bg-indigo-100,
        .manager-theme .bg-purple-100 {
            background-color: #f4f4f5 !important;
        }

        .manager-theme .text-blue-600,
        .manager-theme .text-blue-700,
        .manager-theme .text-indigo-700,
        .manager-theme .text-purple-600,
        .manager-theme .text-purple-700 {
            color: #3f3f46 !important;
        }

        .manager-theme .text-blue-800,
        .manager-theme .text-purple-800 {
            color: #27272a !important;
        }

        .manager-theme .border-blue-200,
        .manager-theme .border-indigo-200,
        .manager-theme .border-purple-200 {
            border-color: #e4e4e7 !important;
        }

        .manager-theme .bg-blue-500,
        .manager-theme .bg-blue-600 {
            background-color: var(--manager-primary) !important;
            color: #fafafa !important;
        }

        .manager-theme .hover\:bg-blue-700:hover,
        .manager-theme .hover\:bg-indigo-50:hover {
            background-color: #f4f4f5 !important;
        }

        .manager-theme .focus\:ring-indigo-500:focus,
        .manager-theme .focus\:ring-blue-500:focus,
        .manager-theme .focus\:ring-primary-500:focus,
        .manager-theme .focus\:ring-primary-600:focus {
            --tw-ring-color: var(--manager-ring) !important;
        }

        .manager-theme input,
        .manager-theme select,
        .manager-theme textarea {
            border-color: var(--manager-border);
            border-radius: 0.75rem;
            background-color: var(--manager-surface);
            color: var(--manager-text);
        }

        .manager-theme input:focus,
        .manager-theme select:focus,
        .manager-theme textarea:focus {
            border-color: #3f3f46;
            box-shadow: 0 0 0 3px var(--manager-ring);
            outline: none;
        }

        .manager-theme .sidebar-item {
            border: 1px solid transparent;
        }

        .manager-theme .sidebar-item:hover {
            background-color: var(--manager-surface-soft) !important;
            border-color: var(--manager-border);
        }

        .manager-theme .sidebar-item.active {
            background-color: #f4f4f5 !important;
            border-color: #d4d4d8;
            color: var(--manager-text) !important;
        }

        .custom-select {
            width: 100%;
            border: 1px solid var(--manager-border);
            border-radius: 0.75rem;
            background-color: var(--manager-surface);
            padding: 0.6rem 0.75rem;
            font-size: 0.875rem;
        }

        .custom-multiselect {
            width: 100%;
            border: 1px solid var(--manager-border);
            border-radius: 0.75rem;
            background-color: var(--manager-surface);
            min-height: 7.5rem;
            padding: 0.375rem;
            font-size: 0.875rem;
        }

        .notification-success,
        .notification-error,
        .notification-info {
            border: 1px solid;
            border-radius: 0.875rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            padding: 0.875rem 1rem;
            min-width: 18rem;
        }

        .notification-success {
            background-color: #f0fdf4;
            color: #166534;
            border-color: #bbf7d0;
        }

        .notification-error {
            background-color: #fef2f2;
            color: #991b1b;
            border-color: #fecaca;
        }

        .notification-info {
            background-color: #eff6ff;
            color: #1e3a8a;
            border-color: #bfdbfe;
        }

        .loading-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('styles')
</head>
<body class="manager-theme font-sans antialiased text-gray-900">
        <div class="flex h-screen overflow-hidden p-4 gap-4">
            <!-- Sidebar Component -->
            @livewire('Mannager.Partials.Sidebar')

            <!-- Main content -->
            <div class="flex flex-col flex-1 overflow-hidden">

                <!-- Main content area -->
                <main class="flex-1 overflow-y-auto rounded-xl bg-gray-50 border border-gray-200 shadow-sm">

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
                primary: '#27272a',
                secondary: '#10B981',
                accent: '#F59E0B',
                danger: '#EF4444',
                info: '#52525b',
                success: '#059669',
                warning: '#D97706',
                purple: '#8B5CF6',
                pink: '#EC4899',
                gray: '#71717a'
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

                    const gradient1 = createGradient(this.ctx, 'rgba(39, 39, 42, 0.24)', 'rgba(39, 39, 42, 0.04)');
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

                    const gradient = createGradient(this.ctx, chartColors.info, 'rgba(82, 82, 91, 0.1)');

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
    {{-- <script src="{{ asset('frontend/js/tracking.js') }}"></script> --}}
    {{-- inter scripts here --}}
    @stack('scripts')
    
</body>
</html>
