<?php

namespace Modules\Campus\Filament\Resources\FeederLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class FeederLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Log Identity'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tenant.name')
                            ->label(FilamentUi::text('Tenant')),
                        TextEntry::make('organization.name')
                            ->label(FilamentUi::text('Organization'))
                            ->placeholder('-'),
                        TextEntry::make('entity_type')
                            ->label(FilamentUi::field('entity_type')),
                        TextEntry::make('entity_id')
                            ->label(FilamentUi::field('entity_id'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('action')
                            ->label(FilamentUi::field('action')),
                        TextEntry::make('status')
                            ->label(FilamentUi::field('status')),
                        TextEntry::make('synced_by')
                            ->label(FilamentUi::field('synced_by'))
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('synced_at')
                            ->label(FilamentUi::field('synced_at'))
                            ->dateTime()
                            ->placeholder('-'),
                    ]),

                Section::make(FilamentUi::text('Payload & Messages'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('request_payload')
                            ->label(FilamentUi::field('request_payload'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('response_payload')
                            ->label(FilamentUi::field('response_payload'))
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
