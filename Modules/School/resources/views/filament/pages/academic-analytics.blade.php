<x-filament-panels::page>
    @php($summary = $this->getAnalyticsSummary())

    <div class="grid gap-4 md:grid-cols-2">
        <x-filament::section>
            <x-slot name="heading">{{ \Modules\Core\Support\FilamentUi::text('Average score') }}</x-slot>
            <p class="text-2xl font-semibold">{{ $summary['average_score'] ?? '—' }}</p>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">{{ \Modules\Core\Support\FilamentUi::text('Remedial flags') }}</x-slot>
            <p class="text-2xl font-semibold">{{ $summary['remedial_flags'] ?? 0 }}</p>
        </x-filament::section>

        <x-filament::section class="md:col-span-2">
            <x-slot name="heading">{{ \Modules\Core\Support\FilamentUi::text('Grade distribution') }}</x-slot>
            <dl class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                @foreach (($summary['grade_distribution'] ?? []) as $grade => $count)
                    <div>
                        <dt class="text-sm text-gray-500">{{ $grade }}</dt>
                        <dd class="text-lg font-medium">{{ $count }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-filament::section>
    </div>
</x-filament-panels::page>
