<?php

namespace Modules\Monitoring\Filament\Resources\WebhookDeliveries\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WebhookDeliveryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('webhook_subscription_id')
                    ->numeric(),
                TextEntry::make('event'),
                TextEntry::make('payload')
                    ->columnSpanFull(),
                TextEntry::make('status'),
                TextEntry::make('attempt_count')
                    ->numeric(),
                TextEntry::make('next_retry_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('response_status')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('response_body')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('error_message')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
