<?php

namespace Modules\Workflow\Filament\Resources\WorkflowDelegations;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Workflow\Filament\Resources\WorkflowDelegations\Pages\CreateWorkflowDelegation;
use Modules\Workflow\Filament\Resources\WorkflowDelegations\Pages\EditWorkflowDelegation;
use Modules\Workflow\Filament\Resources\WorkflowDelegations\Pages\ListWorkflowDelegations;
use Modules\Workflow\Filament\Resources\WorkflowDelegations\Pages\ViewWorkflowDelegation;
use Modules\Workflow\Filament\Resources\WorkflowDelegations\Schemas\WorkflowDelegationForm;
use Modules\Workflow\Filament\Resources\WorkflowDelegations\Schemas\WorkflowDelegationInfolist;
use Modules\Workflow\Filament\Resources\WorkflowDelegations\Tables\WorkflowDelegationsTable;
use Modules\Workflow\Models\WorkflowDelegation;

class WorkflowDelegationResource extends ModuleResource
{
    protected static ?string $model = WorkflowDelegation::class;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return WorkflowDelegationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkflowDelegationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkflowDelegationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkflowDelegations::route('/'),
            'create' => CreateWorkflowDelegation::route('/create'),
            'view' => ViewWorkflowDelegation::route('/{record}'),
            'edit' => EditWorkflowDelegation::route('/{record}/edit'),
        ];
    }
}
