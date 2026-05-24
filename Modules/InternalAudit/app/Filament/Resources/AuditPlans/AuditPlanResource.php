<?php

namespace Modules\InternalAudit\Filament\Resources\AuditPlans;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\InternalAudit\Filament\Resources\AuditPlans\Pages\CreateAuditPlan;
use Modules\InternalAudit\Filament\Resources\AuditPlans\Pages\EditAuditPlan;
use Modules\InternalAudit\Filament\Resources\AuditPlans\Pages\ListAuditPlans;
use Modules\InternalAudit\Filament\Resources\AuditPlans\Pages\ViewAuditPlan;
use Modules\InternalAudit\Filament\Resources\AuditPlans\Schemas\AuditPlanForm;
use Modules\InternalAudit\Filament\Resources\AuditPlans\Schemas\AuditPlanInfolist;
use Modules\InternalAudit\Filament\Resources\AuditPlans\Tables\AuditPlansTable;
use Modules\InternalAudit\Models\AuditPlan;

class AuditPlanResource extends ModuleResource
{
    protected static ?string $model = AuditPlan::class;

    public static function form(Schema $schema): Schema
    {
        return AuditPlanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditPlanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditPlansTable::configure($table);
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
            'index' => ListAuditPlans::route('/'),
            'create' => CreateAuditPlan::route('/create'),
            'view' => ViewAuditPlan::route('/{record}'),
            'edit' => EditAuditPlan::route('/{record}/edit'),
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
