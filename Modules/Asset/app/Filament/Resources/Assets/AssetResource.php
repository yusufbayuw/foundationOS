<?php

namespace Modules\Asset\Filament\Resources\Assets;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Asset\Filament\Resources\Assets\Pages\CreateAsset;
use Modules\Asset\Filament\Resources\Assets\Pages\EditAsset;
use Modules\Asset\Filament\Resources\Assets\Pages\ListAssets;
use Modules\Asset\Filament\Resources\Assets\Pages\ViewAsset;
use Modules\Asset\Filament\Resources\Assets\Schemas\AssetForm;
use Modules\Asset\Filament\Resources\Assets\Tables\AssetsTable;
use Modules\Asset\Models\Asset;
use Modules\Core\Filament\Support\ModuleResource;

class AssetResource extends ModuleResource
{
    protected static ?string $model = Asset::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AssetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssets::route('/'),
            'create' => CreateAsset::route('/create'),
            'view' => ViewAsset::route('/{record}'),
            'edit' => EditAsset::route('/{record}/edit'),
        ];
    }
}
