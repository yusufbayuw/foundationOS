<x-filament-panels::page>
    <div class="space-y-4">
        @forelse ($this->tree as $org)
            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                <p class="font-semibold">{{ $org->name }}</p>
                <p class="text-sm text-gray-500">{{ $org->organization_type ?? $org->type }}</p>
                @if ($org->childOrganizations->isNotEmpty())
                    <ul class="mt-3 ml-4 list-disc space-y-1 text-sm">
                        @foreach ($org->childOrganizations as $child)
                            <li>{{ $child->name }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @empty
            <p class="text-sm text-gray-500">{{ \Modules\Core\Support\FilamentUi::text('No organizations found') }}</p>
        @endforelse
    </div>
</x-filament-panels::page>
