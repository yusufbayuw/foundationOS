<?php

namespace Modules\Employee\Filament\Resources\SalarySlipComponents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SalarySlipComponentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('salarySlip.id')
                    ->label(\Modules\Core\Support\FilamentUi::field('salarySlip.id'))
                    ->searchable(),
                TextColumn::make('payrollComponent.name')
                    ->label(\Modules\Core\Support\FilamentUi::field('payrollComponent.name'))
                    ->searchable(),
                TextColumn::make('component_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('component_type'))
                    ->searchable(),
                TextColumn::make('component_name')
                    ->label(\Modules\Core\Support\FilamentUi::field('component_name'))
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
                TextColumn::make('base_amount')
                    ->label(\Modules\Core\Support\FilamentUi::field('base_amount'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_taxable')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_taxable'))
                    ->boolean(),
                IconColumn::make('is_mandatory')
                    ->label(\Modules\Core\Support\FilamentUi::field('is_mandatory'))
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
