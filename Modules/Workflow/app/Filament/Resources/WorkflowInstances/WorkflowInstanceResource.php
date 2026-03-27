<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstances;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Workflow\Filament\Resources\WorkflowInstances\Pages\ListWorkflowInstances;
use Modules\Workflow\Filament\Resources\WorkflowInstances\Pages\ViewWorkflowInstance;
use Modules\Workflow\Filament\Resources\WorkflowInstances\RelationManagers\AssignmentsRelationManager;
use Modules\Workflow\Filament\Resources\WorkflowInstances\RelationManagers\LogsRelationManager;
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
        return $schema->components([
            TextEntry::make('workflow.name')->label('Workflow'),
            TextEntry::make('subject_label')->label('Subject Label')->placeholder('-'),
            TextEntry::make('status')->label('Status')->badge(),
            TextEntry::make('requester.name')->label('Requester'),
            TextEntry::make('currentStep.name')->label('Current Step')->placeholder('-'),
            TextEntry::make('started_at')->label('Started At')->dateTime(),
            TextEntry::make('due_at')->label('Due At')->dateTime()->placeholder('-'),
            TextEntry::make('completed_at')->label('Completed At')->dateTime()->placeholder('-'),
            KeyValueEntry::make('context_data')->label('Context Data')->columnSpanFull(),
            KeyValueEntry::make('form_data')->label('Form Data')->columnSpanFull(),
            KeyValueEntry::make('computed_data')->label('Computed Data')->columnSpanFull(),
            KeyValueEntry::make('current_assignees')->label('Current Assignees')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('workflow.name')->label('Workflow')->searchable(),
            TextColumn::make('subject_label')->label('Subject Label')->searchable()->placeholder('-'),
            TextColumn::make('requester.name')->label('Requester')->searchable(),
            TextColumn::make('currentStep.name')->label('Current Step')->placeholder('-'),
            TextColumn::make('status')->label('Status')->badge(),
            TextColumn::make('started_at')->label('Started At')->dateTime()->sortable(),
            TextColumn::make('due_at')->label('Due At')->dateTime()->sortable()->placeholder('-'),
        ]);
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

    public static function getModelLabel(): string
    {
        return 'Workflow Instance';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Workflow Instances';
    }

    public static function getNavigationLabel(): string
    {
        return 'Workflow Instances';
    }
}
