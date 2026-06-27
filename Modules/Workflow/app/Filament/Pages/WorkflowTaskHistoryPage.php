<?php

namespace Modules\Workflow\Filament\Pages;

use App\Support\TypedValue;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Facades\Filament;
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

    /**
     * @return list<WorkflowAssignment>
     */
    public function getHistory(): array
    {
        $user = auth()->user();
        $tenant = Filament::getTenant();
        $userId = $user?->getAuthIdentifier();
        $tenantId = TypedValue::tenantKey($tenant?->getKey());

        return array_values(WorkflowAssignment::query()
            ->with(['instance.workflow', 'instance.currentStep', 'instance.requester'])
            ->where('assigned_to_type', 'user')
            ->when($userId !== null, fn (Builder $query) => $query->where('assigned_to_id', $userId))
            ->when($userId === null, fn (Builder $query) => $query->whereRaw('1 = 0'))
            ->whereIn('status', ['completed', 'cancelled', 'expired'])
            ->when($tenantId !== null, fn (Builder $query) => $query->whereHas('instance', fn (Builder $inner) => $inner->where($inner->getModel()->qualifyColumn('tenant_id'), $tenantId)))
            ->latest('updated_at')
            ->limit(50)
            ->get()
            ->values()
            ->all());
    }
}
