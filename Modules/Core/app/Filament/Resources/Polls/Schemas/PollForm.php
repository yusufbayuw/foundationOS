<?php

namespace Modules\Core\Filament\Resources\Polls\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PollForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                TextInput::make('question')
                    ->required(),
                Textarea::make('options')
                    ->columnSpanFull(),
                DateTimePicker::make('closes_at'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
