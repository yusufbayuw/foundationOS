<?php

namespace Modules\Campus\Filament\Resources\FeederLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FeederLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tenant.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Tenant')),
                TextEntry::make('organization.name')
                    ->label(\Modules\Core\Support\FilamentUi::text('Organization'))
                    ->placeholder('-'),
                TextEntry::make('synced_by')
                    ->label(\Modules\Core\Support\FilamentUi::field('synced_by'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('entity_type')
                    ->label(\Modules\Core\Support\FilamentUi::field('entity_type')),
                TextEntry::make('entity_id')
                    ->label(\Modules\Core\Support\FilamentUi::field('entity_id'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('action')
                    ->label(\Modules\Core\Support\FilamentUi::field('action')),
                TextEntry::make('status')
                    ->label(\Modules\Core\Support\FilamentUi::field('status')),
                TextEntry::make('request_payload')
                    ->label(\Modules\Core\Support\FilamentUi::field('request_payload'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('response_payload')
                    ->label(\Modules\Core\Support\FilamentUi::field('response_payload'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('error_message')
                    ->label(\Modules\Core\Support\FilamentUi::field('error_message'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('synced_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('synced_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(\Modules\Core\Support\FilamentUi::field('updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
