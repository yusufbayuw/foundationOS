<?php

namespace Modules\Workflow\Filament\Resources\WorkflowAssignments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class WorkflowAssignmentInfolist
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
                    TextEntry::make('assigned_to_type')
                        ->label(FilamentUi::field('assigned_to_type'))
                        ->placeholder('-'),
                    TextEntry::make('assigned_to_id')
                        ->label(FilamentUi::field('assigned_to_id'))
                        ->placeholder('-'),
                    TextEntry::make('assignment_role')
                        ->label(FilamentUi::field('assignment_role'))
                        ->placeholder('-'),
                    TextEntry::make('status')
                        ->label(FilamentUi::field('status'))
                        ->placeholder('-'),
                    TextEntry::make('outcome')
                        ->label(FilamentUi::field('outcome'))
                        ->placeholder('-'),
                    TextEntry::make('assigned_at')
                        ->label(FilamentUi::field('assigned_at'))
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('claimed_at')
                        ->label(FilamentUi::field('claimed_at'))
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('completed_at')
                        ->label(FilamentUi::field('completed_at'))
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('due_at')
                        ->label(FilamentUi::field('due_at'))
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('meta')
                        ->label(FilamentUi::field('meta'))
                        ->placeholder('-'),
                ])
                ->columns(2),
        ]);
    }
}
