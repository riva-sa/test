<div>
    <div class="mb-6">
        <h1 class="text-xl font-black text-gray-900">المشاريع والوحدات</h1>
        <p class="text-sm text-gray-500 mt-1">تصفح المشاريع المتاحة واختر منها عند إرسال عملائك</p>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="col-span-2 lg:col-span-2 relative">
                <i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
                <input type="text" wire:model.live.debounce.400ms="search"
                       class="w-full pr-10 pl-4 py-2.5 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm"
                       placeholder="ابحث باسم المشروع...">
            </div>
            <select wire:model.live="cityFilter" class="px-3 py-2.5 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm">
                <option value="">كل المدن</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="developerFilter" class="px-3 py-2.5 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm">
                <option value="">كل المطورين</option>
                @foreach ($developers as $developer)
                    <option value="{{ $developer->id }}">{{ $developer->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="unitTypeFilter" class="px-3 py-2.5 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm">
                <option value="">كل أنواع الوحدات</option>
                @foreach ($unitTypes as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
            <div class="flex gap-2 col-span-2 lg:col-span-1">
                <input type="number" wire:model.live.debounce.600ms="minPrice" class="w-1/2 px-3 py-2.5 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm" placeholder="من سعر">
                <input type="number" wire:model.live.debounce.600ms="maxPrice" class="w-1/2 px-3 py-2.5 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm" placeholder="إلى سعر">
            </div>
        </div>
        <div class="mt-3 flex justify-end">
            <button wire:click="resetFilters" class="text-xs font-bold text-gray-400 hover:text-gray-900 transition-colors">
                <i class="fas fa-rotate-right ml-1"></i> إعادة تعيين الفلاتر
            </button>
        </div>
    </div>

    {{-- Projects grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($projects as $project)
            <a href="{{ route('broker.projects.show', $project->id) }}" class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-lg hover:border-gray-200 transition-all group">
                <div class="h-44 bg-gray-100 overflow-hidden">
                    @php
                        $media = $project->getMainImages() ?? $project->projectMedia->where('media_type', 'image')->first();
                    @endphp
                    @if ($media)
                        <img src="{{ \App\Helpers\MediaHelper::getUrl($media->media_url) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $project->name }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                            <i class="fas fa-building text-4xl"></i>
                        </div>
                    @endif
                </div>
                <div class="p-5">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <h3 class="text-[15px] font-black text-gray-900">{{ $project->name }}</h3>
                        @if ($project->is_featured)
                            <span class="px-2 py-0.5 bg-yellow-50 text-yellow-700 text-[9px] font-black rounded-full whitespace-nowrap">مميز</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 text-[11px] text-gray-400 font-bold mb-3">
                        <span><i class="fas fa-location-dot ml-1"></i>{{ $project->city->name ?? '—' }}</span>
                        <span><i class="fas fa-helmet-safety ml-1"></i>{{ $project->developer->name ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                        <span class="text-[11px] font-bold text-gray-500">
                            {{ $project->available_units_count }} وحدة متاحة من {{ $project->units_count }}
                        </span>
                        @if ($project->show_price && $project->price)
                            <span class="text-[13px] font-black text-gray-900">{{ number_format((float) $project->price) }} ر.س</span>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full p-12 text-center text-sm text-gray-400 bg-white rounded-2xl border border-gray-100">
                لا توجد مشاريع مطابقة لبحثك
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $projects->links() }}
    </div>
</div>
