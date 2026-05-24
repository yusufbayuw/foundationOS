<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceProductVariants;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Marketplace\Filament\Resources\MarketplaceProductVariants\Pages\CreateMarketplaceProductVariant;
use Modules\Marketplace\Filament\Resources\MarketplaceProductVariants\Pages\EditMarketplaceProductVariant;
use Modules\Marketplace\Filament\Resources\MarketplaceProductVariants\Pages\ListMarketplaceProductVariants;
use Modules\Marketplace\Filament\Resources\MarketplaceProductVariants\Pages\ViewMarketplaceProductVariant;
use Modules\Marketplace\Filament\Resources\MarketplaceProductVariants\Schemas\MarketplaceProductVariantForm;
use Modules\Marketplace\Filament\Resources\MarketplaceProductVariants\Schemas\MarketplaceProductVariantInfolist;
use Modules\Marketplace\Filament\Resources\MarketplaceProductVariants\Tables\MarketplaceProductVariantsTable;
use Modules\Marketplace\Models\MarketplaceProductVariant;

class MarketplaceProductVariantResource extends ModuleResource
{
    protected static ?string $model = MarketplaceProductVariant::class;

    public static function form(Schema $schema): Schema
    {
        return MarketplaceProductVariantForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MarketplaceProductVariantInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MarketplaceProductVariantsTable::configure($table);
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
            'index' => ListMarketplaceProductVariants::route('/'),
            'create' => CreateMarketplaceProductVariant::route('/create'),
            'view' => ViewMarketplaceProductVariant::route('/{record}'),
            'edit' => EditMarketplaceProductVariant::route('/{record}/edit'),
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
