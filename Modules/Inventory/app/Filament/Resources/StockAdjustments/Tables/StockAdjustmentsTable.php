<?php

namespace Modules\Inventory\Filament\Resources\StockAdjustments\Tables;

use App\Filament\Imports\StockAdjustmentImporter;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class StockAdjustmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('adjustment_number')->label(FilamentUi::field('adjustment_number'))->searchable(),
                TextColumn::make('warehouse.name')->label(FilamentUi::field('warehouse_id')),
                TextColumn::make('reason')->label(FilamentUi::field('reason')),
                TextColumn::make('status')->label(FilamentUi::field('status')),
                TextColumn::make('total_value_impact')->label(FilamentUi::field('total_value_impact')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(StockAdjustmentImporter::class),
            ]);
    }
}
