<?php

namespace Modules\Global\Filament\Resources\Timezones;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Global\Filament\Resources\Timezones\Pages\CreateTimezone;
use Modules\Global\Filament\Resources\Timezones\Pages\EditTimezone;
use Modules\Global\Filament\Resources\Timezones\Pages\ListTimezones;
use Modules\Global\Filament\Resources\Timezones\Pages\ViewTimezone;
use Modules\Global\Filament\Resources\Timezones\Schemas\TimezoneForm;
use Modules\Global\Filament\Resources\Timezones\Schemas\TimezoneInfolist;
use Modules\Global\Filament\Resources\Timezones\Tables\TimezonesTable;
use Modules\Global\Models\Timezone;

class TimezoneResource extends LocalizedResource
{
    protected static ?string $model = Timezone::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TimezoneForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TimezoneInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TimezonesTable::configure($table);
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
            'index' => ListTimezones::route('/'),
            'create' => CreateTimezone::route('/create'),
            'view' => ViewTimezone::route('/{record}'),
            'edit' => EditTimezone::route('/{record}/edit'),
        ];
    }
}
