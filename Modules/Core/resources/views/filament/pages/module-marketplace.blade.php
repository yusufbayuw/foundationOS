<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($this->getModulesWithStatus() as $module)
            <div
                class="fi-card rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6 flex flex-col gap-4"
            >
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        @if ($module->icon)
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg"
                                style="background-color: {{ $module->color ?? '#6366f1' }}20"
                            >
                                <x-dynamic-component
                                    :component="$module->icon"
                                    class="h-5 w-5"
                                    style="color: {{ $module->color ?? '#6366f1' }}"
                                />
                            </div>
                        @endif

                        <div>
                            <h3 class="text-sm font-semibold text-gray-950 dark:text-white">
                                {{ $module->name }}
                            </h3>
                            @if ($module->is_premium)
                                <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-400/10 dark:text-amber-400">
                                    Premium
                                </span>
                            @endif
                        </div>
                    </div>

                    @if (! $module->is_core)
                        <button
                            wire:click="toggleModule({{ $module->id }})"
                            type="button"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 {{ $module->is_tenant_enabled ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700' }}"
                            role="switch"
                            aria-checked="{{ $module->is_tenant_enabled ? 'true' : 'false' }}"
                        >
                            <span
                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $module->is_tenant_enabled ? 'translate-x-5' : 'translate-x-0' }}"
                            ></span>
                        </button>
                    @else
                        <span class="inline-flex items-center rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-700/10 dark:bg-primary-400/10 dark:text-primary-400">
                            {{ \Modules\Core\Support\FilamentUi::text('Core') }}
                        </span>
                    @endif
                </div>

                @if ($module->description)
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $module->description }}
                    </p>
                @endif

                @if ($module->is_premium && $module->price_monthly)
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ number_format($module->price_monthly, 0, ',', '.') }} / {{ \Modules\Core\Support\FilamentUi::text('month') }}
                    </p>
                @endif
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
