<?php

namespace Modules\Finance\Filament\Resources\CustomerInvoices\Tables;

use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Core\Support\FilamentUi;
use Modules\Finance\Models\CustomerInvoice;

class CustomerInvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label(FilamentUi::field('invoice_number'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('customer_name')
                    ->label(FilamentUi::field('customer_name'))
                    ->searchable(),
                TextColumn::make('invoice_type')
                    ->label(FilamentUi::field('invoice_type'))
                    ->badge(),
                TextColumn::make('issue_date')
                    ->label(FilamentUi::field('issue_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label(FilamentUi::field('due_date'))
                    ->date()
                    ->sortable()
                    ->color(fn (CustomerInvoice $record): string => $record->isOverdue() ? 'danger' : 'default'),
                TextColumn::make('total_amount')
                    ->label(FilamentUi::field('total_amount'))
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('remaining_amount')
                    ->label(FilamentUi::field('remaining_amount'))
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'issued', 'partial' => 'warning',
                        'void', 'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(FilamentUi::field('status'))
                    ->options(CustomerInvoice::statusOptions()),
                SelectFilter::make('invoice_type')
                    ->label(FilamentUi::field('invoice_type'))
                    ->options(CustomerInvoice::invoiceTypeOptions()),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('issue_date', 'desc');
    }
}
