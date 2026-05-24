<?php

namespace Modules\Transport\Filament\Resources\Drivers;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Transport\Filament\Resources\Drivers\Pages\CreateDriver;
use Modules\Transport\Filament\Resources\Drivers\Pages\EditDriver;
use Modules\Transport\Filament\Resources\Drivers\Pages\ListDrivers;
use Modules\Transport\Filament\Resources\Drivers\Pages\ViewDriver;
use Modules\Transport\Filament\Resources\Drivers\Schemas\DriverForm;
use Modules\Transport\Filament\Resources\Drivers\Schemas\DriverInfolist;
use Modules\Transport\Filament\Resources\Drivers\Tables\DriversTable;
use Modules\Transport\Models\Driver;

class DriverResource extends ModuleResource
{
    protected static ?string $model = Driver::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DriverForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DriverInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DriversTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDrivers::route('/'),
            'create' => CreateDriver::route('/create'),
            'view' => ViewDriver::route('/{record}'),
            'edit' => EditDriver::route('/{record}/edit'),
        ];
    }
}
