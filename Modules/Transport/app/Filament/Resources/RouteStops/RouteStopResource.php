<?php

namespace Modules\Transport\Filament\Resources\RouteStops;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Transport\Filament\Resources\RouteStops\Pages\CreateRouteStop;
use Modules\Transport\Filament\Resources\RouteStops\Pages\EditRouteStop;
use Modules\Transport\Filament\Resources\RouteStops\Pages\ListRouteStops;
use Modules\Transport\Filament\Resources\RouteStops\Pages\ViewRouteStop;
use Modules\Transport\Filament\Resources\RouteStops\Schemas\RouteStopForm;
use Modules\Transport\Filament\Resources\RouteStops\Schemas\RouteStopInfolist;
use Modules\Transport\Filament\Resources\RouteStops\Tables\RouteStopsTable;
use Modules\Transport\Models\RouteStop;

class RouteStopResource extends ModuleResource
{
    protected static ?string $model = RouteStop::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RouteStopForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RouteStopInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RouteStopsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRouteStops::route('/'),
            'create' => CreateRouteStop::route('/create'),
            'view' => ViewRouteStop::route('/{record}'),
            'edit' => EditRouteStop::route('/{record}/edit'),
        ];
    }
}
