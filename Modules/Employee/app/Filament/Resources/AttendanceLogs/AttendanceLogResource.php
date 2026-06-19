<?php

namespace Modules\Employee\Filament\Resources\AttendanceLogs;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Employee\Filament\Resources\AttendanceLogs\Pages\CreateAttendanceLog;
use Modules\Employee\Filament\Resources\AttendanceLogs\Pages\EditAttendanceLog;
use Modules\Employee\Filament\Resources\AttendanceLogs\Pages\ListAttendanceLogs;
use Modules\Employee\Filament\Resources\AttendanceLogs\Pages\ViewAttendanceLog;
use Modules\Employee\Filament\Resources\AttendanceLogs\Schemas\AttendanceLogForm;
use Modules\Employee\Filament\Resources\AttendanceLogs\Schemas\AttendanceLogInfolist;
use Modules\Employee\Filament\Resources\AttendanceLogs\Tables\AttendanceLogsTable;
use Modules\Employee\Models\AttendanceLog;

class AttendanceLogResource extends LocalizedResource
{
    protected static ?string $model = AttendanceLog::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AttendanceLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AttendanceLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendanceLogsTable::configure($table);
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
            'index' => ListAttendanceLogs::route('/'),
            'create' => CreateAttendanceLog::route('/create'),
            'view' => ViewAttendanceLog::route('/{record}'),
            'edit' => EditAttendanceLog::route('/{record}/edit'),
        ];
    }
}
