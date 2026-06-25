<?php

namespace Modules\Finance\Filament\Resources\Budgets\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Finance\Filament\Resources\Budgets\BudgetResource;
use Modules\Finance\Models\Budget;
use Modules\Workflow\Filament\Actions\OpenActiveSubjectWorkflowAction;
use Modules\Workflow\Filament\Actions\StartSubjectWorkflowAction;

class ViewBudget extends ViewRecord
{
    protected static string $resource = BudgetResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Budget $record */
        $record = $this->getRecord();

        return [
            OpenActiveSubjectWorkflowAction::make($this),
            StartSubjectWorkflowAction::make(
                $this,
                'Budget approval workflow started.',
                'Unable to start budget approval workflow.',
                visibleWhen: fn (): bool => in_array($record->status, ['draft', 'revision_required'], true),
            ),
            EditAction::make(),
        ];
    }
}
