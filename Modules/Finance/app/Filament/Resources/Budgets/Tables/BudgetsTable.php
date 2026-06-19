<?php

namespace Modules\Finance\Filament\Resources\Budgets\Tables;

use App\Filament\Imports\BudgetImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class BudgetsTable
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
                TextColumn::make('chartOfAccount.name')
                    ->label(FilamentUi::field('chartOfAccount.name'))
                    ->searchable(),
                TextColumn::make('approved_by')
                    ->label(FilamentUi::field('approved_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('fiscal_year')
                    ->label(FilamentUi::field('fiscal_year'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('allocated_amount')
                    ->label(FilamentUi::field('allocated_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('used_amount')
                    ->label(FilamentUi::field('used_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('remaining_amount')
                    ->label(FilamentUi::field('remaining_amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(FilamentUi::field('status'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('approved_at')
                    ->label(FilamentUi::field('approved_at'))
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
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(BudgetImporter::class),
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
