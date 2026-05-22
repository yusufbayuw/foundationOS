<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstances;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Workflow\Filament\Resources\WorkflowInstances\Pages\ListWorkflowInstances;
use Modules\Workflow\Filament\Resources\WorkflowInstances\Pages\ViewWorkflowInstance;
use Modules\Workflow\Filament\Resources\WorkflowInstances\RelationManagers\AssignmentsRelationManager;
use Modules\Workflow\Filament\Resources\WorkflowInstances\RelationManagers\LogsRelationManager;
use Modules\Workflow\Filament\Resources\WorkflowInstances\Schemas\WorkflowInstanceInfolist;
use Modules\Workflow\Filament\Resources\WorkflowInstances\Tables\WorkflowInstancesTable;
use Modules\Workflow\Models\WorkflowInstance;

class WorkflowInstanceResource extends LocalizedResource
{
    protected static ?string $model = WorkflowInstance::class;

    protected static ?string $recordTitleAttribute = 'subject_label';

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkflowInstanceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkflowInstancesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AssignmentsRelationManager::class,
            LogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkflowInstances::route('/'),
            'view' => ViewWorkflowInstance::route('/{record}'),
        ];
    }
}
