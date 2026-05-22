<?php

namespace Modules\Monitoring\Filament\Resources\AuditLogs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class AuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Context'))
                    ->columns(2)
                    ->schema([
                        TenantField::make(),
                        Select::make('user_id')
                            ->label(FilamentUi::field('user_id'))
                            ->relationship('user', 'name'),
                        Select::make('organization_id')
                            ->label(FilamentUi::field('organization_id'))
                            ->relationship('organization', 'name'),
                    ]),

                Section::make(FilamentUi::text('Audit Target'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('auditable_type')
                            ->label(FilamentUi::field('auditable_type')),
                        TextInput::make('auditable_id')
                            ->label(FilamentUi::field('auditable_id'))
                            ->numeric(),
                        TextInput::make('action')
                            ->label(FilamentUi::field('action'))
                            ->required(),
                        TextInput::make('status')
                            ->label(FilamentUi::field('status'))
                            ->required()
                            ->default('success'),
                        Textarea::make('description')
                            ->label(FilamentUi::field('description'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Changed Values'))
                    ->columns(2)
                    ->schema([
                        Textarea::make('old_values')
                            ->label(FilamentUi::field('old_values'))
                            ->columnSpanFull(),
                        Textarea::make('new_values')
                            ->label(FilamentUi::field('new_values'))
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Request Details'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('ip_address')
                            ->label(FilamentUi::field('ip_address')),
                        TextInput::make('request_id')
                            ->label(FilamentUi::field('request_id')),
                        Textarea::make('user_agent')
                            ->label(FilamentUi::field('user_agent'))
                            ->columnSpanFull(),
                        Textarea::make('error_message')
                            ->label(FilamentUi::field('error_message'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
