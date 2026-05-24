<?php

namespace Modules\Monitoring\Filament\Resources\WebhookDeliveries\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WebhookDeliveryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('webhook_subscription_id')
                    ->required()
                    ->numeric(),
                TextInput::make('event')
                    ->required(),
                Textarea::make('payload')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                TextInput::make('attempt_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('next_retry_at'),
                TextInput::make('response_status')
                    ->numeric(),
                Textarea::make('response_body')
                    ->columnSpanFull(),
                Textarea::make('error_message')
                    ->columnSpanFull(),
            ]);
    }
}
