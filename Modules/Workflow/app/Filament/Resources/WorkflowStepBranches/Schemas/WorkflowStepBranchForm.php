<?php

namespace Modules\Workflow\Filament\Resources\WorkflowStepBranches\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class WorkflowStepBranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextInput::make('workflow_instance_id')
                        ->label(FilamentUi::field('workflow_instance_id'))
                        ->numeric(),
                    TextInput::make('split_step_id')
                        ->label(FilamentUi::field('split_step_id'))
                        ->numeric(),
                    TextInput::make('branch_step_id')
                        ->label(FilamentUi::field('branch_step_id'))
                        ->numeric(),
                    TextInput::make('join_step_id')
                        ->label(FilamentUi::field('join_step_id'))
                        ->numeric(),
                    TextInput::make('status')
                        ->label(FilamentUi::field('status')),
                    TextInput::make('outcome')
                        ->label(FilamentUi::field('outcome')),
                    TextInput::make('started_at')
                        ->label(FilamentUi::field('started_at')),
                    TextInput::make('completed_at')
                        ->label(FilamentUi::field('completed_at')),
                    Textarea::make('meta')
                        ->label(FilamentUi::field('meta'))
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
