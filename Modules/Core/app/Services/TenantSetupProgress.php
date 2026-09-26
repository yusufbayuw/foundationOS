<?php

namespace Modules\Core\Services;

use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
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
    ) {
    }

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
     *     subscription: array{status: string, status_label: string, plan: string|null}
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
                'title' => (string) __('core::core.setup_center.steps.profile.title'),
                'description' => match (true) {
                    !$hasStoredProfile => (string) __('core::core.setup_center.steps.profile.no_saved_profile'),
                    !$hasCurrentProfile => (string) __('core::core.setup_center.steps.profile.version_outdated'),
                    default => (string) __('core::core.setup_center.steps.profile.active', [
                        'profile' => $this->productProfiles->label((string) $profileCode),
                        'version' => $catalogProfileVersion,
                    ]),
                },
                'action_label' => (string) __(
                    $hasStoredProfile
                        ? 'core::core.setup_center.steps.profile.update_action'
                        : 'core::core.setup_center.steps.profile.choose_action',
                ),
                'icon' => 'heroicon-o-rectangle-stack',
                'is_complete' => $hasCurrentProfile,
            ],
            [
                'key' => 'branding',
                'title' => (string) __('core::core.setup_center.steps.branding.title'),
                'description' => (string) __('core::core.setup_center.steps.branding.description'),
                'action_label' => (string) __('core::core.setup_center.steps.branding.action'),
                'icon' => 'heroicon-o-swatch',
                'is_complete' => filled($brandingSettings->get('brand_logo'))
                    && filled($brandingSettings->get('primary_color')),
            ],
            [
                'key' => 'organizations',
                'title' => (string) __('core::core.setup_center.steps.organizations.title'),
                'description' => $organizationCount > 0
                    ? (string) __('core::core.setup_center.steps.organizations.complete', ['count' => $organizationCount])
                    : (string) __('core::core.setup_center.steps.organizations.empty'),
                'action_label' => (string) __('core::core.setup_center.steps.organizations.action'),
                'icon' => 'heroicon-o-building-office-2',
                'is_complete' => $organizationCount > 0,
            ],
            [
                'key' => 'team',
                'title' => (string) __('core::core.setup_center.steps.team.title'),
                'description' => $teamMemberCount > 1
                    ? (string) __('core::core.setup_center.steps.team.complete', ['count' => $teamMemberCount])
                    : (string) __('core::core.setup_center.steps.team.incomplete'),
                'action_label' => (string) __('core::core.setup_center.steps.team.action'),
                'icon' => 'heroicon-o-user-group',
                'is_complete' => $teamMemberCount > 1,
            ],
            [
                'key' => 'modules',
                'title' => (string) __('core::core.setup_center.steps.modules.title'),
                'description' => !$hasStoredProfile
                    ? (string) __('core::core.setup_center.steps.modules.choose_profile_first')
                    : ($missingModuleCodes === []
                        ? (string) __('core::core.setup_center.steps.modules.complete')
                        : (string) __('core::core.setup_center.steps.modules.missing', ['count' => count($missingModuleCodes)])),
                'action_label' => (string) __('core::core.setup_center.steps.modules.action'),
                'icon' => 'heroicon-o-puzzle-piece',
                'is_complete' => $hasStoredProfile && $missingModuleCodes === [],
            ],
        ];

        $setupTasks = $profileCode === null ? [] : $this->productProfiles->setupTasks($profileCode);

        if (in_array('academic_year', $setupTasks, true)) {
            $steps[] = [
                'key' => 'academic_year',
                'title' => (string) __('core::core.setup_center.steps.academic_year.title'),
                'description' => $hasActiveAcademicYear
                    ? (string) __('core::core.setup_center.steps.academic_year.complete')
                    : (string) __('core::core.setup_center.steps.academic_year.incomplete'),
                'action_label' => (string) __('core::core.setup_center.steps.academic_year.action'),
                'icon' => 'heroicon-o-calendar-days',
                'is_complete' => $hasActiveAcademicYear,
            ];
        }

        if (in_array('academic_period', $setupTasks, true)) {
            $steps[] = [
                'key' => 'academic_period',
                'title' => (string) __('core::core.setup_center.steps.academic_period.title'),
                'description' => $hasActiveAcademicPeriod
                    ? (string) __('core::core.setup_center.steps.academic_period.complete')
                    : (string) __('core::core.setup_center.steps.academic_period.incomplete'),
                'action_label' => (string) __('core::core.setup_center.steps.academic_period.action'),
                'icon' => 'heroicon-o-calendar-date-range',
                'is_complete' => $hasActiveAcademicPeriod,
            ];
        }

        $completedCount = collect($steps)->where('is_complete', true)->count();
        $totalCount = count($steps);
        $status = (string) ($tenant->status ?: 'unknown');
        $statusKey = "core::core.setup_center.statuses.{$status}";

        return [
            'profile' => [
                'code' => $profileCode,
                'label' => $profileCode === null
                    ? (string) __('core::core.setup_center.profile_not_confirmed')
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
                'status' => $status,
                'status_label' => app('translator')->has($statusKey)
                    ? (string) __($statusKey)
                    : str($status)->headline()->toString(),
                'plan' => $tenant->subscriptionPlan?->name,
            ],
        ];
    }
}
