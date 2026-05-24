<?php

namespace Modules\Workflow\Filament\Resources\WorkflowAutomatedActions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class WorkflowAutomatedActionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextInput::make('workflow_id')
                        ->label(FilamentUi::field('workflow_id'))
                        ->numeric(),
                    TextInput::make('step_id')
                        ->label(FilamentUi::field('step_id'))
                        ->numeric(),
                    TextInput::make('trigger_event')
                        ->label(FilamentUi::field('trigger_event')),
                    TextInput::make('action_type')
                        ->label(FilamentUi::field('action_type')),
                    TextInput::make('name')
                        ->label(FilamentUi::field('name')),
                    TextInput::make('config')
                        ->label(FilamentUi::field('config')),
                    TextInput::make('is_active')
                        ->label(FilamentUi::field('is_active')),
                    TextInput::make('sort_order')
                        ->label(FilamentUi::field('sort_order'))
                        ->numeric(),
                ])
                ->columns(2),
        ]);
    }
}
