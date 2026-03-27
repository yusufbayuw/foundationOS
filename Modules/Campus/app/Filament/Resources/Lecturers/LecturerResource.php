<?php

namespace Modules\Campus\Filament\Resources\Lecturers;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\Lecturers\Pages\CreateLecturer;
use Modules\Campus\Filament\Resources\Lecturers\Pages\EditLecturer;
use Modules\Campus\Filament\Resources\Lecturers\Pages\ListLecturers;
use Modules\Campus\Filament\Resources\Lecturers\Pages\ViewLecturer;
use Modules\Campus\Filament\Resources\Lecturers\RelationManagers\AdviseeStudentsRelationManager;
use Modules\Campus\Filament\Resources\Lecturers\RelationManagers\AdvisedThesesRelationManager;
use Modules\Campus\Filament\Resources\Lecturers\RelationManagers\CourseOfferingsRelationManager;
use Modules\Campus\Filament\Resources\Lecturers\RelationManagers\ExaminedThesesRelationManager;
use Modules\Campus\Filament\Resources\Lecturers\RelationManagers\HeadedStudyProgramsRelationManager;
use Modules\Campus\Filament\Resources\Lecturers\Schemas\LecturerForm;
use Modules\Campus\Filament\Resources\Lecturers\Schemas\LecturerInfolist;
use Modules\Campus\Filament\Resources\Lecturers\Tables\LecturersTable;
use Modules\Campus\Models\Lecturer;

class LecturerResource extends LocalizedResource
{
    protected static ?string $model = Lecturer::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return LecturerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LecturerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LecturersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CourseOfferingsRelationManager::class,
            HeadedStudyProgramsRelationManager::class,
            AdviseeStudentsRelationManager::class,
            AdvisedThesesRelationManager::class,
            ExaminedThesesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLecturers::route('/'),
            'create' => CreateLecturer::route('/create'),
            'view' => ViewLecturer::route('/{record}'),
            'edit' => EditLecturer::route('/{record}/edit'),
        ];
    }
}
