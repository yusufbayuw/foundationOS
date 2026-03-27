<?php

namespace Modules\Campus\Filament\Resources\StudyPlans;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\StudyPlans\Pages\CreateStudyPlan;
use Modules\Campus\Filament\Resources\StudyPlans\Pages\EditStudyPlan;
use Modules\Campus\Filament\Resources\StudyPlans\Pages\ListStudyPlans;
use Modules\Campus\Filament\Resources\StudyPlans\Pages\ViewStudyPlan;
use Modules\Campus\Filament\Resources\StudyPlans\RelationManagers\ItemsRelationManager;
use Modules\Campus\Filament\Resources\StudyPlans\Schemas\StudyPlanForm;
use Modules\Campus\Filament\Resources\StudyPlans\Schemas\StudyPlanInfolist;
use Modules\Campus\Filament\Resources\StudyPlans\Tables\StudyPlansTable;
use Modules\Campus\Models\StudyPlan;

class StudyPlanResource extends LocalizedResource
{
    protected static ?string $model = StudyPlan::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return StudyPlanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudyPlanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudyPlansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudyPlans::route('/'),
            'create' => CreateStudyPlan::route('/create'),
            'view' => ViewStudyPlan::route('/{record}'),
            'edit' => EditStudyPlan::route('/{record}/edit'),
        ];
    }
}
