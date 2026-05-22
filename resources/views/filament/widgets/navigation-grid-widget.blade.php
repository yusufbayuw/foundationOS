<x-filament-widgets::widget>
    <x-filament::section>

        {{-- Pinned / Favorit --}}
        @php $pinnedItems = $this->getPinnedItems(); @endphp
        @if (count($pinnedItems))
            <div class="mb-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-3">
                    {{ \Modules\Core\Support\FilamentUi::text('Favorites') }}
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach ($pinnedItems as $item)
                        <div class="relative group">
                            <a href="{{ $item['url'] }}"
                               class="flex flex-col items-center gap-2 p-3 rounded-xl bg-primary-50 dark:bg-primary-950/30 border border-primary-200 dark:border-primary-800 hover:bg-primary-100 dark:hover:bg-primary-900/40 transition-colors">
                                <x-dynamic-component
                                    :component="$item['icon']"
                                    class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                                <span class="text-xs font-medium text-center text-primary-700 dark:text-primary-300 leading-tight line-clamp-2">
                                    {{ $item['label'] }}
                                </span>
                            </a>
                            <button
                                wire:click="togglePin('{{ addslashes($item['label']) }}', '{{ $item['url'] }}', '{{ $item['icon'] }}')"
                                class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-sm hover:bg-red-50 dark:hover:bg-red-950"
                                title="{{ \Modules\Core\Support\FilamentUi::text('Remove from favorites') }}">
                                <x-heroicon-s-x-mark class="w-3 h-3 text-gray-500 dark:text-gray-400" />
                            </button>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 border-t border-gray-100 dark:border-gray-800"></div>
            </div>
        @endif

        {{-- Search & Group Filter --}}
        {{-- Search --}}
        <div class="relative mb-3">
            <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
            <input
                type="text"
                wire:model.live.debounce.200ms="search"
                placeholder="{{ \Modules\Core\Support\FilamentUi::text('Search menu') }}..."
                class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
            />
            @if ($search)
                <button wire:click="$set('search', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <x-heroicon-o-x-circle class="w-4 h-4" />
                </button>
            @endif
        </div>

        {{-- Group Filter Chips --}}
        @if (! $search)
            <div class="flex flex-wrap gap-2 mb-5">
                <button
                    wire:click="setGroup('')"
                    @class([
                        'px-3 py-1.5 text-xs font-medium rounded-full border transition-colors',
                        'bg-primary-600 border-primary-600 text-white' => $activeGroup === '',
                        'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:border-primary-400 hover:text-primary-600' => $activeGroup !== '',
                    ])>
                    {{ \Modules\Core\Support\FilamentUi::text('All') }}
                </button>
                @foreach ($this->getGroupNames() as $group)
                    <button
                        wire:click="setGroup('{{ $group }}')"
                        @class([
                            'px-3 py-1.5 text-xs font-medium rounded-full border transition-colors',
                            'bg-primary-600 border-primary-600 text-white' => $activeGroup === $group,
                            'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:border-primary-400 hover:text-primary-600' => $activeGroup !== $group,
                        ])>
                        {{ $group }}
                    </button>
                @endforeach
            </div>
        @endif

        {{-- Grid Items --}}
        @php $items = $this->getVisibleItems(); @endphp
        @if (count($items))
            <div
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3"
                x-data="{ startX: 0 }"
                x-on:touchstart="startX = $event.touches[0].clientX"
                x-on:touchend="
                    const diff = startX - $event.changedTouches[0].clientX;
                ">
                @foreach ($items as $item)
                    <div class="relative group">
                        <a href="{{ $item['url'] }}"
                           class="flex flex-col items-center gap-2 p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-700/50 hover:bg-primary-50 dark:hover:bg-primary-950/30 hover:border-primary-200 dark:hover:border-primary-800 transition-colors">
                            <x-dynamic-component
                                :component="$item['icon']"
                                class="w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors" />
                            <span class="text-xs font-medium text-center text-gray-700 dark:text-gray-300 group-hover:text-primary-700 dark:group-hover:text-primary-300 leading-tight line-clamp-2 transition-colors">
                                {{ $item['label'] }}
                            </span>
                        </a>
                        {{-- Pin toggle --}}
                        <button
                            wire:click.prevent="togglePin('{{ addslashes($item['label']) }}', '{{ $item['url'] }}', '{{ $item['icon'] }}')"
                            class="absolute top-1.5 right-1.5 opacity-0 group-hover:opacity-100 transition-opacity"
                            title="{{ $this->isPinned($item['url']) ? \Modules\Core\Support\FilamentUi::text('Remove from favorites') : \Modules\Core\Support\FilamentUi::text('Add to favorites') }}">
                            @if ($this->isPinned($item['url']))
                                <x-heroicon-s-star class="w-3.5 h-3.5 text-amber-400" />
                            @else
                                <x-heroicon-o-star class="w-3.5 h-3.5 text-gray-400 hover:text-amber-400 transition-colors" />
                            @endif
                        </button>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12 text-gray-400 dark:text-gray-500">
                <x-heroicon-o-magnifying-glass class="w-8 h-8 mb-2" />
                <p class="text-sm">{{ \Modules\Core\Support\FilamentUi::text('No menu found') }}</p>
            </div>
        @endif

    </x-filament::section>
</x-filament-widgets::widget>
