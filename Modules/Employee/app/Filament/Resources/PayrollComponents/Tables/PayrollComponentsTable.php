<?php

namespace Modules\Employee\Filament\Resources\PayrollComponents\Tables;

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

class PayrollComponentsTable
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
                TextColumn::make('code')
                    ->label(\Modules\Core\Support\FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(\Modules\Core\Support\FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(\Modules\Core\Support\FilamentUi::field('type'))
                    ->searchable(),
                TextColumn::make('category')
                    ->label(\Modules\Core\Support\FilamentUi::field('category'))
                    ->searchable(),
                TextColumn::make('calculation_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('calculation_type'))
                    ->searchable(),
                TextColumn::make('amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('percentage')
                    ->label(\Modules\Core\Support\FilamentUi::field('percentage'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_taxable')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_taxable'))
                    ->boolean(),
                IconColumn::make('is_mandatory')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_mandatory'))
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_active'))
                    ->boolean(),
                TextColumn::make('display_order')
                    ->label(\Modules\Core\Support\FilamentUi::field('display_order'))
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
                ...ImportTableActions::make(\App\Filament\Imports\PayrollComponentImporter::class),
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
