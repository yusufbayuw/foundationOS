<?php

namespace Modules\KpiEnterprise\Filament\Resources\KpiWeights;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\KpiEnterprise\Filament\Resources\KpiWeights\Pages\CreateKpiWeight;
use Modules\KpiEnterprise\Filament\Resources\KpiWeights\Pages\EditKpiWeight;
use Modules\KpiEnterprise\Filament\Resources\KpiWeights\Pages\ListKpiWeights;
use Modules\KpiEnterprise\Filament\Resources\KpiWeights\Pages\ViewKpiWeight;
use Modules\KpiEnterprise\Filament\Resources\KpiWeights\Schemas\KpiWeightForm;
use Modules\KpiEnterprise\Filament\Resources\KpiWeights\Schemas\KpiWeightInfolist;
use Modules\KpiEnterprise\Filament\Resources\KpiWeights\Tables\KpiWeightsTable;
use Modules\KpiEnterprise\Models\KpiWeight;

class KpiWeightResource extends ModuleResource
{
    protected static ?string $model = KpiWeight::class;

    public static function form(Schema $schema): Schema
    {
        return KpiWeightForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KpiWeightInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KpiWeightsTable::configure($table);
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
            'index' => ListKpiWeights::route('/'),
            'create' => CreateKpiWeight::route('/create'),
            'view' => ViewKpiWeight::route('/{record}'),
            'edit' => EditKpiWeight::route('/{record}/edit'),
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
