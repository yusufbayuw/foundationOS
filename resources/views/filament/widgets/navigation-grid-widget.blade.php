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
                                wire:click.prevent='togglePin(@js($item["url"]))'
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
        <div class="mb-5 flex flex-col gap-3 sm:flex-row">
            <div class="relative min-w-0 flex-1">
                <x-heroicon-o-magnifying-glass class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                <input
                    type="search"
                    wire:model.live.debounce.200ms="search"
                    aria-label="{{ \Modules\Core\Support\FilamentUi::text('Search menu') }}"
                    placeholder="{{ \Modules\Core\Support\FilamentUi::text('Search menu') }}..."
                    class="w-full rounded-lg border border-gray-200 bg-white py-2 pl-9 pr-4 text-sm text-gray-800 placeholder-gray-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"
                />
            </div>

            @if (! $search)
                <x-filament::input.wrapper class="sm:w-56">
                    <x-filament::input.select
                        aria-label="{{ \Modules\Core\Support\FilamentUi::text('Filter by module') }}"
                        wire:model.live="activeGroup"
                    >
                        <option value="">{{ \Modules\Core\Support\FilamentUi::text('All modules') }}</option>
                        @foreach ($this->getGroupNames() as $group)
                            <option value="{{ $group }}">{{ $group }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            @endif
        </div>

        {{-- Grid Items --}}
        @php
            $items = $this->getDisplayedItems();
            $visibleItemCount = $this->getVisibleItemCount();
        @endphp
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
                            wire:click.prevent='togglePin(@js($item["url"]))'
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

            <div class="mt-5 flex flex-col items-center gap-3 border-t border-gray-100 pt-4 dark:border-gray-800 sm:flex-row sm:justify-between">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ \Modules\Core\Support\FilamentUi::text('Showing') }}
                    {{ count($items) }}
                    {{ \Modules\Core\Support\FilamentUi::text('of') }}
                    {{ $visibleItemCount }}
                    {{ \Modules\Core\Support\FilamentUi::text('menus') }}
                </p>

                @if ($this->hasMoreVisibleItems())
                    <x-filament::button
                        color="gray"
                        icon="heroicon-o-chevron-down"
                        size="sm"
                        wire:click="loadMore"
                    >
                        {{ \Modules\Core\Support\FilamentUi::text('Show more') }}
                    </x-filament::button>
                @endif
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12 text-gray-400 dark:text-gray-500">
                <x-heroicon-o-magnifying-glass class="w-8 h-8 mb-2" />
                <p class="text-sm">{{ \Modules\Core\Support\FilamentUi::text('No menu found') }}</p>
            </div>
        @endif

    </x-filament::section>
</x-filament-widgets::widget>
