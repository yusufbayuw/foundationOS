<?php

namespace Modules\Campus\Filament\Resources\StudyPrograms\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\Lecturers\LecturerResource;

class LecturersRelationManager extends RelationManager
{
    protected static string $relationship = 'lecturers';

    public function form(Schema $schema): Schema
    {
        return LecturerResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return LecturerResource::table($table);
    }
}
