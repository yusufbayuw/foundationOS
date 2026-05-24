<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceProductCategories;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Marketplace\Filament\Resources\MarketplaceProductCategories\Pages\CreateMarketplaceProductCategory;
use Modules\Marketplace\Filament\Resources\MarketplaceProductCategories\Pages\EditMarketplaceProductCategory;
use Modules\Marketplace\Filament\Resources\MarketplaceProductCategories\Pages\ListMarketplaceProductCategories;
use Modules\Marketplace\Filament\Resources\MarketplaceProductCategories\Pages\ViewMarketplaceProductCategory;
use Modules\Marketplace\Filament\Resources\MarketplaceProductCategories\Schemas\MarketplaceProductCategoryForm;
use Modules\Marketplace\Filament\Resources\MarketplaceProductCategories\Schemas\MarketplaceProductCategoryInfolist;
use Modules\Marketplace\Filament\Resources\MarketplaceProductCategories\Tables\MarketplaceProductCategoriesTable;
use Modules\Marketplace\Models\MarketplaceProductCategory;

class MarketplaceProductCategoryResource extends ModuleResource
{
    protected static ?string $model = MarketplaceProductCategory::class;

    public static function form(Schema $schema): Schema
    {
        return MarketplaceProductCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MarketplaceProductCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MarketplaceProductCategoriesTable::configure($table);
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
            'index' => ListMarketplaceProductCategories::route('/'),
            'create' => CreateMarketplaceProductCategory::route('/create'),
            'view' => ViewMarketplaceProductCategory::route('/{record}'),
            'edit' => EditMarketplaceProductCategory::route('/{record}/edit'),
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
