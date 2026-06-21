<div>
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        عمولات المشاريع
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">تحديد عمولة الوسيط عن كل وحدة مباعة لكل مشروع</p>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="mt-4 p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                    {{ session('message') }}
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white min-h-screen p-2 sm:p-4">
        <!-- Search -->
        <div class="mb-4">
            <input type="text" wire:model.live.debounce.400ms="search"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full md:w-1/3 p-2.5"
                placeholder="ابحث عن مشروع...">
        </div>

        <!-- Projects table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right text-gray-700">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 font-semibold">المشروع</th>
                            <th class="px-4 py-3 font-semibold">نوع العمولة</th>
                            <th class="px-4 py-3 font-semibold">قيمة العمولة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projects as $project)
                            @php $type = $commissions[$project->id]['commission_type'] ?? 'percentage'; @endphp
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50" wire:key="project-{{ $project->id }}">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $project->name }}</td>
                                <td class="px-4 py-3">
                                    <select wire:model.live="commissions.{{ $project->id }}.commission_type"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2">
                                        @foreach ($commissionTypes as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="relative w-40">
                                        <input type="number" step="0.01" min="0"
                                            wire:model="commissions.{{ $project->id }}.commission_value"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 pl-12">
                                        <span class="absolute inset-y-0 left-0 flex items-center px-3 text-gray-400 text-xs border-r border-gray-200">
                                            {{ $type === 'fixed' ? 'ريال' : '%' }}
                                        </span>
                                    </div>
                                    @error("commissions.{$project->id}.commission_value")
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-400">لا توجد مشاريع.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between gap-4">
            <div>
                {{ $projects->links() }}
            </div>
            <button wire:click="saveAll" wire:loading.attr="disabled"
                class="bg-primary-600 text-white px-6 py-2.5 rounded-lg hover:bg-primary-700 transition-colors font-medium text-sm disabled:opacity-50">
                <span wire:loading.remove wire:target="saveAll">حفظ الكل</span>
                <span wire:loading wire:target="saveAll">جارٍ الحفظ...</span>
            </button>
        </div>
    </div>
</div>
