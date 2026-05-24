<?php

namespace Modules\Core\Filament\Resources\Stakeholders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StakeholderForm
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
                Select::make('user_id')
                    ->relationship('user', 'name'),
                TextInput::make('role_type')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone')
                    ->tel(),
                DatePicker::make('term_start'),
                DatePicker::make('term_end'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
