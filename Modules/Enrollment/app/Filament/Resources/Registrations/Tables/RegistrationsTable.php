<?php

namespace Modules\Enrollment\Filament\Resources\Registrations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('applicant.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('applicant.id'))
                    ->searchable(),
                TextColumn::make('completed_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('completed_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('registration_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('registration_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('payment_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_status'))
                    ->searchable(),
                TextColumn::make('total_fee')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_fee'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('paid_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('paid_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('uniform_size')
                    ->label(\Modules\Core\Support\FilamentUi::field('uniform_size'))
                    ->searchable(),
                TextColumn::make('completed_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('completed_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
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
