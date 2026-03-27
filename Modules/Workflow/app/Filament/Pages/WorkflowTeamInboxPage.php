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

        return WorkflowAssignment::query()
            ->with(['instance.workflow', 'instance.currentStep', 'instance.requester'])
            ->where('assigned_to_type', 'user')
            ->when($tenant, fn (Builder $query) => $query->whereHas('instance', fn (Builder $inner) => $inner->where('tenant_id', $tenant->getKey())))
            ->when(! $tenant, fn (Builder $query) => $query->whereRaw('1 = 0'));
    }
}
