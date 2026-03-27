<?php

namespace Modules\Core\Filament\Resources\Users\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\Attendances\AttendanceResource;

class VerifiedAttendancesRelationManager extends RelationManager
{
    protected static string $relationship = 'verifiedAttendances';

    public function form(Schema $schema): Schema
    {
        return AttendanceResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return AttendanceResource::table($table);
    }
}
