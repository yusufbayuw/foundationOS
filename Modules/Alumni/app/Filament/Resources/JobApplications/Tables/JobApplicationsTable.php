<?php

namespace Modules\Alumni\Filament\Resources\JobApplications\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class JobApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('jobPosting.name')
                    ->label(FilamentUi::text('Job posting'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(FilamentUi::text('Applicant'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label(FilamentUi::text('Email'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(FilamentUi::text('Submitted at'))
                    ->dateTime()
                    ->sortable()
                    ->since(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'submitted' => 'Submitted',
                        'in_review' => 'In review',
                        'shortlisted' => 'Shortlisted',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
