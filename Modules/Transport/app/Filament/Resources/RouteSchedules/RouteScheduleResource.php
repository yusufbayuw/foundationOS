<?php

namespace Modules\Transport\Filament\Resources\RouteSchedules;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Transport\Filament\Resources\RouteSchedules\Pages\CreateRouteSchedule;
use Modules\Transport\Filament\Resources\RouteSchedules\Pages\EditRouteSchedule;
use Modules\Transport\Filament\Resources\RouteSchedules\Pages\ListRouteSchedules;
use Modules\Transport\Filament\Resources\RouteSchedules\Pages\ViewRouteSchedule;
use Modules\Transport\Filament\Resources\RouteSchedules\Schemas\RouteScheduleForm;
use Modules\Transport\Filament\Resources\RouteSchedules\Schemas\RouteScheduleInfolist;
use Modules\Transport\Filament\Resources\RouteSchedules\Tables\RouteSchedulesTable;
use Modules\Transport\Models\RouteSchedule;

class RouteScheduleResource extends ModuleResource
{
    protected static ?string $model = RouteSchedule::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RouteScheduleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RouteScheduleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RouteSchedulesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRouteSchedules::route('/'),
            'create' => CreateRouteSchedule::route('/create'),
            'view' => ViewRouteSchedule::route('/{record}'),
            'edit' => EditRouteSchedule::route('/{record}/edit'),
        ];
    }
}
