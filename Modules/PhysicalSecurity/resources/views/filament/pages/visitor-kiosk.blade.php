<x-filament-panels::page>
    <form wire:submit="checkIn" class="mx-auto max-w-lg space-y-4">
        {{ $this->form }}
        <x-filament::button type="submit" class="w-full">
            {{ \Modules\Core\Support\FilamentUi::text('Check in') }}
        </x-filament::button>
    </form>
</x-filament-panels::page>
