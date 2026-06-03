<?php

namespace Modules\Monitoring\Filament\Resources\MoodleSyncOutboxes\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Core\Support\FilamentUi;

class MoodleSyncOutboxInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(FilamentUi::text('Outbox details'))
                    ->schema([
                        TextEntry::make('id')->label(FilamentUi::field('id')),
                        TextEntry::make('tenant_id')->label(FilamentUi::field('tenant_id')),
                        TextEntry::make('entity_type')->label(FilamentUi::field('entity_type')),
                        TextEntry::make('entity_id')->label(FilamentUi::field('entity_id')),
                        TextEntry::make('action')->label(FilamentUi::field('action')),
                        TextEntry::make('status')->label(FilamentUi::field('status')),
                        TextEntry::make('dedupe_key')->label(FilamentUi::field('dedupe_key'))->columnSpanFull(),
                        TextEntry::make('attempts')->label(FilamentUi::field('attempts')),
                        TextEntry::make('next_retry_at')->label(FilamentUi::field('next_retry_at'))->dateTime(),
                        TextEntry::make('synced_at')->label(FilamentUi::field('synced_at'))->dateTime(),
                        TextEntry::make('last_error')->label(FilamentUi::field('last_error'))->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make(FilamentUi::text('Payload'))
                    ->schema([
                        KeyValueEntry::make('payload')
                            ->label(FilamentUi::field('payload'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
