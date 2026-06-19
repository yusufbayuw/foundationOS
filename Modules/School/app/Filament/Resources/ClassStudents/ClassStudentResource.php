<?php

namespace Modules\School\Filament\Resources\ClassStudents;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\School\Filament\Resources\ClassStudents\Pages\CreateClassStudent;
use Modules\School\Filament\Resources\ClassStudents\Pages\EditClassStudent;
use Modules\School\Filament\Resources\ClassStudents\Pages\ListClassStudents;
use Modules\School\Filament\Resources\ClassStudents\Pages\ViewClassStudent;
use Modules\School\Filament\Resources\ClassStudents\RelationManagers\StudentAssessmentAnswersRelationManager;
use Modules\School\Filament\Resources\ClassStudents\Schemas\ClassStudentForm;
use Modules\School\Filament\Resources\ClassStudents\Schemas\ClassStudentInfolist;
use Modules\School\Filament\Resources\ClassStudents\Tables\ClassStudentsTable;
use Modules\School\Models\ClassStudent;

class ClassStudentResource extends LocalizedResource
{
    protected static ?string $model = ClassStudent::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ClassStudentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClassStudentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClassStudentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            StudentAssessmentAnswersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClassStudents::route('/'),
            'create' => CreateClassStudent::route('/create'),
            'view' => ViewClassStudent::route('/{record}'),
            'edit' => EditClassStudent::route('/{record}/edit'),
        ];
    }
}
