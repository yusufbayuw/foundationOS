<?php

namespace Modules\Workflow\Filament\Resources\WorkflowEvidences\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class WorkflowEvidenceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextEntry::make('workflow_instance_id')
                        ->label(FilamentUi::field('workflow_instance_id'))
                        ->placeholder('-'),
                    TextEntry::make('workflow_step_id')
                        ->label(FilamentUi::field('workflow_step_id'))
                        ->placeholder('-'),
                    TextEntry::make('uploaded_by')
                        ->label(FilamentUi::field('uploaded_by'))
                        ->placeholder('-'),
                    TextEntry::make('file_path')
                        ->label(FilamentUi::field('file_path'))
                        ->placeholder('-'),
                    TextEntry::make('original_filename')
                        ->label(FilamentUi::field('original_filename'))
                        ->placeholder('-'),
                    TextEntry::make('mime_type')
                        ->label(FilamentUi::field('mime_type'))
                        ->placeholder('-'),
                    TextEntry::make('file_size')
                        ->label(FilamentUi::field('file_size'))
                        ->placeholder('-'),
                    TextEntry::make('label')
                        ->label(FilamentUi::field('label'))
                        ->placeholder('-'),
                    TextEntry::make('notes')
                        ->label(FilamentUi::field('notes'))
                        ->placeholder('-'),
                ])
                ->columns(2),
        ]);
    }
}
