<?php

namespace Modules\Core\Filament\Resources\Organizations\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\AcademicYears\AcademicYearResource;

class AcademicYearsRelationManager extends RelationManager
{
    protected static string $relationship = 'academicYears';

    public function form(Schema $schema): Schema
    {
        return AcademicYearResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return AcademicYearResource::table($table);
    }
}
