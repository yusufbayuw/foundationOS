<?php

namespace Modules\Helpdesk\Filament\Resources\TicketCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TicketCategoryForm
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
                TextInput::make('response_hours')
                    ->required()
                    ->numeric()
                    ->default(4),
                TextInput::make('resolution_hours')
                    ->required()
                    ->numeric()
                    ->default(24),
                TextInput::make('default_assignee_user_id')
                    ->numeric(),
            ]);
    }
}
