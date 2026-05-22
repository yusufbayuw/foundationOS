<?php

namespace Modules\Monitoring\Filament\Resources\AuditLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Context'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant'))
                            ->placeholder('-'),
                        TextEntry::make('user.name')
                            ->label(FilamentUi::text('User'))
                            ->placeholder('-'),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization'))
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Audit Target'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('auditable_type')
                            ->label(FilamentUi::field('auditable_type'))
                            ->placeholder('-'),
                        TextEntry::make('auditable_id')
                            ->label(FilamentUi::field('auditable_id'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('action')
                            ->label(FilamentUi::field('action')),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('description')
                            ->label(FilamentUi::field('description'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Changed Values'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('old_values')
                            ->label(FilamentUi::field('old_values'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('new_values')
                            ->label(FilamentUi::field('new_values'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Request Details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('ip_address')
                            ->label(FilamentUi::field('ip_address'))
                            ->placeholder('-'),
                        TextEntry::make('request_id')
                            ->label(FilamentUi::field('request_id'))
                            ->placeholder('-'),
                        TextEntry::make('user_agent')
                            ->label(FilamentUi::field('user_agent'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('error_message')
                            ->label(FilamentUi::field('error_message'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make(FilamentUi::text('Timestamps'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(FilamentUi::field('created_at'))
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label(FilamentUi::field('updated_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
