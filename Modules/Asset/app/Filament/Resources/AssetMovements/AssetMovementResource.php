<?php

namespace Modules\Asset\Filament\Resources\AssetMovements;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Asset\Filament\Resources\AssetMovements\Pages\CreateAssetMovement;
use Modules\Asset\Filament\Resources\AssetMovements\Pages\EditAssetMovement;
use Modules\Asset\Filament\Resources\AssetMovements\Pages\ListAssetMovements;
use Modules\Asset\Filament\Resources\AssetMovements\Pages\ViewAssetMovement;
use Modules\Asset\Filament\Resources\AssetMovements\Schemas\AssetMovementForm;
use Modules\Asset\Filament\Resources\AssetMovements\Schemas\AssetMovementInfolist;
use Modules\Asset\Filament\Resources\AssetMovements\Tables\AssetMovementsTable;
use Modules\Asset\Models\AssetMovement;
use Modules\Core\Filament\Support\ModuleResource;

class AssetMovementResource extends ModuleResource
{
    protected static ?string $model = AssetMovement::class;

    public static function form(Schema $schema): Schema
    {
        return AssetMovementForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssetMovementInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetMovementsTable::configure($table);
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
            'index' => ListAssetMovements::route('/'),
            'create' => CreateAssetMovement::route('/create'),
            'view' => ViewAssetMovement::route('/{record}'),
            'edit' => EditAssetMovement::route('/{record}/edit'),
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
