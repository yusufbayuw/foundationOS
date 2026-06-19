<?php

namespace Modules\School\Filament\Resources\Assessments;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\School\Filament\Resources\Assessments\Pages\CreateAssessment;
use Modules\School\Filament\Resources\Assessments\Pages\EditAssessment;
use Modules\School\Filament\Resources\Assessments\Pages\ListAssessments;
use Modules\School\Filament\Resources\Assessments\Pages\ViewAssessment;
use Modules\School\Filament\Resources\Assessments\RelationManagers\AssessmentItemsRelationManager;
use Modules\School\Filament\Resources\Assessments\RelationManagers\StudentAssessmentAnswersRelationManager;
use Modules\School\Filament\Resources\Assessments\RelationManagers\StudentGradesRelationManager;
use Modules\School\Filament\Resources\Assessments\Schemas\AssessmentForm;
use Modules\School\Filament\Resources\Assessments\Schemas\AssessmentInfolist;
use Modules\School\Filament\Resources\Assessments\Tables\AssessmentsTable;
use Modules\School\Models\Assessment;

class AssessmentResource extends LocalizedResource
{
    protected static ?string $model = Assessment::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AssessmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssessmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssessmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AssessmentItemsRelationManager::class,
            StudentGradesRelationManager::class,
            StudentAssessmentAnswersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssessments::route('/'),
            'create' => CreateAssessment::route('/create'),
            'view' => ViewAssessment::route('/{record}'),
            'edit' => EditAssessment::route('/{record}/edit'),
        ];
    }
}
