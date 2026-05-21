<?php

namespace Modules\Monitoring\Filament\Resources\AuditLogs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;

class AuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Context')
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('user_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('user_id'))
                            ->relationship('user', 'name'),
                        Select::make('organization_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                    ]),

                Section::make('Audit Target')
                    ->columns(2)
                    ->schema([
                        TextInput::make('auditable_type')
                            ->label(\Modules\Core\Support\FilamentUi::field('auditable_type')),
                        TextInput::make('auditable_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('auditable_id'))
                            ->numeric(),
                        TextInput::make('action')
                            ->label(\Modules\Core\Support\FilamentUi::field('action'))
                            ->required(),
                        TextInput::make('status')
                            ->label(\Modules\Core\Support\FilamentUi::field('status'))
                            ->required()
                            ->default('success'),
                        Textarea::make('description')
                            ->label(\Modules\Core\Support\FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Changed Values')
                    ->columns(2)
                    ->schema([
                        Textarea::make('old_values')
                            ->label(\Modules\Core\Support\FilamentUi::field('old_values'))
                            ->columnSpanFull(),
                        Textarea::make('new_values')
                            ->label(\Modules\Core\Support\FilamentUi::field('new_values'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Request Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('ip_address')
                            ->label(\Modules\Core\Support\FilamentUi::field('ip_address')),
                        TextInput::make('request_id')
                            ->label(\Modules\Core\Support\FilamentUi::field('request_id')),
                        Textarea::make('user_agent')
                            ->label(\Modules\Core\Support\FilamentUi::field('user_agent'))
                            ->columnSpanFull(),
                        Textarea::make('error_message')
                            ->label(\Modules\Core\Support\FilamentUi::field('error_message'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
