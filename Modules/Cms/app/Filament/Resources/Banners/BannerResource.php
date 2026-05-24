<?php

namespace Modules\Cms\Filament\Resources\Banners;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Cms\Filament\Resources\Banners\Pages\CreateBanner;
use Modules\Cms\Filament\Resources\Banners\Pages\EditBanner;
use Modules\Cms\Filament\Resources\Banners\Pages\ListBanners;
use Modules\Cms\Filament\Resources\Banners\Pages\ViewBanner;
use Modules\Cms\Filament\Resources\Banners\Schemas\BannerForm;
use Modules\Cms\Filament\Resources\Banners\Schemas\BannerInfolist;
use Modules\Cms\Filament\Resources\Banners\Tables\BannersTable;
use Modules\Cms\Models\Banner;
use Modules\Core\Filament\Support\ModuleResource;

class BannerResource extends ModuleResource
{
    protected static ?string $model = Banner::class;

    public static function form(Schema $schema): Schema
    {
        return BannerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BannerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BannersTable::configure($table);
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
            'index' => ListBanners::route('/'),
            'create' => CreateBanner::route('/create'),
            'view' => ViewBanner::route('/{record}'),
            'edit' => EditBanner::route('/{record}/edit'),
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
