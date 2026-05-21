<?php

namespace Modules\Enrollment\Filament\Resources\Registrations\Tables;

use App\Filament\Imports\RegistrationImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class RegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('applicant.id')
                    ->label(FilamentUi::field('applicant.id'))
                    ->searchable(),
                TextColumn::make('completed_by')
                    ->label(FilamentUi::field('completed_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('registration_date')
                    ->label(FilamentUi::field('registration_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('payment_status')
                    ->label(FilamentUi::field('payment_status'))
                    ->searchable(),
                TextColumn::make('total_fee')
                    ->label(FilamentUi::field('total_fee'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('paid_amount')
                    ->label(FilamentUi::field('paid_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('uniform_size')
                    ->label(FilamentUi::field('uniform_size'))
                    ->searchable(),
                TextColumn::make('completed_at')
                    ->label(FilamentUi::field('completed_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(FilamentUi::field('created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('payment_status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'partial' => 'Partial',
                        'paid' => 'Paid',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(RegistrationImporter::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
