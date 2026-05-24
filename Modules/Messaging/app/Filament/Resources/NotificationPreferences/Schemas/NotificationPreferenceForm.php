<?php

namespace Modules\Messaging\Filament\Resources\NotificationPreferences\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NotificationPreferenceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('organization_id')
                    ->relationship('organization', 'name'),
                TextInput::make('code'),
                TextInput::make('name'),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('meta')
                    ->columnSpanFull(),
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('category'),
                Toggle::make('database_enabled')
                    ->required(),
                Toggle::make('mail_enabled')
                    ->required(),
                Toggle::make('whatsapp_enabled')
                    ->required(),
            ]);
    }
}
