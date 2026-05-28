<?php

namespace Modules\Exam\Filament\Resources\ExamParticipants\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class ExamParticipantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_name')
                    ->label(FilamentUi::field('display_name'))
                    ->searchable(),
                TextColumn::make('examDefinition.name')
                    ->label(FilamentUi::field('exam_definition_id'))
                    ->searchable(),
                TextColumn::make('participant_code')
                    ->label(FilamentUi::field('participant_code')),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->badge(),
            ])
            ->filters([TrashedFilter::make()])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
