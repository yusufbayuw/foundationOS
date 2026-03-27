<?php

namespace Modules\Core\Filament\Resources\Tenants\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Monitoring\Filament\Resources\AuditLogs\AuditLogResource;

class AuditableLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'auditableLogs';

    public function form(Schema $schema): Schema
    {
        return AuditLogResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return AuditLogResource::table($table);
    }
}
