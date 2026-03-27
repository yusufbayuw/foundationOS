<?php

namespace Modules\Monitoring\Filament\Resources\AuditLogs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class AuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('tenant_id'))
                    ->relationship('tenant', 'name'),
                Select::make('user_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('user_id'))
                    ->relationship('user', 'name'),
                Select::make('organization_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                    ->relationship('organization', 'name'),
                TextInput::make('auditable_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('auditable_type')),
                TextInput::make('auditable_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('auditable_id'))
                    ->numeric(),
                TextInput::make('action')
                    ->label(\Modules\Core\Support\FilamentUi::field('action'))
                    ->required(),
                Textarea::make('description')
                    ->label(\Modules\Core\Support\FilamentUi::field('description'))
                    ->columnSpanFull(),
                Textarea::make('old_values')
                    ->label(\Modules\Core\Support\FilamentUi::field('old_values'))
                    ->columnSpanFull(),
                Textarea::make('new_values')
                    ->label(\Modules\Core\Support\FilamentUi::field('new_values'))
                    ->columnSpanFull(),
                TextInput::make('ip_address')
                    ->label(\Modules\Core\Support\FilamentUi::field('ip_address')),
                Textarea::make('user_agent')
                    ->label(\Modules\Core\Support\FilamentUi::field('user_agent'))
                    ->columnSpanFull(),
                TextInput::make('request_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('request_id')),
                TextInput::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status'))
                    ->required()
                    ->default('success'),
                Textarea::make('error_message')
                    ->label(\Modules\Core\Support\FilamentUi::field('error_message'))
                    ->columnSpanFull(),
            ]);
    }
}
