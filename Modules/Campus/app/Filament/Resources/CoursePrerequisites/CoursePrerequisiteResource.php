<?php

namespace Modules\Campus\Filament\Resources\CoursePrerequisites;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\CoursePrerequisites\Pages\CreateCoursePrerequisite;
use Modules\Campus\Filament\Resources\CoursePrerequisites\Pages\EditCoursePrerequisite;
use Modules\Campus\Filament\Resources\CoursePrerequisites\Pages\ListCoursePrerequisites;
use Modules\Campus\Filament\Resources\CoursePrerequisites\Pages\ViewCoursePrerequisite;
use Modules\Campus\Filament\Resources\CoursePrerequisites\Schemas\CoursePrerequisiteForm;
use Modules\Campus\Filament\Resources\CoursePrerequisites\Schemas\CoursePrerequisiteInfolist;
use Modules\Campus\Filament\Resources\CoursePrerequisites\Tables\CoursePrerequisitesTable;
use Modules\Campus\Models\CoursePrerequisite;
use Modules\Core\Filament\Support\ModuleResource;

class CoursePrerequisiteResource extends ModuleResource
{
    protected static ?string $model = CoursePrerequisite::class;

    public static function form(Schema $schema): Schema
    {
        return CoursePrerequisiteForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CoursePrerequisiteInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CoursePrerequisitesTable::configure($table);
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
            'index' => ListCoursePrerequisites::route('/'),
            'create' => CreateCoursePrerequisite::route('/create'),
            'view' => ViewCoursePrerequisite::route('/{record}'),
            'edit' => EditCoursePrerequisite::route('/{record}/edit'),
        ];
    }
}
