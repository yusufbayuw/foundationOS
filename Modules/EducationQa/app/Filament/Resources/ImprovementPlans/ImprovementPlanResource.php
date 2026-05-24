<?php

namespace Modules\EducationQa\Filament\Resources\ImprovementPlans;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\EducationQa\Filament\Resources\ImprovementPlans\Pages\CreateImprovementPlan;
use Modules\EducationQa\Filament\Resources\ImprovementPlans\Pages\EditImprovementPlan;
use Modules\EducationQa\Filament\Resources\ImprovementPlans\Pages\ListImprovementPlans;
use Modules\EducationQa\Filament\Resources\ImprovementPlans\Pages\ViewImprovementPlan;
use Modules\EducationQa\Filament\Resources\ImprovementPlans\Schemas\ImprovementPlanForm;
use Modules\EducationQa\Filament\Resources\ImprovementPlans\Schemas\ImprovementPlanInfolist;
use Modules\EducationQa\Filament\Resources\ImprovementPlans\Tables\ImprovementPlansTable;
use Modules\EducationQa\Models\ImprovementPlan;

class ImprovementPlanResource extends ModuleResource
{
    protected static ?string $model = ImprovementPlan::class;

    public static function form(Schema $schema): Schema
    {
        return ImprovementPlanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ImprovementPlanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ImprovementPlansTable::configure($table);
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
            'index' => ListImprovementPlans::route('/'),
            'create' => CreateImprovementPlan::route('/create'),
            'view' => ViewImprovementPlan::route('/{record}'),
            'edit' => EditImprovementPlan::route('/{record}/edit'),
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
