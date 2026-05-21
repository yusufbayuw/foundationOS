<?php

namespace Modules\Finance\Filament\Resources\Payments\Tables;

use App\Filament\Imports\PaymentImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('studentInvoice.id')
                    ->label(FilamentUi::field('studentInvoice.id'))
                    ->searchable(),
                TextColumn::make('chartOfAccount.name')
                    ->label(FilamentUi::field('chartOfAccount.name'))
                    ->searchable(),
                TextColumn::make('verified_by')
                    ->label(FilamentUi::field('verified_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payment_number')
                    ->label(FilamentUi::field('payment_number'))
                    ->searchable(),
                TextColumn::make('payment_date')
                    ->label(FilamentUi::field('payment_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label(FilamentUi::field('amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label(FilamentUi::field('payment_method'))
                    ->searchable(),
                TextColumn::make('payment_channel')
                    ->label(FilamentUi::field('payment_channel'))
                    ->searchable(),
                TextColumn::make('reference_number')
                    ->label(FilamentUi::field('reference_number'))
                    ->searchable(),
                TextColumn::make('account_number')
                    ->label(FilamentUi::field('account_number'))
                    ->searchable(),
                TextColumn::make('account_holder')
                    ->label(FilamentUi::field('account_holder'))
                    ->searchable(),
                TextColumn::make('bank_name')
                    ->label(FilamentUi::field('bank_name'))
                    ->searchable(),
                TextColumn::make('proof_file')
                    ->label(FilamentUi::field('proof_file'))
                    ->searchable(),
                TextColumn::make('verified_at')
                    ->label(FilamentUi::field('verified_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                IconColumn::make('is_reconciled')
                    ->label(FilamentUi::field('is_reconciled'))
                    ->boolean(),
                TextColumn::make('reconciliation_date')
                    ->label(FilamentUi::field('reconciliation_date'))
                    ->date()
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
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(PaymentImporter::class),
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
