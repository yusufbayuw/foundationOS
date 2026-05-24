<?php

namespace Modules\Campus\Filament\Resources\Wisudas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WisudaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('yudisium_id')
                    ->relationship('yudisium', 'id'),
                TextInput::make('name')
                    ->required(),
                DatePicker::make('held_at'),
                TextInput::make('status')
                    ->required()
                    ->default('planned'),
            ]);
    }
}
