<?php

namespace Modules\Inventory\Filament\Resources\Warehouses\Tables;

use App\Filament\Imports\WarehouseImporter;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ImportTableActions;
use Modules\Core\Support\FilamentUi;

class WarehousesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label(FilamentUi::field('code'))->searchable(),
                TextColumn::make('name')->label(FilamentUi::field('name'))->searchable(),
                TextColumn::make('organization.name')->label(FilamentUi::field('organization_id')),
                IconColumn::make('is_default')->label(FilamentUi::field('is_default'))->boolean(),
                IconColumn::make('is_active')->label(FilamentUi::field('is_active'))->boolean(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->headerActions([
                ...ImportTableActions::make(WarehouseImporter::class),
            ]);
    }
}
