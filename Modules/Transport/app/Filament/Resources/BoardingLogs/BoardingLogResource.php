<?php

namespace Modules\Transport\Filament\Resources\BoardingLogs;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource;
use Modules\Transport\Filament\Resources\BoardingLogs\Pages\CreateBoardingLog;
use Modules\Transport\Filament\Resources\BoardingLogs\Pages\EditBoardingLog;
use Modules\Transport\Filament\Resources\BoardingLogs\Pages\ListBoardingLogs;
use Modules\Transport\Filament\Resources\BoardingLogs\Pages\ViewBoardingLog;
use Modules\Transport\Filament\Resources\BoardingLogs\Schemas\BoardingLogForm;
use Modules\Transport\Filament\Resources\BoardingLogs\Schemas\BoardingLogInfolist;
use Modules\Transport\Filament\Resources\BoardingLogs\Tables\BoardingLogsTable;
use Modules\Transport\Models\BoardingLog;

class BoardingLogResource extends ModuleResource
{
    protected static ?string $model = BoardingLog::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return BoardingLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BoardingLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BoardingLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBoardingLogs::route('/'),
            'create' => CreateBoardingLog::route('/create'),
            'view' => ViewBoardingLog::route('/{record}'),
            'edit' => EditBoardingLog::route('/{record}/edit'),
        ];
    }
}
