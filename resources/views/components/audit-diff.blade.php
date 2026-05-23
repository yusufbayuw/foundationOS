@props(['old' => [], 'new' => []])

@php
    $keys = collect(array_keys($old))->merge(array_keys($new))->unique()->sort()->values();
@endphp

<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th class="px-3 py-2 text-left font-medium">{{ \Modules\Core\Support\FilamentUi::field('field') }}</th>
                <th class="px-3 py-2 text-left font-medium">{{ \Modules\Core\Support\FilamentUi::field('old_values') }}</th>
                <th class="px-3 py-2 text-left font-medium">{{ \Modules\Core\Support\FilamentUi::field('new_values') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($keys as $key)
                @php
                    $oldVal = $old[$key] ?? null;
                    $newVal = $new[$key] ?? null;
                    $changed = $oldVal !== $newVal;
                @endphp
                <tr @class(['bg-amber-50/60 dark:bg-amber-950/20' => $changed])>
                    <td class="px-3 py-2 font-mono text-xs">{{ $key }}</td>
                    <td class="px-3 py-2">{{ is_scalar($oldVal) || $oldVal === null ? ($oldVal ?? '—') : json_encode($oldVal) }}</td>
                    <td class="px-3 py-2">{{ is_scalar($newVal) || $newVal === null ? ($newVal ?? '—') : json_encode($newVal) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-3 py-4 text-center text-gray-500">—</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
