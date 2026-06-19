<?php

namespace Modules\Campus\Filament\Resources\FeederLogs;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Campus\Filament\Resources\FeederLogs\Pages\CreateFeederLog;
use Modules\Campus\Filament\Resources\FeederLogs\Pages\EditFeederLog;
use Modules\Campus\Filament\Resources\FeederLogs\Pages\ListFeederLogs;
use Modules\Campus\Filament\Resources\FeederLogs\Pages\ViewFeederLog;
use Modules\Campus\Filament\Resources\FeederLogs\Schemas\FeederLogForm;
use Modules\Campus\Filament\Resources\FeederLogs\Schemas\FeederLogInfolist;
use Modules\Campus\Filament\Resources\FeederLogs\Tables\FeederLogsTable;
use Modules\Campus\Models\FeederLog;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;

class FeederLogResource extends LocalizedResource
{
    protected static ?string $model = FeederLog::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return FeederLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FeederLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FeederLogsTable::configure($table);
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
            'index' => ListFeederLogs::route('/'),
            'create' => CreateFeederLog::route('/create'),
            'view' => ViewFeederLog::route('/{record}'),
            'edit' => EditFeederLog::route('/{record}/edit'),
        ];
    }
}
