<?php

namespace Modules\Finance\Filament\Resources\ChartOfAccounts\Tables;

use App\Filament\Imports\ChartOfAccountImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class ChartOfAccountsTable
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
                TextColumn::make('parent.name')
                    ->label(FilamentUi::field('parent.name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('level')
                    ->label(FilamentUi::field('level'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(FilamentUi::field('type'))
                    ->searchable(),
                TextColumn::make('category')
                    ->label(FilamentUi::field('category'))
                    ->searchable(),
                TextColumn::make('normal_balance')
                    ->label(FilamentUi::field('normal_balance'))
                    ->searchable(),
                IconColumn::make('is_bank_account')
                    ->label(FilamentUi::field('is_bank_account'))
                    ->boolean(),
                TextColumn::make('bank_name')
                    ->label(FilamentUi::field('bank_name'))
                    ->searchable(),
                TextColumn::make('bank_account_number')
                    ->label(FilamentUi::field('bank_account_number'))
                    ->searchable(),
                TextColumn::make('bank_account_holder')
                    ->label(FilamentUi::field('bank_account_holder'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(FilamentUi::field('is_active'))
                    ->boolean(),
                IconColumn::make('is_locked')
                    ->label(FilamentUi::field('is_locked'))
                    ->boolean(),
                TextColumn::make('opening_balance')
                    ->label(FilamentUi::field('opening_balance'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('current_balance')
                    ->label(FilamentUi::field('current_balance'))
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
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(ChartOfAccountImporter::class),
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
