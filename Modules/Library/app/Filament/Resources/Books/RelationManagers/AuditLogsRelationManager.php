<?php

namespace Modules\Library\Filament\Resources\Books\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Monitoring\Filament\Resources\AuditLogs\AuditLogResource;

class AuditLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'auditLogs';

    public function form(Schema $schema): Schema
    {
        return AuditLogResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return AuditLogResource::table($table);
    }
}
