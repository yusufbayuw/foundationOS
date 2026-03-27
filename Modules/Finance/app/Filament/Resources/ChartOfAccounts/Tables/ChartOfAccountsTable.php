<?php

namespace Modules\Finance\Filament\Resources\ChartOfAccounts\Tables;

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

class ChartOfAccountsTable
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
                TextColumn::make('parent.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('parent.name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('level')
                    ->label(\Modules\Core\Support\FilamentUi::field('level'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type'))
                    ->searchable(),
                TextColumn::make('category')
                    ->label(\Modules\Core\Support\FilamentUi::field('category'))
                    ->searchable(),
                TextColumn::make('normal_balance')
                    ->label(\Modules\Core\Support\FilamentUi::field('normal_balance'))
                    ->searchable(),
                IconColumn::make('is_bank_account')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_bank_account'))
                    ->boolean(),
                TextColumn::make('bank_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_name'))
                    ->searchable(),
                TextColumn::make('bank_account_number')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_account_number'))
                    ->searchable(),
                TextColumn::make('bank_account_holder')
                    ->label(\Modules\Core\Support\FilamentUi::field('bank_account_holder'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->boolean(),
                IconColumn::make('is_locked')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_locked'))
                    ->boolean(),
                TextColumn::make('opening_balance')
                    ->label(\Modules\Core\Support\FilamentUi::field('opening_balance'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('current_balance')
                    ->label(\Modules\Core\Support\FilamentUi::field('current_balance'))
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
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(\App\Filament\Imports\ChartOfAccountImporter::class),
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
