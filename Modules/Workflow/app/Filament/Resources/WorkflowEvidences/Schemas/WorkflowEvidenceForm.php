<?php

namespace Modules\Workflow\Filament\Resources\WorkflowEvidences\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class WorkflowEvidenceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextInput::make('workflow_instance_id')
                        ->label(FilamentUi::field('workflow_instance_id'))
                        ->numeric(),
                    TextInput::make('workflow_step_id')
                        ->label(FilamentUi::field('workflow_step_id'))
                        ->numeric(),
                    TextInput::make('uploaded_by')
                        ->label(FilamentUi::field('uploaded_by')),
                    TextInput::make('file_path')
                        ->label(FilamentUi::field('file_path')),
                    TextInput::make('original_filename')
                        ->label(FilamentUi::field('original_filename')),
                    TextInput::make('mime_type')
                        ->label(FilamentUi::field('mime_type')),
                    TextInput::make('file_size')
                        ->label(FilamentUi::field('file_size')),
                    TextInput::make('label')
                        ->label(FilamentUi::field('label')),
                    Textarea::make('notes')
                        ->label(FilamentUi::field('notes'))
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
