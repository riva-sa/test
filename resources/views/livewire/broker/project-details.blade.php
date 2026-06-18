<div>
    <a href="{{ route('broker.projects') }}" class="inline-flex items-center gap-2 text-xs font-bold text-gray-400 hover:text-gray-900 mb-4 transition-colors">
        <i class="fas fa-arrow-right"></i> العودة للمشاريع
    </a>

    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden mb-6">
        @php
            $images = $project->projectMedia->where('media_type', 'image')->values();
        @endphp
        @if ($images->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-1 h-56">
                @foreach ($images->take(4) as $i => $media)
                    <div class="{{ $i === 0 ? 'col-span-2 row-span-2' : '' }} bg-gray-100 overflow-hidden {{ $i > 0 && $loop->remaining >= 0 ? '' : '' }}">
                        <img src="{{ \App\Helpers\MediaHelper::getUrl($media->media_url) }}" class="w-full h-full object-cover" alt="{{ $project->name }}">
                    </div>
                @endforeach
            </div>
        @endif
        <div class="p-6">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div>
                    <h1 class="text-xl font-black text-gray-900">{{ $project->name }}</h1>
                    <div class="flex flex-wrap items-center gap-4 mt-2 text-[12px] text-gray-500 font-bold">
                        <span><i class="fas fa-location-dot ml-1 text-gray-300"></i>{{ $project->city->name ?? '—' }} {{ $project->state?->name ? '· '.$project->state->name : '' }}</span>
                        <span><i class="fas fa-helmet-safety ml-1 text-gray-300"></i>{{ $project->developer->name ?? '—' }}</span>
                        @if ($project->projectType)
                            <span><i class="fas fa-tag ml-1 text-gray-300"></i>{{ $project->projectType->name }}</span>
                        @endif
                        @if ($project->AdLicense)
                            <span><i class="fas fa-certificate ml-1 text-gray-300"></i>رخصة إعلان: {{ $project->AdLicense }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <button type="button" wire:click="downloadPriceList" wire:loading.attr="disabled" wire:target="downloadPriceList"
                       class="px-5 py-3 bg-white border border-gray-200 hover:border-gray-900 text-gray-700 hover:text-gray-900 text-sm font-black rounded-xl transition-all whitespace-nowrap disabled:opacity-50">
                        <i class="fas fa-file-pdf text-red-400 ml-2" wire:loading.remove wire:target="downloadPriceList"></i>
                        <i class="fas fa-spinner fa-spin ml-2" wire:loading wire:target="downloadPriceList"></i>
                        تحميل قائمة الأسعار
                    </button>
                    <a href="{{ route('broker.leads.create', ['project' => $project->id]) }}"
                       class="px-6 py-3 bg-gray-900 hover:bg-gray-800 text-white text-sm font-black rounded-xl transition-all whitespace-nowrap">
                        <i class="fas fa-user-plus ml-2"></i> إرسال عميل لهذا المشروع
                    </a>
                </div>
            </div>

            {{-- Per-project broker commission set by the admin --}}
            @if ($broker && (float) $project->commission_value > 0)
                <div class="flex items-center gap-3 mt-4 px-4 py-3 bg-primary-50 border border-primary-100 rounded-xl">
                    <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-primary-100 text-primary-600 shrink-0">
                        <i class="fas fa-percent"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[10px] font-black text-primary-500 uppercase tracking-wide">عمولتك على هذا المشروع</div>
                        <div class="text-[13px] font-black text-gray-900">{{ $project->commissionLabel() }}</div>
                    </div>
                </div>
            @endif

            @if ($project->description)
                <p class="text-sm text-gray-600 leading-relaxed mt-4">{!! nl2br(e(strip_tags($project->description))) !!}</p>
            @endif

            {{-- Attachments --}}
            @php
                $attachments = $project->projectMedia->whereIn('media_type', ['pdf', 'file', 'brochure'])->values();
            @endphp
            @if ($attachments->isNotEmpty())
                <div class="mt-5 pt-5 border-t border-gray-50">
                    <div class="text-[11px] font-black text-gray-400 uppercase mb-3">المرفقات</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($attachments as $attachment)
                            <a href="{{ \App\Helpers\MediaHelper::getUrl($attachment->media_url) }}" target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-100 text-gray-700 text-[12px] font-bold rounded-xl transition-all">
                                <i class="fas fa-file-pdf text-red-400"></i> تحميل المرفق
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Features · Guarantees · Landmarks (compact, side-by-side) --}}
    @if ($project->features->isNotEmpty() || $project->guarantees->isNotEmpty() || $project->landmarks->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-x-6 gap-y-4 divide-y lg:divide-y-0 lg:divide-x lg:divide-x-reverse divide-gray-100">

                {{-- Features --}}
                @if ($project->features->isNotEmpty())
                    <div class="pt-4 lg:pt-0 lg:px-4 first:pt-0">
                        <div class="flex items-center gap-1.5 mb-2.5">
                            <i class="fas fa-star text-primary-500 text-xs"></i>
                            <h2 class="text-[13px] font-black text-gray-900">مميزات المشروع</h2>
                        </div>
                        <div class="space-y-1.5">
                            @foreach ($project->features as $feature)
                                <div class="flex items-center gap-2">
                                    <span class="flex items-center justify-center w-6 h-6 rounded-md bg-primary-50 text-primary-600 shrink-0 overflow-hidden">
                                        @if ($feature->icon)
                                            <img src="{{ \App\Helpers\MediaHelper::getUrl($feature->icon) }}" class="w-4 h-4 object-contain" alt="{{ $feature->name }}">
                                        @else
                                            <i class="fas fa-check text-[9px]"></i>
                                        @endif
                                    </span>
                                    <div class="min-w-0">
                                        <span class="text-[12px] font-bold text-gray-800">{{ $feature->name }}</span>
                                        @if ($feature->description)
                                            <span class="text-[10px] text-gray-400"> — {{ strip_tags($feature->description) }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Guarantees --}}
                @if ($project->guarantees->isNotEmpty())
                    <div class="pt-4 lg:pt-0 lg:px-4 first:pt-0">
                        <div class="flex items-center gap-1.5 mb-2.5">
                            <i class="fas fa-shield-halved text-green-500 text-xs"></i>
                            <h2 class="text-[13px] font-black text-gray-900">ضمانات المشروع</h2>
                        </div>
                        <div class="space-y-1.5">
                            @foreach ($project->guarantees as $guarantee)
                                <div class="flex items-center gap-2">
                                    <span class="flex items-center justify-center w-6 h-6 rounded-md bg-green-50 text-green-600 shrink-0 overflow-hidden">
                                        @if ($guarantee->icon)
                                            <img src="{{ \App\Helpers\MediaHelper::getUrl($guarantee->icon) }}" class="w-4 h-4 object-contain" alt="{{ $guarantee->name }}">
                                        @else
                                            <i class="fas fa-shield-halved text-[9px]"></i>
                                        @endif
                                    </span>
                                    <div class="min-w-0">
                                        <span class="text-[12px] font-bold text-gray-800">{{ $guarantee->name }}</span>
                                        @if ($guarantee->description)
                                            <span class="text-[10px] text-gray-400"> — {{ strip_tags($guarantee->description) }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Nearby landmarks --}}
                @if ($project->landmarks->isNotEmpty())
                    <div class="pt-4 lg:pt-0 lg:px-4 first:pt-0">
                        <div class="flex items-center gap-1.5 mb-2.5">
                            <i class="fas fa-location-dot text-amber-500 text-xs"></i>
                            <h2 class="text-[13px] font-black text-gray-900">المعالم القريبة</h2>
                        </div>
                        <div class="space-y-1.5">
                            @foreach ($project->landmarks as $landmark)
                                <div class="flex items-center gap-2">
                                    <span class="flex items-center justify-center w-6 h-6 rounded-md bg-amber-50 text-amber-600 shrink-0">
                                        <i class="fas fa-map-pin text-[9px]"></i>
                                    </span>
                                    <div class="min-w-0 flex items-center gap-1.5">
                                        <span class="text-[12px] font-bold text-gray-800">{{ $landmark->name }}</span>
                                        @if ($landmark->pivot->distance ?? $landmark->distance)
                                            <span class="px-1.5 py-px bg-amber-100 text-amber-700 text-[9px] font-black rounded-full whitespace-nowrap">{{ $landmark->pivot->distance ?? $landmark->distance }} كم</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    @endif

    {{-- Units --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
            <h2 class="text-base font-black text-gray-900">الوحدات</h2>
            <div class="flex flex-wrap gap-2">
                <select wire:model.live="unitTypeFilter" class="px-3 py-2 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-xs font-bold">
                    <option value="">كل الأنواع</option>
                    @foreach ($unitTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse ($units as $unit)
                @php
                    $gallery = collect();
                    if ($unit->image) {
                        $gallery->push(['url' => \App\Helpers\MediaHelper::getUrl($unit->image), 'is_plan' => false]);
                    }
                    foreach ((array) $unit->images as $img) {
                        if (! empty($img)) {
                            $gallery->push(['url' => \App\Helpers\MediaHelper::getUrl($img), 'is_plan' => false]);
                        }
                    }
                    if ($unit->floor_plan) {
                        $gallery->push(['url' => \App\Helpers\MediaHelper::getUrl($unit->floor_plan), 'is_plan' => true]);
                    }
                    $gallery = $gallery->values();
                @endphp
                <div class="border border-gray-100 rounded-2xl overflow-hidden hover:border-gray-200 transition-all"
                     x-data="{ active: 0, images: {{ \Illuminate\Support\Js::from($gallery) }} }">
                    <div class="h-32 bg-gray-100 relative">
                        @if ($gallery->isNotEmpty())
                            <img src="{{ $gallery->first()['url'] }}" :src="images[active].url" class="w-full h-full object-cover" alt="{{ $unit->title }}">
                            <span x-show="images[active].is_plan" x-cloak class="absolute top-2 left-2 px-2.5 py-1 text-[10px] font-black rounded-full bg-blue-500 text-white">
                                <i class="fas fa-ruler-combined ml-1"></i>مخطط
                            </span>
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300"><i class="fas fa-house text-2xl"></i></div>
                        @endif
                        <span class="absolute top-2 right-2 px-2.5 py-1 text-[10px] font-black rounded-full text-white
                            {{ $unit->case == 0 ? 'bg-green-500' : ($unit->case == 1 ? 'bg-yellow-500' : 'bg-red-500') }}">
                            {{ $unit->case == 0 ? 'متاحة' : ($unit->case == 1 ? 'محجوزة' : 'مباعة') }}
                        </span>
                        @if ($unit->floor_plan)
                            <a href="{{ route('broker.units.floor-plan', $unit->id) }}"
                               class="absolute bottom-2 left-2 z-10 inline-flex items-center gap-1 px-2.5 py-1 bg-white/90 hover:bg-white text-gray-700 text-[10px] font-black rounded-lg shadow-sm transition-all">
                                <i class="fas fa-download text-blue-500"></i> تحميل المخطط
                            </a>
                        @endif
                    </div>
                    @if ($gallery->count() > 1)
                        <div class="flex gap-1.5 p-2 overflow-x-auto bg-gray-50/50">
                            @foreach ($gallery as $i => $g)
                                <button type="button" @click="active = {{ $i }}"
                                    class="relative shrink-0 w-12 h-12 rounded-lg overflow-hidden border-2 transition-all"
                                    :class="active === {{ $i }} ? 'border-gray-900' : 'border-transparent opacity-60 hover:opacity-100'">
                                    <img src="{{ $g['url'] }}" class="w-full h-full object-cover" alt="">
                                    @if ($g['is_plan'])
                                        <span class="absolute inset-x-0 bottom-0 bg-blue-500 text-white text-[7px] font-black text-center leading-tight py-px">مخطط</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    @endif
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-[13px] font-black text-gray-900">{{ $unit->title }}</h3>
                            <span class="text-[10px] font-bold text-gray-400">{{ $unit->unit_type }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-[10px] text-gray-400 font-bold mb-3">
                            @if ($unit->unit_area) <span><i class="fas fa-ruler-combined ml-1"></i>{{ $unit->unit_area }} م²</span> @endif
                            @if ($unit->beadrooms) <span><i class="fas fa-bed ml-1"></i>{{ $unit->beadrooms }}</span> @endif
                            @if ($unit->bathrooms) <span><i class="fas fa-bath ml-1"></i>{{ $unit->bathrooms }}</span> @endif
                            @if ($unit->floor) <span><i class="fas fa-stairs ml-1"></i>دور {{ $unit->floor }}</span> @endif
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                            <div>
                                @if ($unit->show_price && $unit->unit_price)
                                    <span class="text-[14px] font-black text-gray-900">{{ number_format((float) $unit->unit_price) }} ر.س</span>
                                @else
                                    <span class="text-[11px] font-bold text-gray-400">السعر عند الطلب</span>
                                @endif
                                @if ($broker && (float) $project->commission_value > 0 && ($project->isFixedCommission() || ($unit->show_price && $unit->unit_price)))
                                    <div class="text-[10px] font-bold text-primary-600 mt-0.5">
                                        <i class="fas fa-percent ml-1"></i>عمولتك: {{ number_format($project->commissionForPrice($unit->unit_price)) }} ر.س
                                    </div>
                                @endif
                            </div>
                            @if ($unit->case == 0)
                                <a href="{{ route('broker.leads.create', ['project' => $project->id, 'unit' => $unit->id]) }}"
                                   class="px-3 py-1.5 bg-gray-900 hover:bg-gray-800 text-white text-[10px] font-black rounded-lg transition-all">
                                    إرسال عميل
                                </a>
                            @else
                                <span class="px-3 py-1.5 text-[10px] font-black rounded-lg
                                    {{ $unit->case == 1 ? 'bg-yellow-50 text-yellow-600' : 'bg-red-50 text-red-500' }}">
                                    {{ $unit->case == 1 ? 'محجوزة' : 'غير متاحة' }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-10 text-center text-sm text-gray-400">لا توجد وحدات مطابقة</div>
            @endforelse
        </div>

        <div class="mt-5">
            {{ $units->links() }}
        </div>
    </div>
</div>
