<?php

namespace Modules\Workflow\Filament\Resources\Workflows\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Enums\WorkflowTriggerMode;

class WorkflowForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TenantField::make()
                ->default(Filament::getTenant()?->getKey())
                ->disabled(Filament::getTenant() !== null)
                ->dehydrated()
                ->required(),
            Select::make('organization_id')
                ->label(FilamentUi::field('organization_id'))
                ->relationship('organization', 'name', modifyQueryUsing: function (Builder $query): void {
                    if (Filament::getTenant()) {
                        $query->where('tenant_id', Filament::getTenant()->getKey());
                    }
                })
                ->nullable()
                ->helperText(FilamentUi::text('Leave blank for tenant-wide workflow')),
            TextInput::make('code')
                ->label(FilamentUi::field('code'))
                ->required()
                ->maxLength(255),
            TextInput::make('name')
                ->label(FilamentUi::field('name'))
                ->required()
                ->maxLength(255),
            TextInput::make('module')
                ->label(FilamentUi::field('module'))
                ->maxLength(255),
            TextInput::make('subject_type')
                ->label(FilamentUi::field('subject_type'))
                ->maxLength(255)
                ->helperText(FilamentUi::text('Enter the FQCN of the subject model')),
            Select::make('trigger_mode')
                ->label(FilamentUi::field('trigger_mode'))
                ->options(collect(WorkflowTriggerMode::cases())->mapWithKeys(fn (WorkflowTriggerMode $case) => [$case->value => str($case->value)->headline()])->all())
                ->default(WorkflowTriggerMode::Manual->value)
                ->required(),
            TextInput::make('version')
                ->label(FilamentUi::field('version'))
                ->numeric()
                ->default(1)
                ->required(),
            Select::make('status')
                ->label(FilamentUi::field('status'))
                ->options(collect(WorkflowDefinitionStatus::cases())->mapWithKeys(fn (WorkflowDefinitionStatus $case) => [$case->value => str($case->value)->headline()])->all())
                ->default(WorkflowDefinitionStatus::Draft->value)
                ->required(),
            Toggle::make('is_active')
                ->label(FilamentUi::field('is_active')),
            DateTimePicker::make('published_at')
                ->label(FilamentUi::field('published_at')),
            Textarea::make('description')
                ->label(FilamentUi::field('description'))
                ->columnSpanFull(),
        ]);
    }
}
