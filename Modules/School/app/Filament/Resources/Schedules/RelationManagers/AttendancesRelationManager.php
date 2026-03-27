<?php

namespace Modules\School\Filament\Resources\Schedules\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\Attendances\AttendanceResource;

class AttendancesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendances';

    public function form(Schema $schema): Schema
    {
        return AttendanceResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return AttendanceResource::table($table);
    }
}
