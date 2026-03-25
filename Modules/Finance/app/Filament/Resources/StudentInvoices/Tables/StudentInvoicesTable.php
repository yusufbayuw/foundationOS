<?php

namespace Modules\Finance\Filament\Resources\StudentInvoices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentInvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('tuitionType.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tuitionType.name'))
                    ->searchable(),
                TextColumn::make('invoice_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('invoice_number'))
                    ->searchable(),
                TextColumn::make('invoice_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('invoice_type'))
                    ->searchable(),
                TextColumn::make('issue_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('issue_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('due_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('discount_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('discount_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('penalty_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('penalty_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('total_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('paid_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('paid_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('remaining_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('remaining_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                IconColumn::make('is_sent')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_sent'))
                    ->boolean(),
                TextColumn::make('sent_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('sent_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('sent_via')
                    ->label(\Modules\Core\Support\FilamentUi::field('sent_via'))
                    ->searchable(),
                TextColumn::make('invoiceable_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('invoiceable_type'))
                    ->searchable(),
                TextColumn::make('invoiceable_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('invoiceable_id'))
                    ->numeric()
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
