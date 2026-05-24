<?php

namespace Modules\KpiEnterprise\Filament\Resources\KpiAreas;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\KpiEnterprise\Filament\Resources\KpiAreas\Pages\CreateKpiArea;
use Modules\KpiEnterprise\Filament\Resources\KpiAreas\Pages\EditKpiArea;
use Modules\KpiEnterprise\Filament\Resources\KpiAreas\Pages\ListKpiAreas;
use Modules\KpiEnterprise\Filament\Resources\KpiAreas\Pages\ViewKpiArea;
use Modules\KpiEnterprise\Filament\Resources\KpiAreas\Schemas\KpiAreaForm;
use Modules\KpiEnterprise\Filament\Resources\KpiAreas\Schemas\KpiAreaInfolist;
use Modules\KpiEnterprise\Filament\Resources\KpiAreas\Tables\KpiAreasTable;
use Modules\KpiEnterprise\Models\KpiArea;

class KpiAreaResource extends ModuleResource
{
    protected static ?string $model = KpiArea::class;

    public static function form(Schema $schema): Schema
    {
        return KpiAreaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KpiAreaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KpiAreasTable::configure($table);
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
            'index' => ListKpiAreas::route('/'),
            'create' => CreateKpiArea::route('/create'),
            'view' => ViewKpiArea::route('/{record}'),
            'edit' => EditKpiArea::route('/{record}/edit'),
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
