<?php

namespace Modules\Asset\Filament\Resources\AssetDepreciations;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Asset\Filament\Resources\AssetDepreciations\Pages\CreateAssetDepreciation;
use Modules\Asset\Filament\Resources\AssetDepreciations\Pages\EditAssetDepreciation;
use Modules\Asset\Filament\Resources\AssetDepreciations\Pages\ListAssetDepreciations;
use Modules\Asset\Filament\Resources\AssetDepreciations\Pages\ViewAssetDepreciation;
use Modules\Asset\Filament\Resources\AssetDepreciations\Schemas\AssetDepreciationForm;
use Modules\Asset\Filament\Resources\AssetDepreciations\Schemas\AssetDepreciationInfolist;
use Modules\Asset\Filament\Resources\AssetDepreciations\Tables\AssetDepreciationsTable;
use Modules\Asset\Models\AssetDepreciation;
use Modules\Core\Filament\Support\ModuleResource;

class AssetDepreciationResource extends ModuleResource
{
    protected static ?string $model = AssetDepreciation::class;

    public static function form(Schema $schema): Schema
    {
        return AssetDepreciationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssetDepreciationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetDepreciationsTable::configure($table);
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
            'index' => ListAssetDepreciations::route('/'),
            'create' => CreateAssetDepreciation::route('/create'),
            'view' => ViewAssetDepreciation::route('/{record}'),
            'edit' => EditAssetDepreciation::route('/{record}/edit'),
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
