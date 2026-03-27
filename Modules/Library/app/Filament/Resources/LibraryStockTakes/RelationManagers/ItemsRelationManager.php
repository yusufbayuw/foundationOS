<?php

namespace Modules\Library\Filament\Resources\LibraryStockTakes\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Library\Filament\Resources\LibraryStockTakeItems\LibraryStockTakeItemResource;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return LibraryStockTakeItemResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return LibraryStockTakeItemResource::table($table);
    }
}
