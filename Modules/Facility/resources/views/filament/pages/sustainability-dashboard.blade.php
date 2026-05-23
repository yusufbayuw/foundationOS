<x-filament-panels::page>
    <x-filament::section>
        <p class="text-sm text-gray-500">{{ \Modules\Core\Support\FilamentUi::text('Estimated carbon footprint (kg CO2e)') }}</p>
        <p class="text-2xl font-bold">{{ number_format($this->totalCarbonKg, 2) }}</p>
    </x-filament::section>
</x-filament-panels::page>
