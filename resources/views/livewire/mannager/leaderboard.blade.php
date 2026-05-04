<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">لوحة المتصدرين</h1>
            <p class="text-sm text-gray-500 mt-1">ترتيب الموظفين بناءً على النتيجة المركبة المرجحة</p>
        </div>
        <div class="flex items-center gap-3">
            <select wire:model.live="selectedMonth"
                class="custom-select border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none">
                @foreach($availableMonths as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            <a href="{{ route('manager.targets') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-sliders-h"></i>
                إدارة الأهداف
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
            <i class="fas fa-check-circle text-green-500"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Weights Config (Compact) --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <div class="flex flex-wrap items-center gap-4">
            <span class="text-sm font-medium text-gray-700 shrink-0">الأوزان:</span>
            @foreach(['monthly_orders' => 'شهري', 'daily_orders' => 'يومي', 'reservations' => 'حجوزات', 'sales' => 'مبيعات'] as $key => $label)
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
                تطبيق
            </button>
            <span class="text-xs {{ abs(array_sum($weights) - 100) < 0.5 ? 'text-green-600' : 'text-red-600' }} font-medium">
                المجموع: {{ array_sum($weights) }}%
            </span>
        </div>
    </div>

    {{-- Leaderboard Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-4 py-3 text-center font-medium text-gray-600 w-12">#</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-600">الموظف</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600">النتيجة</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600">شهري</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600">يومي</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600">حجوزات</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600">مبيعات</th>
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
                                        <span class="text-xs font-semibold text-gray-900">{{ $p['current'] }}/{{ $p['target'] ?: '—' }}</span>
                                        @if($p['target'] > 0)
                                            <div class="w-14 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                                                <div class="h-full rounded-full transition-all duration-500"
                                                     style="width: {{ $p['percentage'] }}%; background: {{ $p['percentage'] >= 100 ? '#22C55E' : ($p['percentage'] >= 50 ? '#F97316' : '#3B82F6') }};"></div>
                                            </div>
                                            <span class="text-[10px] text-gray-400">{{ $p['percentage'] }}%</span>
                                        @else
                                            <span class="text-[10px] text-gray-300">لم يحدد</span>
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-trophy text-3xl block mb-2 opacity-20"></i>
                                لا يوجد موظفو مبيعات نشطون
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
