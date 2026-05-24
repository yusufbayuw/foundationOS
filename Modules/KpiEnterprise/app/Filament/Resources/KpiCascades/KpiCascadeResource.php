<?php

namespace Modules\KpiEnterprise\Filament\Resources\KpiCascades;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\KpiEnterprise\Filament\Resources\KpiCascades\Pages\CreateKpiCascade;
use Modules\KpiEnterprise\Filament\Resources\KpiCascades\Pages\EditKpiCascade;
use Modules\KpiEnterprise\Filament\Resources\KpiCascades\Pages\ListKpiCascades;
use Modules\KpiEnterprise\Filament\Resources\KpiCascades\Pages\ViewKpiCascade;
use Modules\KpiEnterprise\Filament\Resources\KpiCascades\Schemas\KpiCascadeForm;
use Modules\KpiEnterprise\Filament\Resources\KpiCascades\Schemas\KpiCascadeInfolist;
use Modules\KpiEnterprise\Filament\Resources\KpiCascades\Tables\KpiCascadesTable;
use Modules\KpiEnterprise\Models\KpiCascade;

class KpiCascadeResource extends ModuleResource
{
    protected static ?string $model = KpiCascade::class;

    public static function form(Schema $schema): Schema
    {
        return KpiCascadeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KpiCascadeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KpiCascadesTable::configure($table);
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
            'index' => ListKpiCascades::route('/'),
            'create' => CreateKpiCascade::route('/create'),
            'view' => ViewKpiCascade::route('/{record}'),
            'edit' => EditKpiCascade::route('/{record}/edit'),
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
