<?php

namespace Modules\Core\Services;

use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\Module;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantModule;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Models\UserTenantRole;

class TenantSetupProgress
{
    public function __construct(
        protected ModuleDependencyGraph $dependencyGraph,
        protected ProductProfileCatalog $productProfiles,
    ) {}

    /**
     * @return array{
     *     profile: array{code: string|null, label: string, version: string|null, capabilities: list<string>},
     *     steps: list<array{key: string, title: string, description: string, action_label: string, icon: string, is_complete: bool}>,
     *     completed_count: int,
     *     total_count: int,
     *     percentage: int,
     *     next_step: array{key: string, title: string, description: string, action_label: string, icon: string, is_complete: bool}|null,
     *     metrics: array{organizations: int, team_members: int, active_modules: int},
     *     active_modules: list<array{code: string, name: string}>,
     *     missing_modules: list<string>,
     *     subscription: array{status: string, plan: string|null}
     * }
     */
    public function forTenant(Tenant $tenant): array
    {
        $tenant->loadMissing('subscriptionPlan:id,name');

        $storedProfileCode = (string) ($tenant->product_profile_code ?? '');
        $hasStoredProfile = $storedProfileCode !== '' && $this->productProfiles->exists($storedProfileCode);
        $profileCode = $hasStoredProfile ? $storedProfileCode : null;
        $catalogProfileVersion = $profileCode === null
            ? null
            : $this->productProfiles->version($profileCode);
        $hasCurrentProfile = $hasStoredProfile
            && $tenant->product_profile_version === $catalogProfileVersion;

        $brandingSettings = TenantSetting::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('group', 'branding')
            ->whereIn('key', ['brand_logo', 'primary_color'])
            ->pluck('value', 'key');

        $organizationCount = Organization::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('is_active', true)
            ->count();

        $teamMemberCount = UserTenantRole::query()
            ->where('tenant_id', $tenant->getKey())
            ->active()
            ->distinct()
            ->count('user_id');

        $hasActiveAcademicYear = AcademicYear::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('is_active', true)
            ->exists();

        $hasActiveAcademicPeriod = AcademicPeriod::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('is_active', true)
            ->exists();

        $activeModules = TenantModule::query()
            ->select(['id', 'tenant_id', 'module_id'])
            ->where('tenant_id', $tenant->getKey())
            ->where('is_enabled', true)
            ->whereHas('module', fn ($query) => $query->where('is_active', true))
            ->with(['module' => fn ($query) => $query
                ->select(['id', 'code', 'name', 'sort_order'])
                ->where('is_active', true)])
            ->get()
            ->filter(fn (TenantModule $tenantModule): bool => $tenantModule->module !== null)
            ->sortBy(fn (TenantModule $tenantModule): int => $tenantModule->module->sort_order)
            ->map(fn (TenantModule $tenantModule): array => [
                'code' => str($tenantModule->module->code)->lower()->toString(),
                'name' => (string) $tenantModule->module->name,
            ])
            ->values();

        $enabledModuleCodes = $activeModules->pluck('code')->all();
        $availableModules = Module::query()
            ->where('is_active', true)
            ->get(['id', 'code', 'required_modules']);
        $dependencyInspection = $profileCode === null
            ? ['resolved' => [], 'missing' => [], 'cycles' => []]
            : $this->dependencyGraph->inspect(
                $availableModules,
                $this->productProfiles->moduleCodes($profileCode),
            );
        $requiredModuleCodes = array_values(array_unique([
            ...$dependencyInspection['resolved'],
            ...$dependencyInspection['missing'],
        ]));
        $missingModuleCodes = array_values(array_unique([
            ...array_diff($requiredModuleCodes, $enabledModuleCodes),
            ...$dependencyInspection['cycles'],
        ]));

        $steps = [
            [
                'key' => 'profile',
                'title' => 'Konfirmasi profil produk',
                'description' => match (true) {
                    ! $hasStoredProfile => 'Tenant lama ini belum memiliki profil produk tersimpan.',
                    ! $hasCurrentProfile => 'Versi profil produk perlu diperbarui agar sesuai dengan katalog saat ini.',
                    default => $this->productProfiles->label($profileCode).' versi '.$catalogProfileVersion.' aktif.',
                },
                'action_label' => $hasStoredProfile ? 'Perbarui profil' : 'Pilih profil',
                'icon' => 'heroicon-o-rectangle-stack',
                'is_complete' => $hasCurrentProfile,
            ],
            [
                'key' => 'branding',
                'title' => 'Lengkapi identitas merek',
                'description' => 'Tambahkan logo dan warna utama agar panel serta dokumen konsisten.',
                'action_label' => 'Atur branding',
                'icon' => 'heroicon-o-swatch',
                'is_complete' => filled($brandingSettings->get('brand_logo'))
                    && filled($brandingSettings->get('primary_color')),
            ],
            [
                'key' => 'organizations',
                'title' => 'Susun unit organisasi',
                'description' => $organizationCount > 0
                    ? "{$organizationCount} unit organisasi sudah terdaftar."
                    : 'Buat sekolah, kampus, atau unit kerja pertama di bawah tenant.',
                'action_label' => 'Kelola unit',
                'icon' => 'heroicon-o-building-office-2',
                'is_complete' => $organizationCount > 0,
            ],
            [
                'key' => 'team',
                'title' => 'Undang tim inti',
                'description' => $teamMemberCount > 1
                    ? "{$teamMemberCount} anggota tim sudah memiliki akses."
                    : 'Tambahkan minimal satu rekan dan tetapkan perannya.',
                'action_label' => 'Kelola akses',
                'icon' => 'heroicon-o-user-group',
                'is_complete' => $teamMemberCount > 1,
            ],
            [
                'key' => 'modules',
                'title' => 'Verifikasi modul produk',
                'description' => ! $hasStoredProfile
                    ? 'Pilih profil produk sebelum memverifikasi modul wajib.'
                    : ($missingModuleCodes === []
                    ? 'Semua modul wajib untuk profil ini sudah aktif.'
                    : count($missingModuleCodes).' modul wajib belum aktif.'),
                'action_label' => 'Buka modul',
                'icon' => 'heroicon-o-puzzle-piece',
                'is_complete' => $hasStoredProfile && $missingModuleCodes === [],
            ],
        ];

        $setupTasks = $profileCode === null ? [] : $this->productProfiles->setupTasks($profileCode);

        if (in_array('academic_year', $setupTasks, true)) {
            $steps[] = [
                'key' => 'academic_year',
                'title' => 'Aktifkan tahun akademik',
                'description' => $hasActiveAcademicYear
                    ? 'Tahun akademik aktif sudah tersedia untuk transaksi akademik.'
                    : 'Buat dan aktifkan tahun akademik sebelum memproses penerimaan atau kelas.',
                'action_label' => 'Kelola tahun akademik',
                'icon' => 'heroicon-o-calendar-days',
                'is_complete' => $hasActiveAcademicYear,
            ];
        }

        if (in_array('academic_period', $setupTasks, true)) {
            $steps[] = [
                'key' => 'academic_period',
                'title' => 'Aktifkan periode akademik',
                'description' => $hasActiveAcademicPeriod
                    ? 'Periode akademik aktif siap menjadi default transaksi.'
                    : 'Buat semester atau periode aktif untuk melengkapi konteks transaksi akademik.',
                'action_label' => 'Kelola periode akademik',
                'icon' => 'heroicon-o-calendar-date-range',
                'is_complete' => $hasActiveAcademicPeriod,
            ];
        }

        $completedCount = collect($steps)->where('is_complete', true)->count();
        $totalCount = count($steps);

        return [
            'profile' => [
                'code' => $profileCode,
                'label' => $profileCode === null
                    ? 'Profil belum dikonfirmasi'
                    : $this->productProfiles->label($profileCode),
                'version' => $catalogProfileVersion,
                'capabilities' => $profileCode === null
                    ? []
                    : $this->productProfiles->capabilities($profileCode),
            ],
            'steps' => $steps,
            'completed_count' => $completedCount,
            'total_count' => $totalCount,
            'percentage' => $totalCount > 0 ? (int) round(($completedCount / $totalCount) * 100) : 100,
            'next_step' => collect($steps)->firstWhere('is_complete', false),
            'metrics' => [
                'organizations' => $organizationCount,
                'team_members' => $teamMemberCount,
                'active_modules' => $activeModules->count(),
            ],
            'active_modules' => $activeModules->all(),
            'missing_modules' => $missingModuleCodes,
            'subscription' => [
                'status' => (string) ($tenant->status ?: 'unknown'),
                'plan' => $tenant->subscriptionPlan?->name,
            ],
        ];
    }
}
