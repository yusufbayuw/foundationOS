<?php

namespace Modules\Workflow\Filament\Resources\WorkflowAssignments\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class WorkflowAssignmentForm
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
                    TextInput::make('assigned_to_type')
                        ->label(FilamentUi::field('assigned_to_type')),
                    TextInput::make('assigned_to_id')
                        ->label(FilamentUi::field('assigned_to_id'))
                        ->numeric(),
                    TextInput::make('assignment_role')
                        ->label(FilamentUi::field('assignment_role')),
                    TextInput::make('status')
                        ->label(FilamentUi::field('status')),
                    TextInput::make('outcome')
                        ->label(FilamentUi::field('outcome')),
                    TextInput::make('assigned_at')
                        ->label(FilamentUi::field('assigned_at')),
                    TextInput::make('claimed_at')
                        ->label(FilamentUi::field('claimed_at')),
                    TextInput::make('completed_at')
                        ->label(FilamentUi::field('completed_at')),
                    TextInput::make('due_at')
                        ->label(FilamentUi::field('due_at')),
                    Textarea::make('meta')
                        ->label(FilamentUi::field('meta'))
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
