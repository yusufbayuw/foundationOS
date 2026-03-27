<?php

namespace Modules\Workflow\Filament\Resources\Workflows;

use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Enums\WorkflowTriggerMode;
use Modules\Workflow\Filament\Resources\Workflows\Pages\CreateWorkflow;
use Modules\Workflow\Filament\Resources\Workflows\Pages\EditWorkflow;
use Modules\Workflow\Filament\Resources\Workflows\Pages\ListWorkflows;
use Modules\Workflow\Filament\Resources\Workflows\Pages\ViewWorkflow;
use Modules\Workflow\Filament\Resources\Workflows\RelationManagers\AutomatedActionsRelationManager;
use Modules\Workflow\Filament\Resources\Workflows\RelationManagers\StepsRelationManager;
use Modules\Workflow\Filament\Resources\Workflows\RelationManagers\TransitionsRelationManager;
use Modules\Workflow\Models\Workflow;

class WorkflowResource extends LocalizedResource
{
    protected static ?string $model = Workflow::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('tenant_id')
                ->label('Tenant')
                ->relationship('tenant', 'name')
                ->default(Filament::getTenant()?->getKey())
                ->disabled(Filament::getTenant() !== null)
                ->dehydrated()
                ->required(),
            Select::make('organization_id')
                ->label('Organization')
                ->relationship('organization', 'name', modifyQueryUsing: function (Builder $query): void {
                    if (Filament::getTenant()) {
                        $query->where('tenant_id', Filament::getTenant()->getKey());
                    }
                })
                ->nullable()
                ->helperText('Kosongkan untuk workflow tenant-wide.'),
            TextInput::make('code')
                ->label('Code')
                ->required()
                ->maxLength(255),
            TextInput::make('name')
                ->label('Name')
                ->required()
                ->maxLength(255),
            TextInput::make('module')
                ->label('Module')
                ->maxLength(255),
            TextInput::make('subject_type')
                ->label('Subject Type')
                ->maxLength(255)
                ->helperText('Isi FQCN model subject, misalnya Modules\\Procurement\\Models\\PurchaseRequisition.'),
            Select::make('trigger_mode')
                ->label('Trigger Mode')
                ->options(collect(WorkflowTriggerMode::cases())->mapWithKeys(fn (WorkflowTriggerMode $case) => [$case->value => str($case->value)->headline()])->all())
                ->default(WorkflowTriggerMode::Manual->value)
                ->required(),
            TextInput::make('version')
                ->label('Version')
                ->numeric()
                ->default(1)
                ->required(),
            Select::make('status')
                ->label('Status')
                ->options(collect(WorkflowDefinitionStatus::cases())->mapWithKeys(fn (WorkflowDefinitionStatus $case) => [$case->value => str($case->value)->headline()])->all())
                ->default(WorkflowDefinitionStatus::Draft->value)
                ->required(),
            Toggle::make('is_active')
                ->label('Is Active')
                ->default(false),
            DateTimePicker::make('published_at')
                ->label('Published At'),
            Textarea::make('description')
                ->label('Description')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Name')->searchable(),
                TextColumn::make('code')->label('Code')->searchable(),
                TextColumn::make('module')->label('Module')->searchable()->placeholder('-'),
                TextColumn::make('organization.name')->label('Organization')->placeholder('Tenant-wide'),
                TextColumn::make('version')->label('Version')->sortable(),
                TextColumn::make('status')->label('Status')->badge(),
                IconColumn::make('is_active')->label('Is Active')->boolean(),
                TextColumn::make('published_at')->label('Published At')->dateTime()->sortable()->placeholder('-'),
                TextColumn::make('updated_at')->label('Updated At')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('name')->label('Name'),
            TextEntry::make('code')->label('Code'),
            TextEntry::make('tenant.name')->label('Tenant'),
            TextEntry::make('organization.name')->label('Organization')->placeholder('Tenant-wide'),
            TextEntry::make('module')->label('Module')->placeholder('-'),
            TextEntry::make('subject_type')->label('Subject Type')->placeholder('-')->columnSpanFull(),
            TextEntry::make('trigger_mode')->label('Trigger Mode')->badge(),
            TextEntry::make('version')->label('Version'),
            TextEntry::make('status')->label('Status')->badge(),
            TextEntry::make('is_active')
                ->label('Active')
                ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                ->badge()
                ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
            TextEntry::make('published_at')->label('Published At')->dateTime()->placeholder('-'),
            TextEntry::make('creator.name')->label('Created By')->placeholder('-'),
            TextEntry::make('updater.name')->label('Updated By')->placeholder('-'),
            TextEntry::make('description')->label('Description')->placeholder('-')->columnSpanFull(),
            KeyValueEntry::make('steps_summary')
                ->label('Workflow Snapshot')
                ->state(fn (Workflow $record): array => [
                    'steps_count' => (string) $record->steps()->count(),
                    'transitions_count' => (string) $record->transitions()->count(),
                    'automated_actions_count' => (string) $record->automatedActions()->count(),
                    'active_instances_count' => (string) $record->instances()->where('status', 'running')->count(),
                ])
                ->columnSpanFull(),
        ]);
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

    public static function getModelLabel(): string
    {
        return 'Workflow';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Workflows';
    }

    public static function getNavigationLabel(): string
    {
        return 'Workflows';
    }
}
