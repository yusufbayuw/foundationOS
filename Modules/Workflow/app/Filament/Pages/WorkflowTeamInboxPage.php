<?php

namespace Modules\Workflow\Filament\Pages;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Support\FilamentUi;
use Modules\Workflow\Models\WorkflowAssignment;

class WorkflowTeamInboxPage extends WorkflowInboxPage
{
    protected static string $routePath = '/workflow/team-inbox';

    protected static ?int $navigationSort = 32;

    public static function getNavigationLabel(): string
    {
        return 'Workflow Team Inbox';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return FilamentUi::module('Workflow');
    }

    protected function baseAssignmentsQuery(): Builder
    {
        $tenant = Filament::getTenant();
        $user = auth()->user();
        $organizationIds = $user?->userTenantRoles()
            ->when($tenant, fn (Builder $query) => $query->where('tenant_id', $tenant->getKey()))
            ->pluck('organization_id')
            ->filter()
            ->unique()
            ->values()
            ->all() ?? [];

        return WorkflowAssignment::query()
            ->with(['instance.workflow', 'instance.currentStep', 'instance.requester'])
            ->where('assigned_to_type', 'user')
            ->when($tenant, function (Builder $query) use ($tenant, $organizationIds, $user): void {
                $query->whereHas('instance', function (Builder $inner) use ($tenant, $organizationIds, $user): void {
                    $inner->where('tenant_id', $tenant->getKey());

                    if (! $user?->isGlobalSuperAdmin() && $organizationIds !== []) {
                        $inner->where(function (Builder $scoped) use ($organizationIds): void {
                            $scoped->whereNull('organization_id')
                                ->orWhereIn('organization_id', $organizationIds);
                        });
                    }
                });
            })
            ->when(! $tenant, fn (Builder $query) => $query->whereRaw('1 = 0'));
    }
}
