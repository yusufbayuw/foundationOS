<?php

namespace Modules\Employee\Filament\Resources\SalarySlipComponents\Tables;

use App\Filament\Imports\SalarySlipComponentImporter;
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

class SalarySlipComponentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label(FilamentUi::field('tenant.name'))
                    ->searchable(),
                TextColumn::make('salarySlip.id')
                    ->label(FilamentUi::field('salarySlip.id'))
                    ->searchable(),
                TextColumn::make('payrollComponent.name')
                    ->label(FilamentUi::field('payrollComponent.name'))
                    ->searchable(),
                TextColumn::make('component_type')
                    ->label(FilamentUi::field('component_type'))
                    ->searchable(),
                TextColumn::make('component_name')
                    ->label(FilamentUi::field('component_name'))
                    ->searchable(),
                TextColumn::make('calculation_type')
                    ->label(FilamentUi::field('calculation_type'))
                    ->searchable(),
                TextColumn::make('amount')
                    ->label(FilamentUi::field('amount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('percentage')
                    ->label(FilamentUi::field('percentage'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('base_amount')
                    ->label(FilamentUi::field('base_amount'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_taxable')
                    ->label(FilamentUi::field('is_taxable'))
                    ->boolean(),
                IconColumn::make('is_mandatory')
                    ->label(FilamentUi::field('is_mandatory'))
                    ->boolean(),
                TextColumn::make('display_order')
                    ->label(FilamentUi::field('display_order'))
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
                ...ImportTableActions::make(SalarySlipComponentImporter::class),
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
