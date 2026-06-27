<?php

namespace Modules\Finance\Filament\Resources\StudentInvoices\Tables;

use App\Filament\Imports\StudentInvoiceImporter;
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
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class StudentInvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['tenant', 'tuitionType']))
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('tuitionType.name')
                    ->label(FilamentUi::field('tuitionType.name'))
                    ->searchable(),
                TextColumn::make('invoice_number')
                    ->label(FilamentUi::field('invoice_number'))
                    ->searchable(),
                TextColumn::make('invoice_type')
                    ->label(FilamentUi::field('invoice_type'))
                    ->searchable(),
                TextColumn::make('issue_date')
                    ->label(FilamentUi::field('issue_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label(FilamentUi::field('due_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label(FilamentUi::field('amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('discount_amount')
                    ->label(FilamentUi::field('discount_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('penalty_amount')
                    ->label(FilamentUi::field('penalty_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label(FilamentUi::field('total_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('paid_amount')
                    ->label(FilamentUi::field('paid_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('remaining_amount')
                    ->label(FilamentUi::field('remaining_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                IconColumn::make('is_sent')
                    ->label(FilamentUi::field('is_sent'))
                    ->boolean(),
                TextColumn::make('sent_at')
                    ->label(FilamentUi::field('sent_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('sent_via')
                    ->label(FilamentUi::field('sent_via'))
                    ->searchable(),
                TextColumn::make('invoiceable_type')
                    ->label(FilamentUi::field('invoiceable_type'))
                    ->searchable(),
                TextColumn::make('invoiceable_id')
                    ->label(FilamentUi::field('invoiceable_id'))
                    ->numeric()
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
                        'draft' => 'Draft',
                        'issued' => 'Issued',
                        'partially_paid' => 'Partially Paid',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(StudentInvoiceImporter::class),
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
