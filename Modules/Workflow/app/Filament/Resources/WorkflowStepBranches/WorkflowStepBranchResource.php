<?php

namespace Modules\Workflow\Filament\Resources\WorkflowStepBranches;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Workflow\Filament\Resources\WorkflowStepBranches\Pages\CreateWorkflowStepBranch;
use Modules\Workflow\Filament\Resources\WorkflowStepBranches\Pages\EditWorkflowStepBranch;
use Modules\Workflow\Filament\Resources\WorkflowStepBranches\Pages\ListWorkflowStepBranches;
use Modules\Workflow\Filament\Resources\WorkflowStepBranches\Pages\ViewWorkflowStepBranch;
use Modules\Workflow\Filament\Resources\WorkflowStepBranches\Schemas\WorkflowStepBranchForm;
use Modules\Workflow\Filament\Resources\WorkflowStepBranches\Schemas\WorkflowStepBranchInfolist;
use Modules\Workflow\Filament\Resources\WorkflowStepBranches\Tables\WorkflowStepBranchesTable;
use Modules\Workflow\Models\WorkflowStepBranch;

class WorkflowStepBranchResource extends ModuleResource
{
    protected static ?string $model = WorkflowStepBranch::class;

    /**
     * Cross-step branch rows are an engine-owned runtime primitive. The
     * current coordinator only supports same-step quorum aggregation, so
     * manual CRUD would create branch records that the engine never consumes.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        return false;
    }

    protected static ?string $recordTitleAttribute = 'status';

    public static function form(Schema $schema): Schema
    {
        return WorkflowStepBranchForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkflowStepBranchInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkflowStepBranchesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkflowStepBranches::route('/'),
            'create' => CreateWorkflowStepBranch::route('/create'),
            'view' => ViewWorkflowStepBranch::route('/{record}'),
            'edit' => EditWorkflowStepBranch::route('/{record}/edit'),
        ];
    }
}
