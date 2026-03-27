<?php

namespace Modules\Campus\Filament\Resources\StudyPrograms\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\Courses\CourseResource;

class CoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'courses';

    public function form(Schema $schema): Schema
    {
        return CourseResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return CourseResource::table($table);
    }
}
