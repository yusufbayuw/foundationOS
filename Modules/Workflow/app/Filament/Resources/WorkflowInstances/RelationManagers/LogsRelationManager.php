<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstances\RelationManagers;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('logged_at')
                    ->label('Logged At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('log_type')
                    ->label('Log Type')
                    ->badge(),
                TextColumn::make('action_taken')
                    ->label('Action Taken')
                    ->badge()
                    ->placeholder('-'),
                TextColumn::make('step.name')
                    ->label('Step')
                    ->placeholder('-'),
                TextColumn::make('actor.name')
                    ->label('Actor')
                    ->placeholder('System')
                    ->searchable(),
                TextColumn::make('status_before')
                    ->label('Status Before')
                    ->badge()
                    ->placeholder('-'),
                TextColumn::make('status_after')
                    ->label('Status After')
                    ->badge()
                    ->placeholder('-'),
                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(60)
                    ->placeholder('-'),
            ])
            ->recordActions([
                \Filament\Tables\Actions\ViewAction::make()
                    ->infolist([
                        TextEntry::make('logged_at')->label('Logged At')->dateTime(),
                        TextEntry::make('log_type')->label('Log Type')->badge(),
                        TextEntry::make('action_taken')->label('Action Taken')->badge()->placeholder('-'),
                        TextEntry::make('step.name')->label('Step')->placeholder('-'),
                        TextEntry::make('actor.name')->label('Actor')->placeholder('System'),
                        TextEntry::make('status_before')->label('Status Before')->badge()->placeholder('-'),
                        TextEntry::make('status_after')->label('Status After')->badge()->placeholder('-'),
                        TextEntry::make('notes')->label('Notes')->placeholder('-')->columnSpanFull(),
                        KeyValueEntry::make('form_data_snapshot')->label('Form Data Snapshot')->columnSpanFull(),
                        KeyValueEntry::make('payload_before')->label('Payload Before')->columnSpanFull(),
                        KeyValueEntry::make('payload_after')->label('Payload After')->columnSpanFull(),
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
