@php
    $tenant = filament()->getTenant();
    $isSuperAdmin = auth()->user()?->isGlobalSuperAdmin() ?? false;
@endphp

@if ($tenant)
    <div class="flex items-center px-3 py-1">
        <span @class([
            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset',
            'bg-warning-50 text-warning-700 ring-warning-600/20 dark:bg-warning-400/10 dark:text-warning-400 dark:ring-warning-400/20' => $isSuperAdmin,
            'bg-primary-50 text-primary-700 ring-primary-600/20 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/20' => ! $isSuperAdmin,
        ])>
            {{ $tenant->name }}
        </span>
    </div>
@endif
