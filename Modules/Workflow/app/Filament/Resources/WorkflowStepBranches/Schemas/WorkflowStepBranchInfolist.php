<?php

namespace Modules\Workflow\Filament\Resources\WorkflowStepBranches\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class WorkflowStepBranchInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(FilamentUi::text('General information'))
                ->schema([
                    TextEntry::make('workflow_instance_id')
                        ->label(FilamentUi::field('workflow_instance_id'))
                        ->placeholder('-'),
                    TextEntry::make('split_step_id')
                        ->label(FilamentUi::field('split_step_id'))
                        ->placeholder('-'),
                    TextEntry::make('branch_step_id')
                        ->label(FilamentUi::field('branch_step_id'))
                        ->placeholder('-'),
                    TextEntry::make('join_step_id')
                        ->label(FilamentUi::field('join_step_id'))
                        ->placeholder('-'),
                    TextEntry::make('status')
                        ->label(FilamentUi::field('status'))
                        ->placeholder('-'),
                    TextEntry::make('outcome')
                        ->label(FilamentUi::field('outcome'))
                        ->placeholder('-'),
                    TextEntry::make('started_at')
                        ->label(FilamentUi::field('started_at'))
                        ->dateTime()
                        ->placeholder('-'),
                    TextEntry::make('completed_at')
                        ->label(FilamentUi::field('completed_at'))
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
