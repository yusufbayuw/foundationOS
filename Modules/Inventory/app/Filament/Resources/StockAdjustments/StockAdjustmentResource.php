<?php

namespace Modules\Inventory\Filament\Resources\StockAdjustments;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Inventory\Filament\Resources\StockAdjustments\Pages\CreateStockAdjustment;
use Modules\Inventory\Filament\Resources\StockAdjustments\Pages\EditStockAdjustment;
use Modules\Inventory\Filament\Resources\StockAdjustments\Pages\ListStockAdjustments;
use Modules\Inventory\Filament\Resources\StockAdjustments\Pages\ViewStockAdjustment;
use Modules\Inventory\Filament\Resources\StockAdjustments\RelationManagers\WorkflowInstancesRelationManager;
use Modules\Inventory\Filament\Resources\StockAdjustments\Schemas\StockAdjustmentForm;
use Modules\Inventory\Filament\Resources\StockAdjustments\Schemas\StockAdjustmentInfolist;
use Modules\Inventory\Filament\Resources\StockAdjustments\Tables\StockAdjustmentsTable;
use Modules\Inventory\Models\StockAdjustment;
use Modules\Monitoring\Filament\RelationManagers\AuditLogsRelationManager;

class StockAdjustmentResource extends ModuleResource
{
    protected static ?string $model = StockAdjustment::class;

    protected static ?string $recordTitleAttribute = 'adjustment_number';

    public static function form(Schema $schema): Schema
    {
        return StockAdjustmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StockAdjustmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockAdjustmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            WorkflowInstancesRelationManager::class,
            AuditLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockAdjustments::route('/'),
            'create' => CreateStockAdjustment::route('/create'),
            'view' => ViewStockAdjustment::route('/{record}'),
            'edit' => EditStockAdjustment::route('/{record}/edit'),
        ];
    }
}
