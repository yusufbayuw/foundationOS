<?php

namespace Modules\Training\Filament\Resources\TrainingCertificates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;

class TrainingCertificatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant_id')
                    ->label(FilamentUi::field('tenant_id'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('training_enrollment_id')
                    ->label(FilamentUi::field('training_enrollment_id'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('certificate_number')
                    ->label(FilamentUi::field('certificate_number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('verification_token')
                    ->label(FilamentUi::field('verification_token'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('issued_at')
                    ->label(FilamentUi::field('issued_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
