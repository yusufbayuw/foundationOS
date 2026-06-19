<?php

namespace Modules\Employee\Filament\Resources\PayrollComponents\Tables;

use App\Filament\Imports\PayrollComponentImporter;
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

class PayrollComponentsTable
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
                TextColumn::make('code')
                    ->label(FilamentUi::field('code'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(FilamentUi::field('name'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(FilamentUi::field('type'))
                    ->searchable(),
                TextColumn::make('category')
                    ->label(FilamentUi::field('category'))
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
                IconColumn::make('is_taxable')
                    ->label(FilamentUi::field('is_taxable'))
                    ->boolean(),
                IconColumn::make('is_mandatory')
                    ->label(FilamentUi::field('is_mandatory'))
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label(FilamentUi::field('is_active'))
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
                ...ImportTableActions::make(PayrollComponentImporter::class),
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
