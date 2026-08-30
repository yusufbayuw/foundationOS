<?php

namespace Modules\Core\Filament\Resources\Stakeholders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Modules\Core\Filament\Support\TenantField;
use Modules\Core\Support\FilamentUi;

class StakeholderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TenantField::make(),
                Select::make('organization_id')
                    ->relationship('organization', 'name'),
                Select::make('user_id')
                    ->relationship('user', 'name'),
                TextInput::make('role_type')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label(FilamentUi::text('Email address'))
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
