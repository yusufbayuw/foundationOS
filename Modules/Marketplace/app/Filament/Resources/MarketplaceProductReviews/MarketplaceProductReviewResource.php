<?php

namespace Modules\Marketplace\Filament\Resources\MarketplaceProductReviews;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Marketplace\Filament\Resources\MarketplaceProductReviews\Pages\CreateMarketplaceProductReview;
use Modules\Marketplace\Filament\Resources\MarketplaceProductReviews\Pages\EditMarketplaceProductReview;
use Modules\Marketplace\Filament\Resources\MarketplaceProductReviews\Pages\ListMarketplaceProductReviews;
use Modules\Marketplace\Filament\Resources\MarketplaceProductReviews\Pages\ViewMarketplaceProductReview;
use Modules\Marketplace\Filament\Resources\MarketplaceProductReviews\Schemas\MarketplaceProductReviewForm;
use Modules\Marketplace\Filament\Resources\MarketplaceProductReviews\Schemas\MarketplaceProductReviewInfolist;
use Modules\Marketplace\Filament\Resources\MarketplaceProductReviews\Tables\MarketplaceProductReviewsTable;
use Modules\Marketplace\Models\MarketplaceProductReview;

class MarketplaceProductReviewResource extends ModuleResource
{
    protected static ?string $model = MarketplaceProductReview::class;

    public static function form(Schema $schema): Schema
    {
        return MarketplaceProductReviewForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MarketplaceProductReviewInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MarketplaceProductReviewsTable::configure($table);
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
            'index' => ListMarketplaceProductReviews::route('/'),
            'create' => CreateMarketplaceProductReview::route('/create'),
            'view' => ViewMarketplaceProductReview::route('/{record}'),
            'edit' => EditMarketplaceProductReview::route('/{record}/edit'),
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
