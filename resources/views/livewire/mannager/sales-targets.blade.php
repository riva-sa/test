<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">أهداف المبيعات</h1>
            <p class="text-sm text-gray-500 mt-1">حدد أهداف كل موظف عبر جميع الأنواع</p>
        </div>
        <a href="{{ route('manager.leaderboard') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors">
            <i class="fas fa-trophy"></i>
            لوحة المتصدرين
        </a>
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

    {{-- Leaderboard Weights --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-900">أوزان لوحة المتصدرين</h2>
                <p class="text-xs text-gray-400 mt-0.5">يجب أن يكون المجموع 100%</p>
            </div>
            <button wire:click="saveWeights"
                class="px-4 py-1.5 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                حفظ الأوزان
            </button>
        </div>
        <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach(['monthly_orders' => 'الطلبات الشهرية', 'daily_orders' => 'الطلبات اليومية', 'reservations' => 'الحجوزات', 'sales' => 'المبيعات'] as $key => $label)
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ $label }}</label>
                    <div class="relative">
                        <input type="number" wire:model="weights.{{ $key }}"
                            min="0" max="100" step="0.5"
                            class="w-full px-3 py-2 px-8 border border-gray-200 rounded-lg text-sm text-center focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">%</span>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-sm">
            <span class="text-gray-500">المجموع الحالي:</span>
            <span class="{{ abs(array_sum($weights) - 100) < 0.5 ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold' }}">
                {{ array_sum($weights) }}%
            </span>
        </div>
    </div>

    {{-- Targets Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-base font-semibold text-gray-900">أهداف الموظفين</h2>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">القيمة الافتراضية:</label>
                    <input type="number" wire:model="defaultValue" min="0"
                        class="w-24 px-3 py-1.5 border border-gray-200 rounded-lg text-sm text-center focus:outline-none">
                    <button wire:click="applyDefaultToAll"
                        class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-200 rounded-lg hover:bg-gray-200 transition-colors">
                        تطبيق للكل
                    </button>
                </div>
                <button wire:click="saveTargets"
                    class="px-4 py-1.5 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors">
                    <i class="fas fa-save ml-1"></i>حفظ الأهداف
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-3 text-right font-medium text-gray-600">الموظف</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600">الطلبات الشهرية</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600">الطلبات اليومية</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600">الحجوزات</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600">المبيعات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($targets as $userId => $rep)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-gray-900 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                        {{ mb_substr($rep['name'], 0, 1) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $rep['name'] }}</span>
                                </div>
                            </td>
                            @foreach(['monthly_orders', 'daily_orders', 'reservations', 'sales'] as $type)
                                <td class="px-4 py-3">
                                    <input type="number" wire:model="targets.{{ $userId }}.{{ $type }}"
                                        min="0"
                                        class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm text-center focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                                لا يوجد موظفو مبيعات نشطون
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
