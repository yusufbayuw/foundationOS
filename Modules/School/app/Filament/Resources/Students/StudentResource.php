<?php

namespace Modules\School\Filament\Resources\Students;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\Students\Pages\CreateStudent;
use Modules\School\Filament\Resources\Students\Pages\EditStudent;
use Modules\School\Filament\Resources\Students\Pages\ListStudents;
use Modules\School\Filament\Resources\Students\Pages\ViewStudent;
use Modules\School\Filament\Resources\Students\Schemas\StudentForm;
use Modules\School\Filament\Resources\Students\Schemas\StudentInfolist;
use Modules\School\Filament\Resources\Students\Tables\StudentsTable;
use Modules\School\Models\Student;

class StudentResource extends LocalizedResource
{
    protected static ?string $model = Student::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
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
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'view' => ViewStudent::route('/{record}'),
            'edit' => EditStudent::route('/{record}/edit'),
        ];
    }
}
