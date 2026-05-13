<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('leaderboard.title') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('leaderboard.subtitle') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            {{-- Month selector --}}
            <select wire:model.live="selectedMonth"
                class="custom-select border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none">
                @foreach($availableMonths as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            {{-- Date picker for daily view --}}
            <input type="date"
                wire:model.live="selectedDate"
                max="{{ now()->toDateString() }}"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400"
                title="{{ __('leaderboard.select_date') }}">

            @if($isAdmin || auth()->user()->hasRole('sales_manager'))
            <button wire:click="refreshLeaderboard" wire:loading.attr="disabled"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
                <i class="fas fa-sync-alt" wire:loading.class="fa-spin"></i>
                <span wire:loading.remove>{{ __('leaderboard.refresh') ?? 'تحديث البيانات' }}</span>
                <span wire:loading>{{ __('leaderboard.refreshing') ?? 'جاري التحديث...' }}</span>
            </button>

            <button wire:click="openHistoryModal"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-history"></i>
                {{ __('leaderboard.adjustment_history') }}
            </button>

            <a href="{{ route('manager.targets') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-sliders-h"></i>
                {{ __('leaderboard.manage_targets') }}
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
            <i class="fas fa-check-circle text-green-500"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
            <i class="fas fa-exclamation-circle text-red-500"></i>{{ session('error') }}
        </div>
    @endif

    @if($isAdmin || auth()->user()->hasRole('sales_manager'))
    {{-- Weights Config (Compact) --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <div class="flex flex-wrap items-center gap-4">
            <span class="text-sm font-medium text-gray-700 shrink-0">{{ __('leaderboard.weights') }}:</span>
            @foreach(['monthly_orders' => __('leaderboard.monthly'), 'daily_orders' => __('leaderboard.daily'), 'reservations' => __('leaderboard.reservations'), 'sales' => __('leaderboard.sales')] as $key => $label)
                <div class="flex items-center gap-1.5">
                    <span class="text-xs text-gray-500">{{ $label }}</span>
                    <div class="relative">
                        <input type="number" wire:model="weights.{{ $key }}"
                            min="0" max="100" step="0.5"
                            class="w-30 px-2 py-1 px-5 border border-gray-200 rounded text-xs text-center focus:outline-none focus:ring-1 focus:ring-gray-400">
                        <span class="absolute left-1.5 top-1/2 -translate-y-1/2 text-[10px] text-gray-400">%</span>
                    </div>
                </div>
            @endforeach
            <button wire:click="saveWeights"
                class="px-3 py-1.5 text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors">
                {{ __('leaderboard.apply') }}
            </button>
            <span class="text-xs {{ abs(array_sum($weights) - 100) < 0.5 ? 'text-green-600' : 'text-red-600' }} font-medium">
                {{ __('leaderboard.total') }}: {{ array_sum($weights) }}%
            </span>
        </div>
    </div>
    @endif

    {{-- Score Explanation for non-admins --}}
    @if(!($isAdmin || auth()->user()->hasRole('sales_manager')))
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
        <div class="text-sm">
            <p class="font-bold text-blue-900 mb-1">{{ __('leaderboard.how_it_works') ?? 'كيف تُحسب النقاط؟' }}</p>
            <p class="text-blue-800 opacity-80">
                {{ __('leaderboard.score_explanation') ?? 'تُحسب النتيجة بناءً على الأوزان المحددة لكل مقياس (الطلبات الجديدة، المعاملات، والمبيعات). كلما اقتربت من تحقيق هدفك، زادت نقاطك.' }}
            </p>
            <div class="mt-2 flex flex-wrap gap-4 text-xs font-medium text-blue-700">
                @foreach(['monthly_orders' => __('leaderboard.monthly'), 'daily_orders' => __('leaderboard.daily'), 'reservations' => __('leaderboard.reservations'), 'sales' => __('leaderboard.sales')] as $key => $label)
                    <span>{{ $label }}: {{ $weights[$key] ?? 25 }}%</span>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Leaderboard Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-4 py-3 text-center font-medium text-gray-600 w-12">#</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-600">{{ __('leaderboard.employee') }}</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600">{{ __('leaderboard.score') }}</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600">{{ __('leaderboard.monthly') }}</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600">{{ __('leaderboard.daily') }}</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600">{{ __('leaderboard.reservations') }}</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600">{{ __('leaderboard.sales') }}</th>
                        @if($isAdmin)
                            <th class="px-4 py-3 text-center font-medium text-gray-600">{{ __('leaderboard.actions') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($leaderboard as $rank => $entry)
                        @php
                            $rankNum = $rank + 1;
                            $medalColors = [1 => 'text-yellow-500', 2 => 'text-gray-400', 3 => 'text-amber-600'];
                        @endphp
                        <tr class="hover:bg-gray-50/50 {{ $rankNum <= 3 ? 'bg-gradient-to-r from-gray-50/30 to-transparent' : '' }}">
                            {{-- Rank --}}
                            <td class="px-4 py-4 text-center">
                                @if($rankNum <= 3)
                                    <i class="fas fa-trophy {{ $medalColors[$rankNum] }} text-lg"></i>
                                @else
                                    <span class="text-gray-400 font-bold">{{ $rankNum }}</span>
                                @endif
                            </td>

                            {{-- Name --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-gray-900 text-white flex items-center justify-center text-sm font-bold shrink-0">
                                        {{ mb_substr($entry['user']->name, 0, 1) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $entry['user']->name }}</span>
                                </div>
                            </td>

                            {{-- Composite Score --}}
                            <td class="px-4 py-4 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span class="text-lg font-bold text-gray-900">{{ $entry['composite_score'] }}</span>
                                    <div class="mt-1 w-16 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-500"
                                             style="width: {{ min(100, $entry['composite_score']) }}%; background: #22C55E;"></div>
                                    </div>
                                </div>
                            </td>

                            {{-- Per-type progress --}}
                            @foreach(['monthly_orders', 'daily_orders', 'reservations', 'sales'] as $type)
                                @php $p = $entry['progress'][$type]; @endphp
                                <td class="px-4 py-4 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <button wire:click="openDetailsModal({{ $entry['user']->id }}, '{{ $type }}')"
                                                class="text-xs font-semibold text-gray-900 hover:text-blue-600 transition-colors underline decoration-dotted underline-offset-4">
                                            {{ $p['current'] }}/{{ $p['target'] ?: '—' }}
                                        </button>
                                        @if($p['target'] > 0)
                                            <div class="w-14 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                                                <div class="h-full rounded-full transition-all duration-500"
                                                     style="width: {{ $p['percentage'] }}%; background: {{ $p['percentage'] >= 100 ? '#22C55E' : ($p['percentage'] >= 50 ? '#F97316' : '#3B82F6') }};"></div>
                                            </div>
                                            <span class="text-[10px] text-gray-400">{{ $p['percentage'] }}%</span>
                                        @else
                                            <span class="text-[10px] text-gray-300">{{ __('leaderboard.not_set') }}</span>
                                        @endif
                                    </div>
                                </td>
                            @endforeach

                            {{-- Admin Edit --}}
                            @if($isAdmin)
                                <td class="px-4 py-4 text-center">
                                    <button
                                        wire:click="openAdjustmentModal({{ $entry['user']->id }}, '{{ $selectedDate }}')"
                                        class="inline-flex items-center gap-1 px-2 py-1 text-xs text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                                        title="{{ __('leaderboard.adjust_points') }}">
                                        <i class="fas fa-pencil-alt text-[10px]"></i>
                                        {{ __('leaderboard.edit') }}
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 8 : 7 }}" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-trophy text-3xl block mb-2 opacity-20"></i>
                                {{ __('leaderboard.no_data_for_date') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Transaction Details Modal --}}
    @if($showDetailsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="closeDetailsModal">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4 overflow-hidden flex flex-col max-h-[85vh]">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">{{ __('leaderboard.details') }}</h2>
                        <p class="text-sm text-gray-500">
                            {{ __('leaderboard.point_details_for', [
                                'metric' => __('leaderboard.' . str_replace('_orders', '', $detailsMetric)),
                                'user' => $detailsUserName
                            ]) }}
                        </p>
                    </div>
                    <button wire:click="closeDetailsModal" class="text-gray-400 hover:text-gray-600 p-2">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6">
                    <table class="w-full text-sm text-right">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3">{{ __('leaderboard.customer') }}</th>
                                <th class="px-4 py-3">{{ __('leaderboard.date') }}</th>
                                <th class="px-4 py-3">{{ __('leaderboard.status_change') }}</th>
                                <th class="px-4 py-3">{{ __('leaderboard.performed_by') }}</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($detailsData as $transition)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-gray-900">{{ $transition->order->name ?? '—' }}</span>
                                            <span class="text-xs text-gray-500">{{ $transition->order->phone ?? '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-gray-600">
                                        {{ $transition->created_at->format('Y/m/d H:i') }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-500">{{ \App\Models\UnitOrder::STATUS_LABELS[$transition->from_status] ?? $transition->from_status }}</span>
                                            <i class="fas fa-arrow-left text-[10px] text-gray-300"></i>
                                            <span class="px-2 py-0.5 rounded font-medium" style="background: {{ \App\Models\UnitOrder::STATUS_COLORS[$transition->to_status] ?? '#eee' }}20; color: {{ \App\Models\UnitOrder::STATUS_COLORS[$transition->to_status] ?? '#666' }}">
                                                {{ \App\Models\UnitOrder::STATUS_LABELS[$transition->to_status] ?? $transition->to_status }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-gray-600">
                                        {{ $transition->user->name ?? 'System' }}
                                    </td>
                                    <td class="px-4 py-4 text-left">
                                        <a href="/crm/orders/{{ $transition->unit_order_id }}" target="_blank"
                                           class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-12 text-center text-gray-400">
                                        <i class="fas fa-info-circle text-2xl block mb-2 opacity-20"></i>
                                        {{ __('leaderboard.no_points_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                    <button wire:click="closeDetailsModal"
                        class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        {{ __('leaderboard.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Admin Adjustment Modal --}}
    @if($isAdmin && $showAdjustmentModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="closeAdjustmentModal">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">{{ __('leaderboard.adjust_points') }}</h2>
                    <button wire:click="closeAdjustmentModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <p class="text-sm text-gray-600">
                    {{ __('leaderboard.adjusting_for') }}: <span class="font-semibold">{{ $editingUserName }}</span>
                </p>

                <div class="space-y-3">
                    {{-- Period type --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('leaderboard.period_type') }}</label>
                        <select wire:model="editingPeriodType" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                            <option value="daily">{{ __('leaderboard.daily') }}</option>
                            <option value="weekly">{{ __('leaderboard.weekly') }}</option>
                            <option value="monthly">{{ __('leaderboard.monthly') }}</option>
                        </select>
                    </div>

                    {{-- Period date --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('leaderboard.period_date') }}</label>
                        <input type="date" wire:model="editingPeriodDate"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                    </div>

                    {{-- Metric — changing this auto-refreshes the original count shown below --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('leaderboard.metric') }}</label>
                        <select wire:model.live="editingMetric" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                            <option value="monthly_orders">{{ __('leaderboard.monthly') }}</option>
                            <option value="daily_orders">{{ __('leaderboard.daily') }}</option>
                            <option value="reservations">{{ __('leaderboard.reservations') }}</option>
                            <option value="sales">{{ __('leaderboard.sales') }}</option>
                        </select>
                    </div>

                    {{-- Original count (read-only, updates when metric changes) --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('leaderboard.original_value') }}</label>
                        <input type="number" value="{{ $editingOriginalValue }}" readonly
                            class="w-full border border-gray-100 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                    </div>

                    {{-- New count — enter the actual number, the score is recalculated automatically --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('leaderboard.new_value') }}</label>
                        <input type="number" wire:model="editingAdjustedValue" min="0" step="1"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                        <p class="mt-1 text-[10px] text-gray-400">{{ __('leaderboard.count_hint') }}</p>
                    </div>

                    {{-- Reason --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('leaderboard.reason') }} <span class="text-red-500">*</span></label>
                        <textarea wire:model="editingReason" rows="3"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400 resize-none"
                            placeholder="{{ __('leaderboard.reason_placeholder') }}"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button wire:click="saveAdjustment"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors">
                        {{ __('leaderboard.save') }}
                    </button>
                    <button wire:click="closeAdjustmentModal"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        {{ __('leaderboard.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Adjustment History Modal --}}
    @if($showHistoryModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="closeHistoryModal">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl mx-4 overflow-hidden flex flex-col max-h-[85vh]">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h2 class="text-lg font-bold text-gray-900">{{ __('leaderboard.adjustment_history') }}</h2>
                    <button wire:click="closeHistoryModal" class="text-gray-400 hover:text-gray-600 p-2">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6">
                    <table class="w-full text-sm text-right">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3">{{ __('leaderboard.adjustment_date') }}</th>
                                <th class="px-4 py-3">{{ __('leaderboard.employee') }}</th>
                                <th class="px-4 py-3">{{ __('leaderboard.metric_type') }}</th>
                                <th class="px-4 py-3">{{ __('leaderboard.change') }}</th>
                                <th class="px-4 py-3">{{ __('leaderboard.reason') }}</th>
                                <th class="px-4 py-3">{{ __('leaderboard.admin') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($adjustmentHistory as $adj)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4 text-gray-600">
                                        {{ $adj->created_at->format('Y/m/d H:i') }}
                                        <div class="text-[10px] text-gray-400">{{ $adj->period_date->format('Y/m/d') }} ({{ __('leaderboard.' . $adj->period_type) }})</div>
                                    </td>
                                    <td class="px-4 py-4 font-medium text-gray-900">
                                        {{ $adj->agent->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-4">
                                        {{ __('leaderboard.' . str_replace('_orders', '', $adj->metric_type)) }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-gray-400">{{ $adj->original_value }}</span>
                                            <i class="fas fa-arrow-left text-[10px] text-gray-300"></i>
                                            <span class="font-bold text-gray-900">{{ $adj->adjusted_value }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-gray-600 max-w-xs">
                                        {{ $adj->reason }}
                                    </td>
                                    <td class="px-4 py-4 text-gray-500">
                                        {{ $adj->admin->name ?? 'System' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                                        <i class="fas fa-history text-2xl block mb-2 opacity-20"></i>
                                        {{ __('leaderboard.no_data_for_date') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                    <button wire:click="closeHistoryModal"
                        class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        {{ __('leaderboard.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
