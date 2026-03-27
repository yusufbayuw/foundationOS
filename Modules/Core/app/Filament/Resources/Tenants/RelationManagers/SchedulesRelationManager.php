<?php

namespace Modules\Core\Filament\Resources\Tenants\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\Schedules\ScheduleResource;

class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';

    public function form(Schema $schema): Schema
    {
        return ScheduleResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return ScheduleResource::table($table);
    }
}
