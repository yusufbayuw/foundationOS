<?php

namespace Modules\Workflow\Filament\Resources\WorkflowAutomatedActions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class WorkflowAutomatedActionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextEntry::make('workflow_id')
                        ->label(FilamentUi::field('workflow_id'))
                        ->placeholder('-'),
                    TextEntry::make('step_id')
                        ->label(FilamentUi::field('step_id'))
                        ->placeholder('-'),
                    TextEntry::make('trigger_event')
                        ->label(FilamentUi::field('trigger_event'))
                        ->placeholder('-'),
                    TextEntry::make('action_type')
                        ->label(FilamentUi::field('action_type'))
                        ->placeholder('-'),
                    TextEntry::make('name')
                        ->label(FilamentUi::field('name'))
                        ->placeholder('-'),
                    TextEntry::make('config')
                        ->label(FilamentUi::field('config'))
                        ->placeholder('-'),
                    TextEntry::make('is_active')
                        ->label(FilamentUi::field('is_active'))
                        ->placeholder('-'),
                    TextEntry::make('sort_order')
                        ->label(FilamentUi::field('sort_order'))
                        ->placeholder('-'),
                ])
                ->columns(2),
        ]);
    }
}
