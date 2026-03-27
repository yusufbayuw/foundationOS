<?php

namespace Modules\Campus\Filament\Resources\Courses;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\Courses\Pages\CreateCourse;
use Modules\Campus\Filament\Resources\Courses\Pages\EditCourse;
use Modules\Campus\Filament\Resources\Courses\Pages\ListCourses;
use Modules\Campus\Filament\Resources\Courses\Pages\ViewCourse;
use Modules\Campus\Filament\Resources\Courses\RelationManagers\CourseOfferingsRelationManager;
use Modules\Campus\Filament\Resources\Courses\RelationManagers\StudyPlanItemsRelationManager;
use Modules\Campus\Filament\Resources\Courses\Schemas\CourseForm;
use Modules\Campus\Filament\Resources\Courses\Schemas\CourseInfolist;
use Modules\Campus\Filament\Resources\Courses\Tables\CoursesTable;
use Modules\Campus\Models\Course;

class CourseResource extends LocalizedResource
{
    protected static ?string $model = Course::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CourseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CourseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CoursesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CourseOfferingsRelationManager::class,
            StudyPlanItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourses::route('/'),
            'create' => CreateCourse::route('/create'),
            'view' => ViewCourse::route('/{record}'),
            'edit' => EditCourse::route('/{record}/edit'),
        ];
    }
}
