<?php

namespace Modules\Finance\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Modules\Core\Filament\Support\ImportTableActions;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('studentInvoice.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('studentInvoice.id'))
                    ->searchable(),
                TextColumn::make('chartOfAccount.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('chartOfAccount.name'))
                    ->searchable(),
                TextColumn::make('verified_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('verified_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payment_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_number'))
                    ->searchable(),
                TextColumn::make('payment_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_method'))
                    ->searchable(),
                TextColumn::make('payment_channel')
                    ->label(\Modules\Core\Support\FilamentUi::field('payment_channel'))
                    ->searchable(),
                TextColumn::make('reference_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('reference_number'))
                    ->searchable(),
                TextColumn::make('account_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('account_number'))
                    ->searchable(),
                TextColumn::make('account_holder')
                    ->label(\Modules\Core\Support\FilamentUi::field('account_holder'))
                    ->searchable(),
                TextColumn::make('bank_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_name'))
                    ->searchable(),
                TextColumn::make('proof_file')
                    ->label(\Modules\Core\Support\FilamentUi::field('proof_file'))
                    ->searchable(),
                TextColumn::make('verified_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('verified_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                IconColumn::make('is_reconciled')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_reconciled'))
                    ->boolean(),
                TextColumn::make('reconciliation_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('reconciliation_date'))
                    ->date()
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
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(\App\Filament\Imports\PaymentImporter::class),
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
