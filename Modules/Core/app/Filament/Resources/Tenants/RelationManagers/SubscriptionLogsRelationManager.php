<?php

namespace Modules\Core\Filament\Resources\Tenants\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\SubscriptionLogs\SubscriptionLogResource;

class SubscriptionLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptionLogs';

    public function form(Schema $schema): Schema
    {
        return SubscriptionLogResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return SubscriptionLogResource::table($table);
    }
}
