<?php

namespace Modules\Inventory\Filament\Resources\StockMoves;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Inventory\Filament\Resources\StockMoves\Pages\ListStockMoves;
use Modules\Inventory\Filament\Resources\StockMoves\Pages\ViewStockMove;
use Modules\Inventory\Filament\Resources\StockMoves\Schemas\StockMoveInfolist;
use Modules\Inventory\Filament\Resources\StockMoves\Tables\StockMovesTable;
use Modules\Inventory\Models\StockMove;
use Modules\Monitoring\Filament\RelationManagers\AuditLogsRelationManager;

class StockMoveResource extends ModuleResource
{
    protected static ?string $model = StockMove::class;

    protected static ?string $recordTitleAttribute = 'move_number';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function infolist(Schema $schema): Schema
    {
        return StockMoveInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockMovesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AuditLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockMoves::route('/'),
            'view' => ViewStockMove::route('/{record}'),
        ];
    }
}
