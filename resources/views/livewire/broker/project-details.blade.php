<div>
    @php
        $images = $project->projectMedia->where('media_type', 'image')->values();
        $projectImageItems = $images->map(function($m) use ($project) {
            return [
                'id' => $m->id,
                'url' => \App\Helpers\MediaHelper::getUrl($m->media_url),
                'title' => $m->media_title ?: $project->name,
                'download_url' => route('broker.projects.media.download', [$project->id, $m->id]),
            ];
        })->values();

        $videos = $project->projectMedia->filter(function($m) {
            return $m->media_type === 'video' || !empty($m->youtube_url) || !empty($m->vimeo_url);
        })->values();
    @endphp

    <div x-data="{
        galleryOpen: false,
        galleryIndex: 0,
        galleryItems: {{ \Illuminate\Support\Js::from($projectImageItems) }},
        unitModalOpen: false,
        selectedUnit: null,
        selectedUnitGalleryIndex: 0
    }">
        <a href="{{ route('broker.projects') }}" class="inline-flex items-center gap-2 text-xs font-bold text-gray-400 hover:text-gray-900 mb-4 transition-colors">
            <i class="fas fa-arrow-right"></i> العودة للمشاريع
        </a>

        {{-- Header --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden mb-6">
            @if ($images->isNotEmpty())
                <div class="relative group">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-1 h-64 md:h-72">
                        @foreach ($images->take(4) as $i => $media)
                            <div class="{{ $i === 0 ? 'col-span-2 row-span-2' : '' }} bg-gray-100 overflow-hidden cursor-pointer relative"
                                 @click="galleryOpen = true; galleryIndex = {{ $i }}">
                                <img src="{{ \App\Helpers\MediaHelper::getUrl($media->media_url) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="{{ $project->name }}">
                                <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors"></div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" @click="galleryOpen = true; galleryIndex = 0"
                            class="absolute bottom-3 left-3 px-3.5 py-2 bg-black/75 hover:bg-black text-white text-xs font-bold rounded-xl backdrop-blur-md transition-all shadow-lg flex items-center gap-2">
                        <i class="fas fa-images text-yellow-400"></i>
                        <span>عرض صور المشروع ({{ $images->count() }})</span>
                    </button>
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
                        @if ($images->isNotEmpty())
                            <a href="{{ route('broker.projects.download-images', $project->id) }}"
                               class="px-4 py-3 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 text-sm font-black rounded-xl transition-all whitespace-nowrap flex items-center justify-center gap-2">
                                <i class="fas fa-file-archive text-blue-500"></i>
                                تنزيل صور المشروع (ZIP)
                            </a>
                        @endif
                        <button type="button" wire:click="downloadPriceList" wire:loading.attr="disabled" wire:target="downloadPriceList"
                           class="px-5 py-3 bg-white border border-gray-200 hover:border-gray-900 text-gray-700 hover:text-gray-900 text-sm font-black rounded-xl transition-all whitespace-nowrap disabled:opacity-50">
                            <i class="fas fa-file-pdf text-red-400 ml-2" wire:loading.remove wire:target="downloadPriceList"></i>
                            <i class="fas fa-spinner fa-spin ml-2" wire:loading wire:target="downloadPriceList"></i>
                            تحميل قائمة الأسعار
                        </button>
                        <a href="{{ route('broker.leads.create', ['project' => $project->id]) }}"
                           class="px-6 py-3 bg-gray-900 hover:bg-gray-800 text-white text-sm font-black rounded-xl transition-all whitespace-nowrap text-center">
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
                        <div class="text-[11px] font-black text-gray-400 uppercase mb-3">المرفقات والبروشورات</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($attachments as $attachment)
                                <a href="{{ \App\Helpers\MediaHelper::getUrl($attachment->media_url) }}" target="_blank"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-100 text-gray-700 text-[12px] font-bold rounded-xl transition-all">
                                    <i class="fas fa-file-pdf text-red-400"></i> {{ $attachment->media_title ?: 'تحميل المرفق' }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Videos Section --}}
        @if ($videos->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-video text-red-500 text-lg"></i>
                        <h2 class="text-base font-black text-gray-900">فيديوهات المشروع</h2>
                    </div>
                    <span class="text-xs font-bold text-gray-400">{{ $videos->count() }} فيديو</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($videos as $video)
                        <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 flex flex-col justify-between gap-3">
                            <div>
                                @if ($video->youtube_embed_url)
                                    <div class="aspect-video w-full rounded-xl overflow-hidden bg-black mb-3">
                                        <iframe src="{{ $video->youtube_embed_url }}" class="w-full h-full border-0" allowfullscreen></iframe>
                                    </div>
                                @elseif ($video->media_url && \Illuminate\Support\Facades\Storage::disk('public')->exists($video->media_url))
                                    <div class="aspect-video w-full rounded-xl overflow-hidden bg-black mb-3">
                                        <video controls class="w-full h-full object-contain" preload="metadata">
                                            <source src="{{ \App\Helpers\MediaHelper::getUrl($video->media_url) }}">
                                            متصفحك لا يدعم تشغيل الفيديو.
                                        </video>
                                    </div>
                                @elseif ($video->youtube_url)
                                    <div class="aspect-video w-full rounded-xl overflow-hidden bg-gray-900 flex flex-col items-center justify-center text-white mb-3 p-4 text-center">
                                        <i class="fab fa-youtube text-red-500 text-4xl mb-2"></i>
                                        <span class="text-xs font-bold mb-3">{{ $video->media_title ?: 'فيديو المشروع' }}</span>
                                        <a href="{{ $video->youtube_url }}" target="_blank" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl transition-all">
                                            مشاهدة على YouTube <i class="fas fa-external-link-alt mr-1"></i>
                                        </a>
                                    </div>
                                @endif
                                <h3 class="text-sm font-black text-gray-900">{{ $video->media_title ?: 'فيديو عالي الدقة عن المشروع' }}</h3>
                                @if ($video->media_description)
                                    <p class="text-xs text-gray-500 mt-1">{!! nl2br(e(strip_tags($video->media_description))) !!}</p>
                                @endif
                            </div>
                            @if ($video->media_url && \Illuminate\Support\Facades\Storage::disk('public')->exists($video->media_url))
                                <div class="pt-3 border-t border-gray-200/60 flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-gray-400">ملف متاح للتنزيل التسويقي</span>
                                    <a href="{{ route('broker.projects.media.download', [$project->id, $video->id]) }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                                        <i class="fas fa-download text-blue-400"></i> تنزيل الفيديو
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Features · Guarantees · Landmarks --}}
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

        {{-- Units Section --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-5">
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-black text-gray-900">كروت الوحدات</h2>
                    <span class="px-2.5 py-0.5 bg-gray-100 text-gray-700 text-xs font-black rounded-full">{{ $units->total() }} وحدة</span>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    {{-- Search Input --}}
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="searchUnit" placeholder="بحث باسم الوحدة أو الرقم..."
                               class="w-44 sm:w-60 pr-8 pl-3 py-2 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-xs font-bold bg-gray-50">
                        <i class="fas fa-search absolute right-3 top-3 text-gray-400 text-xs"></i>
                    </div>
                    {{-- Status Filter --}}
                    <select wire:model.live="unitStatusFilter" class="px-3 py-2 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-xs font-bold">
                        <option value="">كل الحالات</option>
                        <option value="0">المتاحة فقط</option>
                        <option value="1">المحجوزة</option>
                        <option value="2">المباعة</option>
                    </select>
                    {{-- Type Filter --}}
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
                            $gallery->push(['url' => \App\Helpers\MediaHelper::getUrl($unit->image), 'is_plan' => false, 'title' => 'الصورة الرئيسية']);
                        }
                        foreach ((array) $unit->images as $img) {
                            if (! empty($img)) {
                                $gallery->push(['url' => \App\Helpers\MediaHelper::getUrl($img), 'is_plan' => false, 'title' => 'صورة إضافية']);
                            }
                        }
                        if ($unit->floor_plan) {
                            $gallery->push(['url' => \App\Helpers\MediaHelper::getUrl($unit->floor_plan), 'is_plan' => true, 'title' => 'المخطط الهندسي']);
                        }
                        $gallery = $gallery->values();

                        $unitData = [
                            'id' => $unit->id,
                            'title' => $unit->title,
                            'building_number' => $unit->building_number ?? '',
                            'unit_number' => $unit->unit_number ?? '',
                            'unit_type' => $unit->unit_type,
                            'description' => $unit->description ? strip_tags($unit->description) : null,
                            'unit_area' => $unit->unit_area,
                            'beadrooms' => $unit->beadrooms,
                            'bathrooms' => $unit->bathrooms,
                            'living_rooms' => $unit->living_rooms ?? null,
                            'kitchen' => $unit->kitchen ?? null,
                            'floor' => $unit->floor,
                            'sale_type' => $unit->sale_type ?? null,
                            'case' => $unit->case,
                            'unit_price' => $unit->show_price && $unit->unit_price ? number_format((float) $unit->unit_price) : null,
                            'raw_price' => (float) $unit->unit_price,
                            'commission' => $broker && (float) $project->commission_value > 0 ? number_format($project->commissionForPrice($unit->unit_price)) : null,
                            'floor_plan' => $unit->floor_plan ? route('broker.units.floor-plan', $unit->id) : null,
                            'lead_url' => route('broker.leads.create', ['project' => $project->id, 'unit' => $unit->id]),
                            'gallery' => $gallery,
                            'features' => $unit->features ? $unit->features->pluck('name')->toArray() : [],
                        ];
                    @endphp
                    <div class="border border-gray-100 rounded-2xl overflow-hidden hover:border-gray-300 hover:shadow-md transition-all bg-white flex flex-col justify-between"
                         x-data="{ active: 0, images: {{ \Illuminate\Support\Js::from($gallery) }} }">
                        <div>
                            {{-- Image header --}}
                            <div class="h-40 bg-gray-100 relative group cursor-pointer"
                                 @click="selectedUnit = {{ \Illuminate\Support\Js::from($unitData) }}; selectedUnitGalleryIndex = active; unitModalOpen = true;">
                                @if ($gallery->isNotEmpty())
                                    <img src="{{ $gallery->first()['url'] }}" :src="images[active].url" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="{{ $unit->title }}">
                                    <span x-show="images[active].is_plan" x-cloak class="absolute top-2 left-2 px-2.5 py-1 text-[10px] font-black rounded-full bg-blue-500 text-white">
                                        <i class="fas fa-ruler-combined ml-1"></i>مخطط
                                    </span>
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300"><i class="fas fa-house text-3xl"></i></div>
                                @endif
                                <span class="absolute top-2 right-2 px-2.5 py-1 text-[10px] font-black rounded-full text-white shadow-sm
                                    {{ $unit->case == 0 ? 'bg-green-500' : ($unit->case == 1 ? 'bg-yellow-500' : 'bg-red-500') }}">
                                    {{ $unit->case == 0 ? 'متاحة' : ($unit->case == 1 ? 'محجوزة' : 'مباعة') }}
                                </span>
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                    <span class="opacity-0 group-hover:opacity-100 transition-opacity px-3.5 py-2 bg-white text-gray-900 text-xs font-black rounded-xl shadow-lg border border-gray-100">
                                        <i class="fas fa-eye ml-1 text-primary-600"></i> عرض تفاصيل الوحدة
                                    </span>
                                </div>
                            </div>

                            {{-- Gallery Thumbnails Strip --}}
                            @if ($gallery->count() > 1)
                                <div class="flex gap-1.5 p-2 overflow-x-auto bg-gray-50/70 border-b border-gray-100">
                                    @foreach ($gallery as $i => $g)
                                        <button type="button" @click="active = {{ $i }}"
                                            class="relative shrink-0 w-10 h-10 rounded-lg overflow-hidden border-2 transition-all"
                                            :class="active === {{ $i }} ? 'border-gray-900 ring-2 ring-gray-900/20' : 'border-transparent opacity-60 hover:opacity-100'">
                                            <img src="{{ $g['url'] }}" class="w-full h-full object-cover" alt="">
                                            @if ($g['is_plan'])
                                                <span class="absolute inset-x-0 bottom-0 bg-blue-500 text-white text-[6px] font-black text-center leading-tight py-px">مخطط</span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Card Details --}}
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-sm font-black text-gray-900 hover:text-primary-600 transition-colors cursor-pointer"
                                        @click="selectedUnit = {{ \Illuminate\Support\Js::from($unitData) }}; selectedUnitGalleryIndex = 0; unitModalOpen = true;">
                                        {{ $unit->title }}
                                    </h3>
                                    <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md">{{ $unit->unit_type }}</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-3 text-[11px] text-gray-500 font-bold mb-3">
                                    @if ($unit->unit_area) <span><i class="fas fa-ruler-combined ml-1 text-gray-400"></i>{{ $unit->unit_area }} م²</span> @endif
                                    @if ($unit->beadrooms) <span><i class="fas fa-bed ml-1 text-gray-400"></i>{{ $unit->beadrooms }} غرف</span> @endif
                                    @if ($unit->bathrooms) <span><i class="fas fa-bath ml-1 text-gray-400"></i>{{ $unit->bathrooms }} حمام</span> @endif
                                    @if ($unit->floor) <span><i class="fas fa-stairs ml-1 text-gray-400"></i>دور {{ $unit->floor }}</span> @endif
                                </div>
                            </div>
                        </div>

                        <div class="p-4 pt-0">
                            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                <div>
                                    @if ($unit->show_price && $unit->unit_price)
                                        <span class="text-sm font-black text-gray-900">{{ number_format((float) $unit->unit_price) }} ر.س</span>
                                    @else
                                        <span class="text-[11px] font-bold text-gray-400">السعر عند الطلب</span>
                                    @endif
                                    @if ($broker && (float) $project->commission_value > 0 && ($project->isFixedCommission() || ($unit->show_price && $unit->unit_price)))
                                        <div class="text-[10px] font-bold text-primary-600 mt-0.5">
                                            <i class="fas fa-percent ml-1"></i>عمولتك: {{ number_format($project->commissionForPrice($unit->unit_price)) }} ر.س
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <button type="button" @click="selectedUnit = {{ \Illuminate\Support\Js::from($unitData) }}; selectedUnitGalleryIndex = 0; unitModalOpen = true;"
                                            class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-[10px] font-black rounded-lg transition-all flex items-center gap-1"
                                            title="عرض التفاصيل الكاملة">
                                        <i class="fas fa-eye"></i>
                                        <span>التفاصيل</span>
                                    </button>
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
                    </div>
                @empty
                    <div class="col-span-full p-12 text-center bg-gray-50 rounded-2xl border border-gray-100 text-sm text-gray-400 font-bold">
                        <i class="fas fa-search text-2xl mb-2 text-gray-300 block"></i>
                        لا توجد وحدات مطابقة للبحث
                    </div>
                @endforelse
            </div>

            <div class="mt-5">
                {{ $units->links() }}
            </div>
        </div>

        {{-- 1. Fullscreen Project Images Lightbox Modal --}}
        <div x-show="galleryOpen" x-cloak
             class="fixed inset-0 z-50 bg-black/95 backdrop-blur-md flex flex-col justify-between p-4"
             @keydown.window.escape="galleryOpen = false"
             @keydown.window.arrow-right="galleryIndex = (galleryIndex > 0 ? galleryIndex - 1 : galleryItems.length - 1)"
             @keydown.window.arrow-left="galleryIndex = (galleryIndex < galleryItems.length - 1 ? galleryIndex + 1 : 0)">

            {{-- Top Toolbar --}}
            <div class="flex items-center justify-between text-white border-b border-white/10 pb-3">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-black bg-white/10 px-3 py-1 rounded-full text-yellow-400"
                          x-text="(galleryIndex + 1) + ' / ' + galleryItems.length"></span>
                    <span class="text-sm font-bold text-gray-200 hidden sm:inline" x-text="galleryItems[galleryIndex]?.title"></span>
                </div>
                <div class="flex items-center gap-2">
                    <a :href="galleryItems[galleryIndex]?.download_url"
                       class="px-3.5 py-1.5 bg-white/10 hover:bg-white/20 text-white text-xs font-bold rounded-xl transition-all flex items-center gap-1.5">
                        <i class="fas fa-download text-blue-400"></i>
                        <span class="hidden sm:inline">تحميل هذه الصورة</span>
                    </a>
                    @if ($images->isNotEmpty())
                        <a href="{{ route('broker.projects.download-images', $project->id) }}"
                           class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition-all flex items-center gap-1.5">
                            <i class="fas fa-file-archive"></i>
                            <span class="hidden sm:inline">تحميل الكل (ZIP)</span>
                        </a>
                    @endif
                    <button type="button" @click="galleryOpen = false" class="w-9 h-9 flex items-center justify-center bg-white/10 hover:bg-white/20 text-white rounded-xl transition-all">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            {{-- Main Viewport --}}
            <div class="relative flex-1 flex items-center justify-center my-4 overflow-hidden">
                <button type="button" @click="galleryIndex = (galleryIndex > 0 ? galleryIndex - 1 : galleryItems.length - 1)"
                        class="absolute right-2 sm:right-6 z-10 w-12 h-12 flex items-center justify-center bg-black/60 hover:bg-black text-white rounded-full transition-all border border-white/10 shadow-xl">
                    <i class="fas fa-chevron-right text-lg"></i>
                </button>

                <img :src="galleryItems[galleryIndex]?.url"
                     class="max-h-[75vh] max-w-full object-contain rounded-xl shadow-2xl transition-all duration-200" alt="">

                <button type="button" @click="galleryIndex = (galleryIndex < galleryItems.length - 1 ? galleryIndex + 1 : 0)"
                        class="absolute left-2 sm:left-6 z-10 w-12 h-12 flex items-center justify-center bg-black/60 hover:bg-black text-white rounded-full transition-all border border-white/10 shadow-xl">
                    <i class="fas fa-chevron-left text-lg"></i>
                </button>
            </div>

            {{-- Thumbnails Bar --}}
            <div class="flex gap-2 overflow-x-auto justify-center py-2 border-t border-white/10 max-w-4xl mx-auto w-full">
                <template x-for="(item, idx) in galleryItems" :key="idx">
                    <button type="button" @click="galleryIndex = idx"
                            class="w-14 h-14 rounded-xl overflow-hidden border-2 transition-all shrink-0"
                            :class="galleryIndex === idx ? 'border-yellow-400 scale-105 opacity-100 ring-2 ring-yellow-400/40' : 'border-transparent opacity-40 hover:opacity-80'">
                        <img :src="item.url" class="w-full h-full object-cover" alt="">
                    </button>
                </template>
            </div>
        </div>

        {{-- 2. Sleek Platform-Style Unit Details Pop-up Modal --}}
        <div x-show="unitModalOpen" x-cloak
             class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto"
             @keydown.window.escape="unitModalOpen = false">

            <div class="bg-white rounded-3xl max-w-3xl w-full overflow-hidden shadow-2xl border border-gray-100 transition-all transform my-6"
                 @click.away="unitModalOpen = false">
                
                {{-- Platform Header --}}
                <div class="p-6 bg-white border-b border-gray-100 flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-gray-900 text-white flex items-center justify-center font-black text-base shrink-0 shadow-md shadow-gray-200">
                            <i class="fas fa-home"></i>
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-black text-gray-900" x-text="selectedUnit?.title"></h3>
                                <span class="px-2.5 py-0.5 text-xs font-bold rounded-lg bg-gray-100 text-gray-700 border border-gray-200/60" x-text="selectedUnit?.unit_type"></span>
                                <span class="px-3 py-0.5 text-xs font-black rounded-full border shadow-sm"
                                      :class="selectedUnit?.case == 0 ? 'bg-green-50 text-green-700 border-green-200' : (selectedUnit?.case == 1 ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : 'bg-red-50 text-red-600 border-red-200')"
                                      x-text="selectedUnit?.case == 0 ? 'متاحة' : (selectedUnit?.case == 1 ? 'محجوزة' : 'مباعة')"></span>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 font-bold mt-1">
                                <span><i class="fas fa-building text-gray-400 ml-1"></i>{{ $project->name }}</span>
                                <template x-if="selectedUnit?.building_number">
                                    <span><i class="fas fa-city text-gray-400 ml-1"></i>مبنى <span x-text="selectedUnit.building_number"></span></span>
                                </template>
                                <template x-if="selectedUnit?.unit_number">
                                    <span><i class="fas fa-door-closed text-gray-400 ml-1"></i>وحدة <span x-text="selectedUnit.unit_number"></span></span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <button type="button" @click="unitModalOpen = false" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-900 border border-gray-100 flex items-center justify-center transition-all shrink-0">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                {{-- Pop-up Body --}}
                <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto scrollbar-thin">
                    {{-- Unit Image Gallery & Floor Plan Viewer --}}
                    <template x-if="selectedUnit?.gallery && selectedUnit.gallery.length > 0">
                        <div class="space-y-2.5">
                            <div class="h-64 sm:h-72 bg-gray-50 rounded-2xl overflow-hidden relative border border-gray-100 group shadow-inner">
                                <img :src="selectedUnit.gallery[selectedUnitGalleryIndex]?.url" class="w-full h-full object-cover" alt="">
                                <span x-show="selectedUnit.gallery[selectedUnitGalleryIndex]?.is_plan" class="absolute top-3 left-3 px-3 py-1 bg-blue-600 text-white text-xs font-black rounded-full shadow-md">
                                    <i class="fas fa-ruler-combined ml-1"></i> المخطط الهندسي
                                </span>
                                <span class="absolute bottom-3 right-3 px-3.5 py-1.5 bg-black/70 text-white text-xs font-bold rounded-xl backdrop-blur-md"
                                      x-text="selectedUnit.gallery[selectedUnitGalleryIndex]?.title || 'صورة الوحدة'"></span>
                            </div>
                            <template x-if="selectedUnit.gallery.length > 1">
                                <div class="flex gap-2 overflow-x-auto py-1">
                                    <template x-for="(g, gIdx) in selectedUnit.gallery" :key="gIdx">
                                        <button type="button" @click="selectedUnitGalleryIndex = gIdx"
                                                class="w-14 h-14 rounded-xl overflow-hidden border-2 transition-all shrink-0 relative"
                                                :class="selectedUnitGalleryIndex === gIdx ? 'border-gray-900 ring-2 ring-gray-900/20 scale-105' : 'border-transparent opacity-50 hover:opacity-100'">
                                            <img :src="g.url" class="w-full h-full object-cover" alt="">
                                            <span x-show="g.is_plan" class="absolute inset-x-0 bottom-0 bg-blue-600 text-white text-[7px] font-black text-center py-px">مخطط</span>
                                        </button>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Specs Cards Grid --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-layer-group text-primary-600 text-xs"></i>
                            <h4 class="text-xs font-black text-gray-900 uppercase tracking-wide">تفاصيل ومواصفات الوحدة</h4>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="bg-gray-50/80 p-3.5 rounded-2xl border border-gray-100 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-blue-600 shrink-0 text-xs shadow-sm">
                                    <i class="fas fa-ruler-combined"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold text-gray-400 block">المساحة</span>
                                    <span class="text-xs font-black text-gray-900 truncate" x-text="selectedUnit?.unit_area ? selectedUnit.unit_area + ' م²' : '—'"></span>
                                </div>
                            </div>

                            <div class="bg-gray-50/80 p-3.5 rounded-2xl border border-gray-100 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-indigo-600 shrink-0 text-xs shadow-sm">
                                    <i class="fas fa-bed"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold text-gray-400 block">غرف النوم</span>
                                    <span class="text-xs font-black text-gray-900 truncate" x-text="selectedUnit?.beadrooms ? selectedUnit.beadrooms + ' غرفة' : '—'"></span>
                                </div>
                            </div>

                            <div class="bg-gray-50/80 p-3.5 rounded-2xl border border-gray-100 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-teal-600 shrink-0 text-xs shadow-sm">
                                    <i class="fas fa-bath"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold text-gray-400 block">دورات المياه</span>
                                    <span class="text-xs font-black text-gray-900 truncate" x-text="selectedUnit?.bathrooms ? selectedUnit.bathrooms + ' حمام' : '—'"></span>
                                </div>
                            </div>

                            <div class="bg-gray-50/80 p-3.5 rounded-2xl border border-gray-100 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-purple-600 shrink-0 text-xs shadow-sm">
                                    <i class="fas fa-couch"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold text-gray-400 block">الصالات / المجالس</span>
                                    <span class="text-xs font-black text-gray-900 truncate" x-text="selectedUnit?.living_rooms ? selectedUnit.living_rooms + ' صالة' : '—'"></span>
                                </div>
                            </div>

                            <div class="bg-gray-50/80 p-3.5 rounded-2xl border border-gray-100 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-orange-600 shrink-0 text-xs shadow-sm">
                                    <i class="fas fa-utensils"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold text-gray-400 block">المطابخ</span>
                                    <span class="text-xs font-black text-gray-900 truncate" x-text="selectedUnit?.kitchen ? selectedUnit.kitchen + ' مطبخ' : '—'"></span>
                                </div>
                            </div>

                            <div class="bg-gray-50/80 p-3.5 rounded-2xl border border-gray-100 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-amber-600 shrink-0 text-xs shadow-sm">
                                    <i class="fas fa-stairs"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold text-gray-400 block">الدور</span>
                                    <span class="text-xs font-black text-gray-900 truncate" x-text="selectedUnit?.floor ? 'دور ' + selectedUnit.floor : '—'"></span>
                                </div>
                            </div>

                            <div class="bg-gray-50/80 p-3.5 rounded-2xl border border-gray-100 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-emerald-600 shrink-0 text-xs shadow-sm">
                                    <i class="fas fa-city"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold text-gray-400 block">رقم المبنى</span>
                                    <span class="text-xs font-black text-gray-900 truncate" x-text="selectedUnit?.building_number ? 'مبنى ' + selectedUnit.building_number : '—'"></span>
                                </div>
                            </div>

                            <div class="bg-gray-50/80 p-3.5 rounded-2xl border border-gray-100 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-rose-600 shrink-0 text-xs shadow-sm">
                                    <i class="fas fa-door-closed"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold text-gray-400 block">رقم الوحدة</span>
                                    <span class="text-xs font-black text-gray-900 truncate" x-text="selectedUnit?.unit_number ? 'وحدة ' + selectedUnit.unit_number : '—'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <template x-if="selectedUnit?.description">
                        <div>
                            <h4 class="text-xs font-black text-gray-900 uppercase tracking-wide mb-2">الوصف والتفاصيل</h4>
                            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 text-xs font-bold text-gray-700 leading-relaxed"
                                 x-text="selectedUnit.description"></div>
                        </div>
                    </template>

                    {{-- Unit Features --}}
                    <template x-if="selectedUnit?.features && selectedUnit.features.length > 0">
                        <div>
                            <h4 class="text-xs font-black text-gray-900 uppercase tracking-wide mb-2.5">مميزات الوحدة</h4>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="(feat, fIdx) in selectedUnit.features" :key="fIdx">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 text-gray-800 text-xs font-bold rounded-xl border border-gray-200/80">
                                        <i class="fas fa-check text-green-500 text-[10px]"></i>
                                        <span x-text="feat"></span>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Financials & Broker Commission Card --}}
                    <div class="bg-primary-50/60 border border-primary-100 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <span class="text-xs font-bold text-gray-500 block mb-0.5">سعر الوحدة الإجمالي</span>
                            <span class="text-lg font-black text-gray-900" x-text="selectedUnit?.unit_price ? selectedUnit.unit_price + ' ر.س' : 'السعر عند الطلب'"></span>
                        </div>
                        <template x-if="selectedUnit?.commission">
                            <div class="bg-white px-4 py-2.5 rounded-xl border border-primary-200 shadow-sm text-left sm:text-right w-full sm:w-auto">
                                <span class="text-[10px] font-black text-primary-500 uppercase tracking-wide block mb-0.5"><i class="fas fa-percent ml-1"></i>عمولتك المتوقعة من ريفا</span>
                                <span class="text-sm font-black text-primary-600" x-text="selectedUnit.commission + ' ر.س'"></span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Platform Footer --}}
                <div class="p-4 sm:p-5 bg-white border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div>
                        <template x-if="selectedUnit?.floor_plan">
                            <a :href="selectedUnit.floor_plan"
                               class="px-4 py-2.5 bg-white border border-gray-200 hover:border-gray-900 text-gray-700 text-xs font-black rounded-xl transition-all flex items-center gap-2">
                                <i class="fas fa-download text-blue-500"></i> تحميل المخطط الهندسي
                            </a>
                        </template>
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="button" @click="unitModalOpen = false" class="px-5 py-2.5 bg-white border border-gray-200 hover:border-gray-900 text-gray-700 text-xs font-black rounded-xl transition-all">
                            إغلاق
                        </button>
                        <template x-if="selectedUnit?.case == 0">
                            <a :href="selectedUnit.lead_url"
                               class="flex-1 sm:flex-none px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-black rounded-xl shadow-lg shadow-gray-200 transition-all text-center">
                                <i class="fas fa-user-plus ml-1.5"></i> إرسال عميل لهذه الوحدة
                            </a>
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
