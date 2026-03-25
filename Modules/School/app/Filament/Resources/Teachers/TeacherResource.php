<?php

namespace Modules\School\Filament\Resources\Teachers;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\Teachers\Pages\CreateTeacher;
use Modules\School\Filament\Resources\Teachers\Pages\EditTeacher;
use Modules\School\Filament\Resources\Teachers\Pages\ListTeachers;
use Modules\School\Filament\Resources\Teachers\Pages\ViewTeacher;
use Modules\School\Filament\Resources\Teachers\Schemas\TeacherForm;
use Modules\School\Filament\Resources\Teachers\Schemas\TeacherInfolist;
use Modules\School\Filament\Resources\Teachers\Tables\TeachersTable;
use Modules\School\Models\Teacher;

class TeacherResource extends LocalizedResource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TeacherForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TeacherInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeachersTable::configure($table);
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
            'index' => ListTeachers::route('/'),
            'create' => CreateTeacher::route('/create'),
            'view' => ViewTeacher::route('/{record}'),
            'edit' => EditTeacher::route('/{record}/edit'),
        ];
    }
}
