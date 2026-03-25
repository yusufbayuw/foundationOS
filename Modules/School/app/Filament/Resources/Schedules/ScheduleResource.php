<?php

namespace Modules\School\Filament\Resources\Schedules;

use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\School\Filament\Resources\Schedules\Pages\CreateSchedule;
use Modules\School\Filament\Resources\Schedules\Pages\EditSchedule;
use Modules\School\Filament\Resources\Schedules\Pages\ListSchedules;
use Modules\School\Filament\Resources\Schedules\Pages\ViewSchedule;
use Modules\School\Filament\Resources\Schedules\Schemas\ScheduleForm;
use Modules\School\Filament\Resources\Schedules\Schemas\ScheduleInfolist;
use Modules\School\Filament\Resources\Schedules\Tables\SchedulesTable;
use Modules\School\Models\Schedule;

class ScheduleResource extends LocalizedResource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ScheduleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ScheduleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchedulesTable::configure($table);
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
            'index' => ListSchedules::route('/'),
            'create' => CreateSchedule::route('/create'),
            'view' => ViewSchedule::route('/{record}'),
            'edit' => EditSchedule::route('/{record}/edit'),
        ];
    }
}
