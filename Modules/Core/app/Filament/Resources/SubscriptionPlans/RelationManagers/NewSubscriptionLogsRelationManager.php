<?php

namespace Modules\Core\Filament\Resources\SubscriptionPlans\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\SubscriptionLogs\SubscriptionLogResource;

class NewSubscriptionLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'newSubscriptionLogs';

    public function form(Schema $schema): Schema
    {
        return SubscriptionLogResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return SubscriptionLogResource::table($table);
    }
}
