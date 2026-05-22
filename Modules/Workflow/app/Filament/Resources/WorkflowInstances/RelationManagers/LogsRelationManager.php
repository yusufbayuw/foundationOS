<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstances\RelationManagers;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('logged_at')
                    ->label(FilamentUi::text('Logged at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('log_type')
                    ->label(FilamentUi::text('Log type'))
                    ->badge(),
                TextColumn::make('action_taken')
                    ->label(FilamentUi::text('Action taken'))
                    ->badge()
                    ->placeholder('-'),
                TextColumn::make('step.name')
                    ->label(FilamentUi::field('step_id'))
                    ->placeholder('-'),
                TextColumn::make('actor.name')
                    ->label(FilamentUi::text('Actor'))
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('status_before')
                    ->label(FilamentUi::text('Status before'))
                    ->badge()
                    ->placeholder('-'),
                TextColumn::make('status_after')
                    ->label(FilamentUi::text('Status after'))
                    ->badge()
                    ->placeholder('-'),
                TextColumn::make('notes')
                    ->label(FilamentUi::field('notes'))
                    ->limit(60)
                    ->placeholder('-'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->infolist([
                        TextEntry::make('logged_at')->label(FilamentUi::text('Logged at'))->dateTime(),
                        TextEntry::make('log_type')->label(FilamentUi::text('Log type'))->badge(),
                        TextEntry::make('action_taken')->label(FilamentUi::text('Action taken'))->badge()->placeholder('-'),
                        TextEntry::make('step.name')->label(FilamentUi::field('step_id'))->placeholder('-'),
                        TextEntry::make('actor.name')->label(FilamentUi::text('Actor'))->placeholder('-'),
                        TextEntry::make('status_before')->label(FilamentUi::text('Status before'))->badge()->placeholder('-'),
                        TextEntry::make('status_after')->label(FilamentUi::text('Status after'))->badge()->placeholder('-'),
                        TextEntry::make('notes')->label(FilamentUi::field('notes'))->placeholder('-')->columnSpanFull(),
                        KeyValueEntry::make('form_data_snapshot')->label(FilamentUi::text('Form data snapshot'))->columnSpanFull(),
                        KeyValueEntry::make('payload_before')->label(FilamentUi::text('Payload before'))->columnSpanFull(),
                        KeyValueEntry::make('payload_after')->label(FilamentUi::text('Payload after'))->columnSpanFull(),
                    ]),
            ])
            ->defaultSort('logged_at', 'desc');
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema;
    }
}
