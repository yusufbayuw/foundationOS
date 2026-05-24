<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstanceLogs;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Workflow\Filament\Resources\WorkflowInstanceLogs\Pages\CreateWorkflowInstanceLog;
use Modules\Workflow\Filament\Resources\WorkflowInstanceLogs\Pages\EditWorkflowInstanceLog;
use Modules\Workflow\Filament\Resources\WorkflowInstanceLogs\Pages\ListWorkflowInstanceLogs;
use Modules\Workflow\Filament\Resources\WorkflowInstanceLogs\Pages\ViewWorkflowInstanceLog;
use Modules\Workflow\Filament\Resources\WorkflowInstanceLogs\Schemas\WorkflowInstanceLogForm;
use Modules\Workflow\Filament\Resources\WorkflowInstanceLogs\Schemas\WorkflowInstanceLogInfolist;
use Modules\Workflow\Filament\Resources\WorkflowInstanceLogs\Tables\WorkflowInstanceLogsTable;
use Modules\Workflow\Models\WorkflowInstanceLog;

class WorkflowInstanceLogResource extends ModuleResource
{
    protected static ?string $model = WorkflowInstanceLog::class;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return WorkflowInstanceLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkflowInstanceLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkflowInstanceLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkflowInstanceLogs::route('/'),
            'create' => CreateWorkflowInstanceLog::route('/create'),
            'view' => ViewWorkflowInstanceLog::route('/{record}'),
            'edit' => EditWorkflowInstanceLog::route('/{record}/edit'),
        ];
    }
}
