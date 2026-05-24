<?php

namespace Modules\IsoCompliance\Filament\Resources\InformationAssets;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\IsoCompliance\Filament\Resources\InformationAssets\Pages\CreateInformationAsset;
use Modules\IsoCompliance\Filament\Resources\InformationAssets\Pages\EditInformationAsset;
use Modules\IsoCompliance\Filament\Resources\InformationAssets\Pages\ListInformationAssets;
use Modules\IsoCompliance\Filament\Resources\InformationAssets\Pages\ViewInformationAsset;
use Modules\IsoCompliance\Filament\Resources\InformationAssets\Schemas\InformationAssetForm;
use Modules\IsoCompliance\Filament\Resources\InformationAssets\Schemas\InformationAssetInfolist;
use Modules\IsoCompliance\Filament\Resources\InformationAssets\Tables\InformationAssetsTable;
use Modules\IsoCompliance\Models\InformationAsset;

class InformationAssetResource extends ModuleResource
{
    protected static ?string $model = InformationAsset::class;

    public static function form(Schema $schema): Schema
    {
        return InformationAssetForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InformationAssetInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InformationAssetsTable::configure($table);
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
            'index' => ListInformationAssets::route('/'),
            'create' => CreateInformationAsset::route('/create'),
            'view' => ViewInformationAsset::route('/{record}'),
            'edit' => EditInformationAsset::route('/{record}/edit'),
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
