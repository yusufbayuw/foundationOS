<?php

namespace Modules\Workflow\Filament\Resources\Workflows;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Workflow\Filament\Resources\Workflows\Pages\CreateWorkflow;
use Modules\Workflow\Filament\Resources\Workflows\Pages\EditWorkflow;
use Modules\Workflow\Filament\Resources\Workflows\Pages\ListWorkflows;
use Modules\Workflow\Filament\Resources\Workflows\Pages\ViewWorkflow;
use Modules\Workflow\Filament\Resources\Workflows\RelationManagers\AutomatedActionsRelationManager;
use Modules\Workflow\Filament\Resources\Workflows\RelationManagers\StepsRelationManager;
use Modules\Workflow\Filament\Resources\Workflows\RelationManagers\TransitionsRelationManager;
use Modules\Workflow\Filament\Resources\Workflows\Schemas\WorkflowForm;
use Modules\Workflow\Filament\Resources\Workflows\Schemas\WorkflowInfolist;
use Modules\Workflow\Filament\Resources\Workflows\Tables\WorkflowsTable;
use Modules\Workflow\Models\Workflow;

class WorkflowResource extends LocalizedResource
{
    protected static ?string $model = Workflow::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    public static function form(Schema $schema): Schema
    {
        return WorkflowForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkflowsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkflowInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            StepsRelationManager::class,
            TransitionsRelationManager::class,
            AutomatedActionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkflows::route('/'),
            'create' => CreateWorkflow::route('/create'),
            'view' => ViewWorkflow::route('/{record}'),
            'edit' => EditWorkflow::route('/{record}/edit'),
        ];
    }
}
