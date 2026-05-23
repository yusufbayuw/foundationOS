<?php

namespace Modules\Inventory\Filament\Resources\StockItems\Tables;

use App\Filament\Imports\StockItemImporter;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class StockItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label(FilamentUi::field('code'))->searchable(),
                TextColumn::make('name')->label(FilamentUi::field('name'))->searchable(),
                TextColumn::make('valuation_method')->label(FilamentUi::field('valuation_method')),
                IconColumn::make('is_active')->label(FilamentUi::field('is_active'))->boolean(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(StockItemImporter::class),
            ]);
    }
}
