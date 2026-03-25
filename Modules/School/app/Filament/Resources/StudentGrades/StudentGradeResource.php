<?php

namespace Modules\School\Filament\Resources\StudentGrades;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\StudentGrades\Pages\CreateStudentGrade;
use Modules\School\Filament\Resources\StudentGrades\Pages\EditStudentGrade;
use Modules\School\Filament\Resources\StudentGrades\Pages\ListStudentGrades;
use Modules\School\Filament\Resources\StudentGrades\Pages\ViewStudentGrade;
use Modules\School\Filament\Resources\StudentGrades\Schemas\StudentGradeForm;
use Modules\School\Filament\Resources\StudentGrades\Schemas\StudentGradeInfolist;
use Modules\School\Filament\Resources\StudentGrades\Tables\StudentGradesTable;
use Modules\School\Models\StudentGrade;

class StudentGradeResource extends LocalizedResource
{
    protected static ?string $model = StudentGrade::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return StudentGradeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentGradeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentGradesTable::configure($table);
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
            'index' => ListStudentGrades::route('/'),
            'create' => CreateStudentGrade::route('/create'),
            'view' => ViewStudentGrade::route('/{record}'),
            'edit' => EditStudentGrade::route('/{record}/edit'),
        ];
    }
}
