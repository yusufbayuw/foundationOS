<?php

namespace Modules\Asset\Filament\Resources\AssetMaintenances;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Asset\Filament\Resources\AssetMaintenances\Pages\CreateAssetMaintenance;
use Modules\Asset\Filament\Resources\AssetMaintenances\Pages\EditAssetMaintenance;
use Modules\Asset\Filament\Resources\AssetMaintenances\Pages\ListAssetMaintenances;
use Modules\Asset\Filament\Resources\AssetMaintenances\Pages\ViewAssetMaintenance;
use Modules\Asset\Filament\Resources\AssetMaintenances\Schemas\AssetMaintenanceForm;
use Modules\Asset\Filament\Resources\AssetMaintenances\Schemas\AssetMaintenanceInfolist;
use Modules\Asset\Filament\Resources\AssetMaintenances\Tables\AssetMaintenancesTable;
use Modules\Asset\Models\AssetMaintenance;
use Modules\Core\Filament\Support\ModuleResource;

class AssetMaintenanceResource extends ModuleResource
{
    protected static ?string $model = AssetMaintenance::class;

    public static function form(Schema $schema): Schema
    {
        return AssetMaintenanceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssetMaintenanceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetMaintenancesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssetMaintenances::route('/'),
            'create' => CreateAssetMaintenance::route('/create'),
            'view' => ViewAssetMaintenance::route('/{record}'),
            'edit' => EditAssetMaintenance::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
