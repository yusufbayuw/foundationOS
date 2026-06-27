<?php

namespace Modules\Workflow\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Support\FilamentUi;
use Modules\Workflow\Models\WorkflowAssignment;

class WorkflowTaskHistoryPage extends Page
{
    use HasPageShield;

    protected static string $routePath = '/workflow/history';

    protected string $view = 'workflow::filament.pages.workflow-task-history';

    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return FilamentUi::module('Workflow');
    }

    public function getHistory(): array
    {
        $user = auth()->user();
        $tenant = current_tenant_model();

        return WorkflowAssignment::query()
            ->with(['instance.workflow', 'instance.currentStep', 'instance.requester'])
            ->where('assigned_to_type', 'user')
            ->when($user, fn (Builder $query) => $query->where('assigned_to_id', $user->getAuthIdentifier()))
            ->whereIn('status', ['completed', 'cancelled', 'expired'])
            ->when($tenant, fn (Builder $query) => $query->whereHas('instance', fn (Builder $inner) => $inner->where('tenant_id', $tenant->getKey())))
            ->latest('updated_at')
            ->limit(50)
            ->get()
            ->all();
    }
}
