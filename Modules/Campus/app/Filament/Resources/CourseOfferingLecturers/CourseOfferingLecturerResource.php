<?php

namespace Modules\Campus\Filament\Resources\CourseOfferingLecturers;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\CourseOfferingLecturers\Pages\CreateCourseOfferingLecturer;
use Modules\Campus\Filament\Resources\CourseOfferingLecturers\Pages\EditCourseOfferingLecturer;
use Modules\Campus\Filament\Resources\CourseOfferingLecturers\Pages\ListCourseOfferingLecturers;
use Modules\Campus\Filament\Resources\CourseOfferingLecturers\Pages\ViewCourseOfferingLecturer;
use Modules\Campus\Filament\Resources\CourseOfferingLecturers\Schemas\CourseOfferingLecturerForm;
use Modules\Campus\Filament\Resources\CourseOfferingLecturers\Schemas\CourseOfferingLecturerInfolist;
use Modules\Campus\Filament\Resources\CourseOfferingLecturers\Tables\CourseOfferingLecturersTable;
use Modules\Campus\Models\CourseOfferingLecturer;
use Modules\Core\Filament\Support\ModuleResource;

class CourseOfferingLecturerResource extends ModuleResource
{
    protected static ?string $model = CourseOfferingLecturer::class;

    public static function form(Schema $schema): Schema
    {
        return CourseOfferingLecturerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CourseOfferingLecturerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseOfferingLecturersTable::configure($table);
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
            'index' => ListCourseOfferingLecturers::route('/'),
            'create' => CreateCourseOfferingLecturer::route('/create'),
            'view' => ViewCourseOfferingLecturer::route('/{record}'),
            'edit' => EditCourseOfferingLecturer::route('/{record}/edit'),
        ];
    }
}
