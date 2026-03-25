<?php

namespace Modules\School\Filament\Resources\StudentAssessmentAnswers;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\StudentAssessmentAnswers\Pages\CreateStudentAssessmentAnswer;
use Modules\School\Filament\Resources\StudentAssessmentAnswers\Pages\EditStudentAssessmentAnswer;
use Modules\School\Filament\Resources\StudentAssessmentAnswers\Pages\ListStudentAssessmentAnswers;
use Modules\School\Filament\Resources\StudentAssessmentAnswers\Pages\ViewStudentAssessmentAnswer;
use Modules\School\Filament\Resources\StudentAssessmentAnswers\Schemas\StudentAssessmentAnswerForm;
use Modules\School\Filament\Resources\StudentAssessmentAnswers\Schemas\StudentAssessmentAnswerInfolist;
use Modules\School\Filament\Resources\StudentAssessmentAnswers\Tables\StudentAssessmentAnswersTable;
use Modules\School\Models\StudentAssessmentAnswer;

class StudentAssessmentAnswerResource extends LocalizedResource
{
    protected static ?string $model = StudentAssessmentAnswer::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return StudentAssessmentAnswerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentAssessmentAnswerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentAssessmentAnswersTable::configure($table);
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
            'index' => ListStudentAssessmentAnswers::route('/'),
            'create' => CreateStudentAssessmentAnswer::route('/create'),
            'view' => ViewStudentAssessmentAnswer::route('/{record}'),
            'edit' => EditStudentAssessmentAnswer::route('/{record}/edit'),
        ];
    }
}
