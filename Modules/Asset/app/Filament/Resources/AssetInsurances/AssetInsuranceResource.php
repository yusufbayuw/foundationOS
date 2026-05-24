<?php

namespace Modules\Asset\Filament\Resources\AssetInsurances;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Asset\Filament\Resources\AssetInsurances\Pages\CreateAssetInsurance;
use Modules\Asset\Filament\Resources\AssetInsurances\Pages\EditAssetInsurance;
use Modules\Asset\Filament\Resources\AssetInsurances\Pages\ListAssetInsurances;
use Modules\Asset\Filament\Resources\AssetInsurances\Pages\ViewAssetInsurance;
use Modules\Asset\Filament\Resources\AssetInsurances\Schemas\AssetInsuranceForm;
use Modules\Asset\Filament\Resources\AssetInsurances\Schemas\AssetInsuranceInfolist;
use Modules\Asset\Filament\Resources\AssetInsurances\Tables\AssetInsurancesTable;
use Modules\Asset\Models\AssetInsurance;
use Modules\Core\Filament\Support\ModuleResource;

class AssetInsuranceResource extends ModuleResource
{
    protected static ?string $model = AssetInsurance::class;

    public static function form(Schema $schema): Schema
    {
        return AssetInsuranceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssetInsuranceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetInsurancesTable::configure($table);
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
            'index' => ListAssetInsurances::route('/'),
            'create' => CreateAssetInsurance::route('/create'),
            'view' => ViewAssetInsurance::route('/{record}'),
            'edit' => EditAssetInsurance::route('/{record}/edit'),
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
