<x-filament-panels::page>
    <form wire:submit="generateReport" class="space-y-6">
        {{ $this->form }}
        <x-filament::button type="submit">
            {{ \Modules\Core\Support\FilamentUi::text('Generate report') }}
        </x-filament::button>
    </form>

    @if (! empty($reportData))
        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <x-filament::section>
                <p class="text-sm text-gray-500">{{ \Modules\Core\Support\FilamentUi::text('Revenue') }}</p>
                <p class="text-2xl font-semibold">Rp {{ number_format($reportData['revenue'] ?? 0, 0, ',', '.') }}</p>
            </x-filament::section>
            <x-filament::section>
                <p class="text-sm text-gray-500">{{ \Modules\Core\Support\FilamentUi::text('Payroll') }}</p>
                <p class="text-2xl font-semibold">Rp {{ number_format($reportData['payroll'] ?? 0, 0, ',', '.') }}</p>
            </x-filament::section>
            <x-filament::section>
                <p class="text-sm text-gray-500">{{ \Modules\Core\Support\FilamentUi::text('Efficiency ratio') }}</p>
                <p class="text-2xl font-semibold">{{ $reportData['efficiency_ratio'] ?? '-' }}</p>
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
