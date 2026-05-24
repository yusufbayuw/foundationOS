<?php

namespace Modules\KpiEnterprise\Filament\Resources\KpiActuals;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\KpiEnterprise\Filament\Resources\KpiActuals\Pages\CreateKpiActual;
use Modules\KpiEnterprise\Filament\Resources\KpiActuals\Pages\EditKpiActual;
use Modules\KpiEnterprise\Filament\Resources\KpiActuals\Pages\ListKpiActuals;
use Modules\KpiEnterprise\Filament\Resources\KpiActuals\Pages\ViewKpiActual;
use Modules\KpiEnterprise\Filament\Resources\KpiActuals\Schemas\KpiActualForm;
use Modules\KpiEnterprise\Filament\Resources\KpiActuals\Schemas\KpiActualInfolist;
use Modules\KpiEnterprise\Filament\Resources\KpiActuals\Tables\KpiActualsTable;
use Modules\KpiEnterprise\Models\KpiActual;

class KpiActualResource extends ModuleResource
{
    protected static ?string $model = KpiActual::class;

    public static function form(Schema $schema): Schema
    {
        return KpiActualForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KpiActualInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KpiActualsTable::configure($table);
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
            'index' => ListKpiActuals::route('/'),
            'create' => CreateKpiActual::route('/create'),
            'view' => ViewKpiActual::route('/{record}'),
            'edit' => EditKpiActual::route('/{record}/edit'),
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
