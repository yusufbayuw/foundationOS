<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstanceLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class WorkflowInstanceLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextEntry::make('workflow_instance_id')
                        ->label(FilamentUi::field('workflow_instance_id'))
                        ->placeholder('-'),
                    TextEntry::make('step_id')
                        ->label(FilamentUi::field('step_id'))
                        ->placeholder('-'),
                    TextEntry::make('transition_id')
                        ->label(FilamentUi::field('transition_id'))
                        ->placeholder('-'),
                    TextEntry::make('actor_id')
                        ->label(FilamentUi::field('actor_id'))
                        ->placeholder('-'),
                    TextEntry::make('log_type')
                        ->label(FilamentUi::field('log_type'))
                        ->placeholder('-'),
                    TextEntry::make('action_taken')
                        ->label(FilamentUi::field('action_taken'))
                        ->placeholder('-'),
                    TextEntry::make('status_before')
                        ->label(FilamentUi::field('status_before'))
                        ->placeholder('-'),
                    TextEntry::make('status_after')
                        ->label(FilamentUi::field('status_after'))
                        ->placeholder('-'),
                    TextEntry::make('payload_before')
                        ->label(FilamentUi::field('payload_before'))
                        ->placeholder('-'),
                    TextEntry::make('payload_after')
                        ->label(FilamentUi::field('payload_after'))
                        ->placeholder('-'),
                    TextEntry::make('form_data_snapshot')
                        ->label(FilamentUi::field('form_data_snapshot'))
                        ->placeholder('-'),
                    TextEntry::make('notes')
                        ->label(FilamentUi::field('notes'))
                        ->placeholder('-'),
                ])
                ->columns(2),
        ]);
    }
}
