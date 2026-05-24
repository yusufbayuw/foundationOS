<?php

namespace Modules\KpiEnterprise\Filament\Resources\KpiTargets;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\KpiEnterprise\Filament\Resources\KpiTargets\Pages\CreateKpiTarget;
use Modules\KpiEnterprise\Filament\Resources\KpiTargets\Pages\EditKpiTarget;
use Modules\KpiEnterprise\Filament\Resources\KpiTargets\Pages\ListKpiTargets;
use Modules\KpiEnterprise\Filament\Resources\KpiTargets\Pages\ViewKpiTarget;
use Modules\KpiEnterprise\Filament\Resources\KpiTargets\Schemas\KpiTargetForm;
use Modules\KpiEnterprise\Filament\Resources\KpiTargets\Schemas\KpiTargetInfolist;
use Modules\KpiEnterprise\Filament\Resources\KpiTargets\Tables\KpiTargetsTable;
use Modules\KpiEnterprise\Models\KpiTarget;

class KpiTargetResource extends ModuleResource
{
    protected static ?string $model = KpiTarget::class;

    public static function form(Schema $schema): Schema
    {
        return KpiTargetForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KpiTargetInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KpiTargetsTable::configure($table);
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
            'index' => ListKpiTargets::route('/'),
            'create' => CreateKpiTarget::route('/create'),
            'view' => ViewKpiTarget::route('/{record}'),
            'edit' => EditKpiTarget::route('/{record}/edit'),
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
