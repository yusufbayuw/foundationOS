<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <section class="rounded-2xl bg-primary-50 p-6 ring-1 ring-primary-200 dark:bg-primary-950/30 dark:ring-primary-800 xl:col-span-2">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex flex-col gap-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-filament::badge color="primary">
                            {{ $setup['profile']['label'] }}
                        </x-filament::badge>
                        @if ($setup['profile']['version'])
                            <x-filament::badge color="gray">
                                Versi {{ $setup['profile']['version'] }}
                            </x-filament::badge>
                        @endif
                    </div>

                    <div>
                        <p class="text-sm font-medium text-primary-700 dark:text-primary-300">
                            Progress kesiapan organisasi
                        </p>
                        <p class="mt-1 text-4xl font-bold tracking-tight text-gray-950 dark:text-white">
                            {{ $setup['percentage'] }}%
                        </p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                            {{ $setup['completed_count'] }} dari {{ $setup['total_count'] }} langkah selesai
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 sm:min-w-80">
                    <div class="rounded-xl bg-white p-3 text-center ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                        <p class="text-2xl font-semibold text-gray-950 dark:text-white">{{ $setup['metrics']['organizations'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Unit</p>
                    </div>
                    <div class="rounded-xl bg-white p-3 text-center ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                        <p class="text-2xl font-semibold text-gray-950 dark:text-white">{{ $setup['metrics']['team_members'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Anggota</p>
                    </div>
                    <div class="rounded-xl bg-white p-3 text-center ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                        <p class="text-2xl font-semibold text-gray-950 dark:text-white">{{ $setup['metrics']['active_modules'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Modul</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-2" aria-label="Kapabilitas profil produk">
                @foreach ($setup['profile']['capabilities'] as $capability)
                    <span class="rounded-full bg-white px-3 py-1 text-xs font-medium text-gray-700 ring-1 ring-gray-950/10 dark:bg-gray-900 dark:text-gray-200 dark:ring-white/10">
                        {{ str($capability)->headline() }}
                    </span>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-start gap-3">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-warning-50 text-warning-600 dark:bg-warning-400/10 dark:text-warning-400">
                    <x-filament::icon
                        :icon="$setup['next_step']['icon'] ?? 'heroicon-o-check-circle'"
                        class="size-5"
                    />
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        {{ $setup['next_step'] ? 'Langkah berikutnya' : 'Siap digunakan' }}
                    </p>
                    <h2 class="mt-1 text-base font-semibold text-gray-950 dark:text-white">
                        {{ $setup['next_step']['title'] ?? 'Setup dasar selesai' }}
                    </h2>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        {{ $setup['next_step']['description'] ?? 'Semua fondasi utama telah siap. Tim dapat mulai menjalankan proses operasional.' }}
                    </p>

                    @if (filled($setup['next_step']['action_url'] ?? null))
                        <x-filament::button
                            tag="a"
                            :href="$setup['next_step']['action_url']"
                            :icon="$setup['next_step']['icon']"
                            size="sm"
                            class="mt-4"
                        >
                            {{ $setup['next_step']['action_label'] }}
                        </x-filament::button>
                    @endif
                </div>
            </div>
        </section>
    </div>

    <x-filament::section
        heading="Checklist kesiapan"
        description="Progress dihitung langsung dari data tenant, bukan checklist manual."
        icon="heroicon-o-clipboard-document-check"
    >
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            @foreach ($setup['steps'] as $step)
                <article
                    wire:key="setup-step-{{ $step['key'] }}"
                    class="flex flex-col gap-4 rounded-xl p-4 ring-1 {{ $step['is_complete'] ? 'bg-success-50/60 ring-success-200 dark:bg-success-950/20 dark:ring-success-800' : 'bg-white ring-gray-950/10 dark:bg-gray-900 dark:ring-white/10' }}"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl {{ $step['is_complete'] ? 'bg-success-100 text-success-700 dark:bg-success-400/10 dark:text-success-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' }}">
                                <x-filament::icon :icon="$step['icon']" class="size-5" />
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-semibold text-gray-950 dark:text-white">
                                    {{ $step['title'] }}
                                </h3>
                                <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-300">
                                    {{ $step['description'] }}
                                </p>
                            </div>
                        </div>

                        <x-filament::badge :color="$step['is_complete'] ? 'success' : 'warning'">
                            {{ $step['is_complete'] ? 'Selesai' : 'Perlu tindakan' }}
                        </x-filament::badge>
                    </div>

                    @if (filled($step['action_url']))
                        <div class="flex justify-end">
                            <x-filament::button
                                tag="a"
                                :href="$step['action_url']"
                                :icon="$step['icon']"
                                color="gray"
                                size="xs"
                            >
                                {{ $step['action_label'] }}
                            </x-filament::button>
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
    </x-filament::section>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-filament::section
            heading="Modul produk aktif"
            description="Implementasi teknis yang menopang profil produk tenant."
            icon="heroicon-o-puzzle-piece"
            class="lg:col-span-2"
        >
            <div class="flex flex-wrap gap-2">
                @forelse ($setup['active_modules'] as $module)
                    <x-filament::badge color="gray">
                        {{ $module['name'] }}
                    </x-filament::badge>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada modul aktif.</p>
                @endforelse
            </div>

            @if ($setup['missing_modules'] !== [])
                <div class="mt-4 rounded-xl bg-danger-50 p-4 text-sm text-danger-700 ring-1 ring-danger-200 dark:bg-danger-950/20 dark:text-danger-300 dark:ring-danger-800">
                    Modul wajib belum aktif: {{ collect($setup['missing_modules'])->map(fn ($code) => str($code)->headline())->join(', ') }}.
                </div>
            @endif
        </x-filament::section>

        <x-filament::section
            heading="Status layanan"
            description="Kondisi tenant dan paket saat ini."
            icon="heroicon-o-shield-check"
        >
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-sm text-gray-600 dark:text-gray-300">Status tenant</span>
                    <x-filament::badge :color="match ($setup['subscription']['status']) {
                        'active' => 'success',
                        'trial' => 'warning',
                        'past_due', 'suspended' => 'danger',
                        default => 'gray',
                    }">
                        {{ str($setup['subscription']['status'])->headline() }}
                    </x-filament::badge>
                </div>
                <div class="flex items-center justify-between gap-3 border-t border-gray-200 pt-4 dark:border-gray-700">
                    <span class="text-sm text-gray-600 dark:text-gray-300">Paket langganan</span>
                    <span class="text-sm font-medium text-gray-950 dark:text-white">
                        {{ $setup['subscription']['plan'] ?? 'Belum ditetapkan' }}
                    </span>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
