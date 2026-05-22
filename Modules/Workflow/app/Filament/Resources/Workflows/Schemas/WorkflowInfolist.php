<?php

namespace Modules\Workflow\Filament\Resources\Workflows\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;
use Modules\Workflow\Models\Workflow;

class WorkflowInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('name')->label(FilamentUi::field('name')),
            TextEntry::make('code')->label(FilamentUi::field('code')),
            TextEntry::make('tenant.name')->label(FilamentUi::field('tenant_id')),
            TextEntry::make('organization.name')->label(FilamentUi::field('organization_id'))->placeholder('-'),
            TextEntry::make('module')->label(FilamentUi::field('module'))->placeholder('-'),
            TextEntry::make('subject_type')->label(FilamentUi::field('subject_type'))->placeholder('-')->columnSpanFull(),
            TextEntry::make('trigger_mode')->label(FilamentUi::field('trigger_mode'))->badge(),
            TextEntry::make('version')->label(FilamentUi::field('version')),
            TextEntry::make('status')->label(FilamentUi::field('status'))->badge(),
            TextEntry::make('is_active')
                ->label(FilamentUi::field('is_active'))
                ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                ->badge()
                ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
            TextEntry::make('published_at')->label(FilamentUi::field('published_at'))->dateTime()->placeholder('-'),
            TextEntry::make('creator.name')->label(FilamentUi::text('Created by'))->placeholder('-'),
            TextEntry::make('updater.name')->label(FilamentUi::text('Updated by'))->placeholder('-'),
            TextEntry::make('description')->label(FilamentUi::field('description'))->placeholder('-')->columnSpanFull(),
            KeyValueEntry::make('steps_summary')
                ->label(FilamentUi::text('Workflow snapshot'))
                ->state(fn (Workflow $record): array => [
                    'steps_count' => (string) $record->steps()->count(),
                    'transitions_count' => (string) $record->transitions()->count(),
                    'automated_actions_count' => (string) $record->automatedActions()->count(),
                    'active_instances_count' => (string) $record->instances()->where('status', 'running')->count(),
                ])
                ->columnSpanFull(),
        ]);
    }
}
