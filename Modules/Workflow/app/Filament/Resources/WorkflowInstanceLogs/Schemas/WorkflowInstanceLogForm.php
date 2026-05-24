<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstanceLogs\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class WorkflowInstanceLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextInput::make('workflow_instance_id')
                        ->label(FilamentUi::field('workflow_instance_id'))
                        ->numeric(),
                    TextInput::make('step_id')
                        ->label(FilamentUi::field('step_id'))
                        ->numeric(),
                    TextInput::make('transition_id')
                        ->label(FilamentUi::field('transition_id'))
                        ->numeric(),
                    TextInput::make('actor_id')
                        ->label(FilamentUi::field('actor_id'))
                        ->numeric(),
                    TextInput::make('log_type')
                        ->label(FilamentUi::field('log_type')),
                    TextInput::make('action_taken')
                        ->label(FilamentUi::field('action_taken')),
                    TextInput::make('status_before')
                        ->label(FilamentUi::field('status_before')),
                    TextInput::make('status_after')
                        ->label(FilamentUi::field('status_after')),
                    Textarea::make('payload_before')
                        ->label(FilamentUi::field('payload_before'))
                        ->columnSpanFull(),
                    Textarea::make('payload_after')
                        ->label(FilamentUi::field('payload_after'))
                        ->columnSpanFull(),
                    TextInput::make('form_data_snapshot')
                        ->label(FilamentUi::field('form_data_snapshot')),
                    Textarea::make('notes')
                        ->label(FilamentUi::field('notes'))
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
