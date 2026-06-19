<?php

namespace Modules\Monitoring\Filament\Resources\AuditLogs;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Support\ModuleResource as LocalizedResource;
use Modules\Monitoring\Filament\Resources\AuditLogs\Pages\CreateAuditLog;
use Modules\Monitoring\Filament\Resources\AuditLogs\Pages\EditAuditLog;
use Modules\Monitoring\Filament\Resources\AuditLogs\Pages\ListAuditLogs;
use Modules\Monitoring\Filament\Resources\AuditLogs\Pages\ViewAuditLog;
use Modules\Monitoring\Filament\Resources\AuditLogs\Schemas\AuditLogForm;
use Modules\Monitoring\Filament\Resources\AuditLogs\Schemas\AuditLogInfolist;
use Modules\Monitoring\Filament\Resources\AuditLogs\Tables\AuditLogsTable;
use Modules\Monitoring\Models\AuditLog;

class AuditLogResource extends LocalizedResource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AuditLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditLogsTable::configure($table);
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
            'index' => ListAuditLogs::route('/'),
            'create' => CreateAuditLog::route('/create'),
            'view' => ViewAuditLog::route('/{record}'),
            'edit' => EditAuditLog::route('/{record}/edit'),
        ];
    }
}
