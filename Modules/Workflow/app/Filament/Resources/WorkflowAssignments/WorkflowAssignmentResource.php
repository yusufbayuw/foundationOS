<?php

namespace Modules\Workflow\Filament\Resources\WorkflowAssignments;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Workflow\Filament\Resources\WorkflowAssignments\Pages\CreateWorkflowAssignment;
use Modules\Workflow\Filament\Resources\WorkflowAssignments\Pages\EditWorkflowAssignment;
use Modules\Workflow\Filament\Resources\WorkflowAssignments\Pages\ListWorkflowAssignments;
use Modules\Workflow\Filament\Resources\WorkflowAssignments\Pages\ViewWorkflowAssignment;
use Modules\Workflow\Filament\Resources\WorkflowAssignments\Schemas\WorkflowAssignmentForm;
use Modules\Workflow\Filament\Resources\WorkflowAssignments\Schemas\WorkflowAssignmentInfolist;
use Modules\Workflow\Filament\Resources\WorkflowAssignments\Tables\WorkflowAssignmentsTable;
use Modules\Workflow\Models\WorkflowAssignment;

class WorkflowAssignmentResource extends ModuleResource
{
    protected static ?string $model = WorkflowAssignment::class;

    protected static ?string $recordTitleAttribute = 'status';

    public static function form(Schema $schema): Schema
    {
        return WorkflowAssignmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkflowAssignmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkflowAssignmentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkflowAssignments::route('/'),
            'create' => CreateWorkflowAssignment::route('/create'),
            'view' => ViewWorkflowAssignment::route('/{record}'),
            'edit' => EditWorkflowAssignment::route('/{record}/edit'),
        ];
    }
}
