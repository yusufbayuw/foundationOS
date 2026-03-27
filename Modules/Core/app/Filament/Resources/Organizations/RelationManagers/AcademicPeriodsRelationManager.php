<?php

namespace Modules\Core\Filament\Resources\Organizations\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\AcademicPeriods\AcademicPeriodResource;

class AcademicPeriodsRelationManager extends RelationManager
{
    protected static string $relationship = 'academicPeriods';

    public function form(Schema $schema): Schema
    {
        return AcademicPeriodResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return AcademicPeriodResource::table($table);
    }
}
