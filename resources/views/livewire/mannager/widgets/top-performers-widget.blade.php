<div class="bg-white shadow rounded-xl border border-gray-100">
    <div class="px-5 py-4 border-b border-gray-100">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800 flex items-center">
                <i class="fas fa-trophy h-5 w-5 ml-2 text-yellow-500 text-base"></i>
                {{ __('leaderboard.top_performers') }}
            </h3>
            <a href="{{ route('manager.leaderboard') }}" class="text-xs font-medium text-gray-500 hover:text-gray-800">
                {{ __('leaderboard.view_all') }}
            </a>
        </div>
    </div>
    <div class="px-5 py-3 divide-y divide-gray-50">
        @forelse($topPerformers as $rank => $entry)
            @php
                $rankNum = $rank + 1;
                $medalColors = [1 => 'text-yellow-500', 2 => 'text-gray-400', 3 => 'text-amber-600'];
                $name  = $entry['user']->name;
                $score = $entry['composite_score'];
            @endphp
            <div class="flex items-center gap-3 py-2.5">
                <div class="w-6 text-center shrink-0">
                    @if($rankNum <= 3)
                        <i class="fas fa-trophy {{ $medalColors[$rankNum] ?? 'text-gray-300' }} text-sm"></i>
                    @else
                        <span class="text-xs font-bold text-gray-400">{{ $rankNum }}</span>
                    @endif
                </div>
                <div class="h-8 w-8 rounded-full bg-gray-900 text-white flex items-center justify-center text-xs font-bold shrink-0">
                    {{ mb_substr($name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0 text-right">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $name }}</p>
                    <div class="mt-0.5 w-full h-1 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full rounded-full bg-green-500" style="width: {{ min(100, $score) }}%"></div>
                    </div>
                </div>
                <span class="text-sm font-bold text-gray-700 shrink-0">{{ $score }}</span>
            </div>
        @empty
            <div class="py-12 text-center text-gray-400">
                <i class="fas fa-users-slash text-2xl block mb-2 opacity-20"></i>
                <span class="text-xs">{{ __('leaderboard.no_data_for_date') }}</span>
            </div>
        @endforelse
    </div>
</div>
