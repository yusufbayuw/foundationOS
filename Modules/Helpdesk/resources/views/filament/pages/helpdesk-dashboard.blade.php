<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-3">
        <x-filament::section>
            <p class="text-sm text-gray-500">{{ \Modules\Core\Support\FilamentUi::text('Open tickets') }}</p>
            <p class="text-2xl font-bold">{{ $this->openTickets }}</p>
        </x-filament::section>
        <x-filament::section>
            <p class="text-sm text-gray-500">{{ \Modules\Core\Support\FilamentUi::text('Escalated tickets') }}</p>
            <p class="text-2xl font-bold">{{ $this->escalatedTickets }}</p>
        </x-filament::section>
        <x-filament::section>
            <p class="text-sm text-gray-500">{{ \Modules\Core\Support\FilamentUi::text('Average resolution hours') }}</p>
            <p class="text-2xl font-bold">{{ $this->avgResolutionHours }}</p>
        </x-filament::section>
    </div>
</x-filament-panels::page>
