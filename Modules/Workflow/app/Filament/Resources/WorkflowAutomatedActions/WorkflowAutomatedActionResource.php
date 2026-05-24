<?php

namespace Modules\Workflow\Filament\Resources\WorkflowAutomatedActions;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Workflow\Filament\Resources\WorkflowAutomatedActions\Pages\CreateWorkflowAutomatedAction;
use Modules\Workflow\Filament\Resources\WorkflowAutomatedActions\Pages\EditWorkflowAutomatedAction;
use Modules\Workflow\Filament\Resources\WorkflowAutomatedActions\Pages\ListWorkflowAutomatedActions;
use Modules\Workflow\Filament\Resources\WorkflowAutomatedActions\Pages\ViewWorkflowAutomatedAction;
use Modules\Workflow\Filament\Resources\WorkflowAutomatedActions\Schemas\WorkflowAutomatedActionForm;
use Modules\Workflow\Filament\Resources\WorkflowAutomatedActions\Schemas\WorkflowAutomatedActionInfolist;
use Modules\Workflow\Filament\Resources\WorkflowAutomatedActions\Tables\WorkflowAutomatedActionsTable;
use Modules\Workflow\Models\WorkflowAutomatedAction;

class WorkflowAutomatedActionResource extends ModuleResource
{
    protected static ?string $model = WorkflowAutomatedAction::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return WorkflowAutomatedActionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkflowAutomatedActionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkflowAutomatedActionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkflowAutomatedActions::route('/'),
            'create' => CreateWorkflowAutomatedAction::route('/create'),
            'view' => ViewWorkflowAutomatedAction::route('/{record}'),
            'edit' => EditWorkflowAutomatedAction::route('/{record}/edit'),
        ];
    }
}
