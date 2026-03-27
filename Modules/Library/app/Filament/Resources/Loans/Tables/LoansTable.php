<?php

namespace Modules\Library\Filament\Resources\Loans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Modules\Core\Filament\Support\ImportTableActions;

class LoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization.name'))
                    ->searchable(),
                TextColumn::make('bookCopy.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('bookCopy.id'))
                    ->searchable(),
                TextColumn::make('member.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('member.id'))
                    ->searchable(),
                TextColumn::make('processed_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('processed_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('returned_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('returned_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('loan_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('loan_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('due_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('return_date')
                    ->label(\Modules\Core\Support\FilamentUi::field('return_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('extension_count')
                    ->label(\Modules\Core\Support\FilamentUi::field('extension_count'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_extensions')
                    ->label(\Modules\Core\Support\FilamentUi::field('max_extensions'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->searchable(),
                TextColumn::make('fine_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('fine_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('fine_paid')
                    ->label(\Modules\Core\Support\FilamentUi::field('fine_paid'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('fine_status')
                    ->label(\Modules\Core\Support\FilamentUi::field('fine_status'))
                    ->searchable(),
                TextColumn::make('condition_on_loan')
                    ->label(\Modules\Core\Support\FilamentUi::field('condition_on_loan'))
                    ->searchable(),
                TextColumn::make('condition_on_return')
                    ->label(\Modules\Core\Support\FilamentUi::field('condition_on_return'))
                    ->searchable(),
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
                ...ImportTableActions::make(\App\Filament\Imports\LoanImporter::class),
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
