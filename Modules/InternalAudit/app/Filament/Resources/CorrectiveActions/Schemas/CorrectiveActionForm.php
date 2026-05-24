<?php

namespace Modules\InternalAudit\Filament\Resources\CorrectiveActions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CorrectiveActionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('organization_id')
                    ->relationship('organization', 'name'),
                TextInput::make('code'),
                TextInput::make('name'),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('meta')
                    ->columnSpanFull(),
            ]);
    }
}
