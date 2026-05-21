<?php

namespace Modules\Library\Filament\Resources\Loans\Tables;

use App\Filament\Imports\LoanImporter;
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

class LoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('organization.name')
                    ->label(FilamentUi::field('organization.name'))
                    ->searchable(),
                TextColumn::make('bookCopy.id')
                    ->label(FilamentUi::field('bookCopy.id'))
                    ->searchable(),
                TextColumn::make('member.id')
                    ->label(FilamentUi::field('member.id'))
                    ->searchable(),
                TextColumn::make('processed_by')
                    ->label(FilamentUi::field('processed_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('returned_by')
                    ->label(FilamentUi::field('returned_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('loan_date')
                    ->label(FilamentUi::field('loan_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label(FilamentUi::field('due_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('return_date')
                    ->label(FilamentUi::field('return_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('extension_count')
                    ->label(FilamentUi::field('extension_count'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_extensions')
                    ->label(FilamentUi::field('max_extensions'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('fine_amount')
                    ->label(FilamentUi::field('fine_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('fine_paid')
                    ->label(FilamentUi::field('fine_paid'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('fine_status')
                    ->label(FilamentUi::field('fine_status'))
                    ->searchable(),
                TextColumn::make('condition_on_loan')
                    ->label(FilamentUi::field('condition_on_loan'))
                    ->searchable(),
                TextColumn::make('condition_on_return')
                    ->label(FilamentUi::field('condition_on_return'))
                    ->searchable(),
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
                        'borrowed' => 'Borrowed',
                        'overdue' => 'Overdue',
                        'returned' => 'Returned',
                        'lost' => 'Lost',
                        'damaged' => 'Damaged',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(LoanImporter::class),
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
