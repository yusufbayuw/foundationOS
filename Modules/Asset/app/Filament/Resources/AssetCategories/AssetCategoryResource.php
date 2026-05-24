<?php

namespace Modules\Asset\Filament\Resources\AssetCategories;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Asset\Filament\Resources\AssetCategories\Pages\CreateAssetCategory;
use Modules\Asset\Filament\Resources\AssetCategories\Pages\EditAssetCategory;
use Modules\Asset\Filament\Resources\AssetCategories\Pages\ListAssetCategories;
use Modules\Asset\Filament\Resources\AssetCategories\Pages\ViewAssetCategory;
use Modules\Asset\Filament\Resources\AssetCategories\Schemas\AssetCategoryForm;
use Modules\Asset\Filament\Resources\AssetCategories\Schemas\AssetCategoryInfolist;
use Modules\Asset\Filament\Resources\AssetCategories\Tables\AssetCategoriesTable;
use Modules\Asset\Models\AssetCategory;
use Modules\Core\Filament\Support\ModuleResource;

class AssetCategoryResource extends ModuleResource
{
    protected static ?string $model = AssetCategory::class;

    public static function form(Schema $schema): Schema
    {
        return AssetCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssetCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetCategoriesTable::configure($table);
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
            'index' => ListAssetCategories::route('/'),
            'create' => CreateAssetCategory::route('/create'),
            'view' => ViewAssetCategory::route('/{record}'),
            'edit' => EditAssetCategory::route('/{record}/edit'),
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
