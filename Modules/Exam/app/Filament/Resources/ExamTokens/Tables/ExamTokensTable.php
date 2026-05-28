<?php

namespace Modules\Exam\Filament\Resources\ExamTokens\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class ExamTokensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('token')
                    ->label(FilamentUi::field('token'))
                    ->searchable(),
                TextColumn::make('examParticipant.display_name')
                    ->label(FilamentUi::field('exam_participant_id'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(FilamentUi::field('is_active'))
                    ->boolean(),
                TextColumn::make('expires_at')
                    ->label(FilamentUi::field('expires_at'))
                    ->dateTime(),
            ])
            ->filters([TrashedFilter::make()])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
