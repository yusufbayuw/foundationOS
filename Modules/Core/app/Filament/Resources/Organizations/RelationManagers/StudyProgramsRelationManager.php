<?php

namespace Modules\Core\Filament\Resources\Organizations\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\StudyPrograms\StudyProgramResource;

class StudyProgramsRelationManager extends RelationManager
{
    protected static string $relationship = 'studyPrograms';

    public function form(Schema $schema): Schema
    {
        return StudyProgramResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return StudyProgramResource::table($table);
    }
}
